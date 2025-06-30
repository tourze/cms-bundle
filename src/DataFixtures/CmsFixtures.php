<?php

namespace CmsBundle\DataFixtures;

use Carbon\CarbonImmutable;
use CmsBundle\Entity\Attribute;
use CmsBundle\Entity\Category;
use CmsBundle\Entity\Entity;
use CmsBundle\Entity\Model;
use CmsBundle\Entity\Topic;
use CmsBundle\Entity\Value;
use CmsBundle\Enum\EntityState;
use CmsBundle\Enum\FieldType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CmsFixtures extends Fixture
{

    public function load(ObjectManager $manager): void
    {
        $topic = new Topic();
        $topic->setTitle('运动专题');
        $topic->setDescription('这是一个关于运动相关的话题');
        $topic->setRecommend(true);
        $topic->setCreateTime(CarbonImmutable::now());
        $topic->setUpdateTime(CarbonImmutable::now());
        $manager->persist($topic);
        $this->addReference('topic-sports', $topic);

        $category = new Category();
        $category->setTitle('运动');
        $category->setDescription('运动');
        $category->setValid(true);
        $category->setCreateTime(CarbonImmutable::now());
        $category->setSortNumber(0);
        $category->setUpdateTime(CarbonImmutable::now());
        $manager->persist($category);
        $this->addReference('category-sports', $category);

        $array = ['篮球', '足球', '羽毛球', '乒乓球', '排球'];
        foreach ($array as $value) {
            $category2 = new Category();
            $category2->setTitle($value);
            $category2->setDescription($value);
            $category2->setParent($category);
            $category2->setValid(true);
            $category2->setCreateTime(CarbonImmutable::now());
            $category2->setUpdateTime(CarbonImmutable::now());
            $category2->setSortNumber(0);
            $manager->persist($category2);
        }

        $model = new Model();
        $model->setValid(true);
        $model->setTitle('娱乐文章');
        $model->setCode('娱乐文章');
        $model->setSortNumber(0);
        $model->setAllowLike(true);
        $model->setAllowCollect(true);
        $model->setAllowShare(true);
        $model->setCreateTime(CarbonImmutable::now());
        $model->setUpdateTime(CarbonImmutable::now());
        $manager->persist($model);
        $this->addReference('model-entertainment', $model);

        $attribute = new Attribute();
        $attribute->setModel($model);
        $attribute->setType(FieldType::RICH_TEXT);
        $attribute->setTitle('内容');
        $attribute->setValid(true);
        $attribute->setSearchable(true);
        $attribute->setDisplayOrder(2);
        $attribute->setName('content');
        $attribute->setSpan(24);
        $attribute->setRequired(true);
        $attribute->setCreateTime(CarbonImmutable::now());
        $attribute->setUpdateTime(CarbonImmutable::now());
        $manager->persist($attribute);
        $this->addReference('attribute-content', $attribute);

        foreach ($this->getArticleData() as [$title, $remark,$content]) {
            $article = new Entity();
            $article->setTitle($title);
            $article->setRemark($remark);
            $article->setPublishTime(CarbonImmutable::now());
            $article->setEndTime(CarbonImmutable::now()->addDay());
            $article->setModel($model);
            $article->setState(EntityState::PUBLISHED);
            $article->addCategory($category);
            $article->addTopic($topic);
            $manager->persist($article);

            $value = new Value();
            $value->setModel($model);
            $value->setEntity($article);
            $value->setAttribute($attribute);
            $value->setCreateTime(CarbonImmutable::now());
            $value->setUpdateTime(CarbonImmutable::now());
            $value->setData($content);
            $value->setRawData([
                'v' => $content,
            ]);
            $manager->persist($value);
        }
        $manager->flush();
    }

    /**
     * @return array<array{string, string, string}>
     */
    public function getArticleData(): array
    {
        return [
            ['篮球的魅力', '篮球的魅力', '<p>篮球（basketball），是以手为中心的身体对抗性体育运动，是奥运会核心比赛项目。1891年12月21日，由美国马萨诸塞州斯普林菲尔德基督教青年会训练学校体育教师詹姆士·奈史密斯发明。1896年，篮球运动传入中国天津。</p>'],
            ['足球的魅力', '足球的魅力', '<p>足球（Football[英]、 Soccer[美]）是一项以脚为主，控制和支配球，两支球队按照一定规则在同一块长方形球场上互相进行进攻、防守对抗的体育运动项目。因足球运动对抗性强、战术多变、参与人数多等特点，故被称为“世界第一运动”。</p>'],
        ];
    }
}
