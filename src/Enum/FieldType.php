<?php

namespace CmsBundle\Enum;

use AntdCpBundle\Builder\Field\BaseField;
use AntdCpBundle\Builder\Field\BraftEditor;
use AntdCpBundle\Builder\Field\FileSelectField;
use AntdCpBundle\Builder\Field\FileUpload;
use AntdCpBundle\Builder\Field\InputNumberField;
use AntdCpBundle\Builder\Field\InputTextField;
use AntdCpBundle\Builder\Field\LongTextField;
use AntdCpBundle\Builder\Field\SelectField;
use AntdCpBundle\Builder\Field\TimestampPickerField;
use Carbon\Carbon;
use CmsBundle\Entity\Attribute;
use Doctrine\ORM\EntityManagerInterface;
use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;
use Yiisoft\Json\Json;

enum FieldType: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case DATE = 'date';
    case DATE_TIME = 'date-time';
    case DECIMAL = 'decimal';
    case INTEGER = 'integer';
    case STRING = 'string';
    case TEXT = 'text';
    case RICH_TEXT = 'rich-text';
    case SINGLE_IMAGE = 'single-image';
    case MULTIPLE_IMAGE = 'multiple-image';
    case SINGLE_FILE = 'single-file';
    case SINGLE_SELECT = 'single-select';
    case MULTIPLE_SELECT = 'multiple-select';
    case TAGS_SELECT = 'tags-select';
    case FORMULA = 'formula';

    public function getLabel(): string
    {
        return match ($this) {
            self::DATE => '日期',
            self::DATE_TIME => '日期+时间',
            self::DECIMAL => '小数',
            self::INTEGER => '整数',
            self::STRING => '字符串',
            self::TEXT => '长文本',
            self::RICH_TEXT => '富文本',
            self::SINGLE_IMAGE => '单图',
            self::MULTIPLE_IMAGE => '多图',
            self::SINGLE_FILE => '单文件',
            self::SINGLE_SELECT => '下拉单选',
            self::MULTIPLE_SELECT => '下拉多选',
            self::TAGS_SELECT => '下拉多选(可自由添加)',
            self::FORMULA => '公式',
        };
    }

    public static function getStorableValue(Attribute $attribute, mixed $data): string
    {
        switch ($attribute->getType()) {
            case self::DATE:
                if (is_integer($data)) {
                    $data = Carbon::createFromTimestamp($data, date_default_timezone_get())->format('Y-m-d');
                }

                break;
            case self::DATE_TIME:
                if (is_integer($data)) {
                    $data = Carbon::createFromTimestamp($data, date_default_timezone_get())->format('Y-m-d H:i:s');
                }

                break;
            case self::SINGLE_FILE:
            case self::SINGLE_IMAGE:
            case self::MULTIPLE_IMAGE:
                $data = Json::encode($data);
                break;
            case self::DECIMAL:
            case self::INTEGER:
                $data = (string) $data;
                break;
            case self::MULTIPLE_SELECT:
            case self::TAGS_SELECT:
                if (is_array($data)) {
                    $data = implode(',', $data);
                }
        }

        return (string) $data;
    }

    public static function getEditObjectMap(Attribute $attribute, EntityManagerInterface $entityManager): ?BaseField
    {
        $eavField = null;
        switch ($attribute->getType()) {
            case self::DATE:
                $eavField = TimestampPickerField::gen()
                    ->setInputProps([
                        'showTime' => false,
                        'format' => 'YYYY-MM-DD',
                    ]);
                break;
            case self::DATE_TIME:
                $eavField = TimestampPickerField::gen();
                break;
            case self::DECIMAL:
            case self::INTEGER:
                $eavField = InputNumberField::gen();
                break;
            case self::STRING:
                $eavField = InputTextField::gen();
                break;
            case self::TEXT:
            case self::FORMULA:
                $eavField = LongTextField::gen();
                break;
            case self::RICH_TEXT:
                $eavField = BraftEditor::gen();
                break;
            case self::SINGLE_IMAGE:
                $eavField = FileSelectField::gen();
                $inputProps = $eavField->getInputProps();
                if ($attribute->getPlaceholder()) {
                    $inputProps['uploadButtonText'] = $attribute->getPlaceholder();
                }
                $eavField->setInputProps($inputProps);
                break;
            case self::MULTIPLE_IMAGE:
                $eavField = FileSelectField::gen()
                    ->setInputProps([
                        'limit' => $attribute->getLength(),
                    ]);
                break;
            case self::SINGLE_FILE:
                $eavField = FileUpload::gen()
                    ->setInputProps([
                        'limit' => 1,
                        'uploadProps' => [
                            'multiple' => false,
                        ],
                    ]);
                break;
            case self::SINGLE_SELECT:
                $options = $attribute->genSelectOptions($entityManager);
                $eavField = SelectField::gen()
                    ->setInputProps([
                        'defaultValue' => $attribute->getDefaultValue(),
                        'options' => $options,
                        // 'placeholder' => '如果填写SQL，列必须带有label和value',
                        'style' => [
                            'width' => '100%',
                        ],
                    ]);
                break;
            case self::MULTIPLE_SELECT:
                $options = $attribute->genSelectOptions($entityManager);
                $eavField = SelectField::gen()
                    ->setInputProps([
                        'options' => $options,
                        'showSearch' => true,
                        'mode' => 'multiple',
                        'style' => [
                            'width' => '100%',
                        ],
                    ]);
                break;
            case self::TAGS_SELECT:
                $options = $attribute->genSelectOptions($entityManager);
                $eavField = SelectField::gen()
                    ->setInputProps([
                        'options' => $options,
                        'showSearch' => true,
                        'mode' => 'tags',
                        'tokenSeparators' => [','],
                        'style' => [
                            'width' => '100%',
                        ],
                    ]);
                break;
        }

        if ($attribute->getRequired()) {
            $eavField->setRules([['required' => true, 'message' => "请填写/选择{$attribute->getTitle()}"]]);
        }

        return $eavField;
    }
}
