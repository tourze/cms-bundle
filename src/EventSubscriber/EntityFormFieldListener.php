<?php

namespace CmsBundle\EventSubscriber;

use AntdCpBundle\Event\CreateRecordEvent;
use AntdCpBundle\Event\LinkageFormRequestEvent;
use AntdCpBundle\Event\ModelRowFormatEvent;
use AntdCpBundle\Event\ModifyRecordEvent;
use CmsBundle\Entity\Attribute;
use CmsBundle\Entity\Entity;
use CmsBundle\Entity\Value;
use CmsBundle\Enum\FieldType;
use CmsBundle\Repository\AttributeRepository;
use CmsBundle\Repository\EntityRepository;
use CmsBundle\Repository\ModelRepository;
use CmsBundle\Repository\ValueRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Tourze\JsonRPC\Core\Exception\ApiException;

class EntityFormFieldListener
{
    final public const PREFIX = '__cms_attr__';

    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly EntityRepository $entityRepository,
        private readonly ModelRepository $modelRepository,
        private readonly AttributeRepository $attributeRepository,
        private readonly ValueRepository $valueRepository,
        private readonly PropertyAccessor $propertyAccessor,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[AsEventListener]
    public function onLinkageRequest(LinkageFormRequestEvent $event): void
    {
        //        $this->logger->debug("识别是否是CMS实体数据", [
        //            'event' => $event,
        //        ]);

        if (Entity::class !== $event->getModelClass()) {
            return;
        }

        $model = null;
        if (!$model && $event->getRecord() && isset($event->getRecord()['id'])) {
            $entity = $this->entityRepository->find($event->getRecord()['id']);
            if ($entity) {
                $model = $entity->getModel();
            }
        }

        if (!$model && $event->getForm() && isset($event->getForm()['model|Doctrine\ORM\Mapping\ManyToOne'])) {
            $_id = is_array($event->getForm()['model|Doctrine\ORM\Mapping\ManyToOne'])
                ? ($event->getForm()['model|Doctrine\ORM\Mapping\ManyToOne']['value'] ?? 0)
                : $event->getForm()['model|Doctrine\ORM\Mapping\ManyToOne'];
            if ($_id > 0) {
                $model = $this->modelRepository->find($_id);
            }
        }

        if (!$model) {
            return;
        }

        $fields = [];
        foreach ($model->getSortedAttributes() as $attribute) {
            $tmp = FieldType::getEditObjectMap($attribute, $this->entityManager);
            if (!$tmp) {
                $this->logger->warning("找不到字段[{$attribute->getName()}]的编辑配置", [
                    'attribute' => $attribute,
                ]);
                continue;
            }
            $this->logger->debug("CMS联动字段[{$attribute->getName()}]对应得到编辑字段" . $tmp::class, [
                'attribute' => $attribute,
                'editField' => $tmp,
            ]);

            $tmp->setLabel($attribute->getTitle());
            $tmp->setSpan($attribute->getSpan() ?: 24);
            $tmp->setId($this->genAttributeName($attribute));
            $fields[] = $tmp;
        }

        $event->setFields($fields);
    }

    #[AsEventListener]
    public function appendAttributeData(ModelRowFormatEvent $event): void
    {
        $model = $event->getRow();
        if (!($model instanceof Entity)) {
            return;
        }

        $result = $event->getResult();
        foreach ($model->getValueList() as $value) {
            $key = $this->genAttributeName($value->getAttribute());
            $data = $value->getData();
            if (in_array($value->getAttribute()->getType(), [
                FieldType::SINGLE_IMAGE,
                FieldType::MULTIPLE_IMAGE,
                FieldType::SINGLE_FILE,
            ])) {
                $data = json_decode($data, true);
            }

            if (in_array($value->getAttribute()->getType(), [FieldType::MULTIPLE_SELECT, FieldType::TAGS_SELECT])) {
                $data = explode(',', (string) $data);
            }

            $result[$key] = $data;
        }

        $event->setResult($result);
    }

    #[AsEventListener(event: ModifyRecordEvent::class)]
    #[AsEventListener(event: CreateRecordEvent::class)]
    public function saveAttributeForm(ModifyRecordEvent|CreateRecordEvent $event): void
    {
        $entity = $event->getModel();
        if (!($entity instanceof Entity)) {
            return;
        }

        $form = $event->getForm();
        $values = [];
        foreach ($form as $k => $v) {
            if (!str_starts_with($k, self::PREFIX)) {
                continue;
            }

            $attrId = str_replace(self::PREFIX, '', $k);
            $attribute = $this->attributeRepository->findOneBy([
                'model' => $entity->getModel(),
                'id' => $attrId,
            ]);
            if (!$attribute) {
                $this->logger->warning('找不到Attribute', [
                    'model' => $entity->getModel(),
                    'id' => $attrId,
                ]);
                continue;
            }

            // 富文本，有可能会提交一个空的 p 标签过来，需要特殊处理
            if (FieldType::RICH_TEXT === $attribute->getType()) {
                if ('<p></p>' === $v) {
                    $v = '';
                }
                if (empty($v) && $attribute->getRequired()) {
                    throw new ApiException($attribute->getTitle() . '不能为空');
                }
            }

            $value = null;
            if ($entity->getId()) {
                $value = $this->valueRepository->findOneBy([
                    'entity' => $entity,
                    'model' => $entity->getModel(),
                    'attribute' => $attribute,
                ]);
            } else {
                $value = new Value();
                $value->setEntity($entity);
                $value->setModel($entity->getModel());
                $value->setAttribute($attribute);
            }

            if (!$value) {
                $value = new Value();
                $value->setModel($entity->getModel());
                $value->setAttribute($attribute);
                $value->setEntity($entity);
            }

            $value->setRawData([
                'v' => $v,
            ]);
            $value->setData(FieldType::getStorableValue($attribute, $v));
            $values[] = $value;
        }

        $this->logger->debug('准备修改values', [
            'entity' => $entity,
            'values' => $values,
            'form' => $form,
        ]);
        $this->propertyAccessor->setValue($entity, 'valueList', $values);
    }

    private function genAttributeName(Attribute $attribute): string
    {
        return self::PREFIX . $attribute->getId();
    }
}
