<?php namespace smart_filter\src\Admin;

use Map\SProductsTableMap;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Util\PropelModelPager;
use SBrandsQuery;
use SCategory;
use SCategoryQuery;
use smart_filter\models\SFilterPattern;
use smart_filter\models\SFilterPatternQuery;
use SPropertiesQuery;
use SPropertyValueQuery;


/**
 * Class DataProvider
 * @package smart_filter\src\Admin
 */
class DataProvider
{

    /**
     * @param $conditions
     * @param $locale
     * @param $page
     * @param $perPage
     * @return PropelModelPager|SFilterPattern[]
     */
    public function getPatterns($conditions, $locale, $page, $perPage) {

        return SFilterPatternQuery::create()
            ->_if($ac = $this->fetch($conditions, 'active'))
            ->filterByActive($ac)
            ->_endif()
            ->joinWithI18n($locale, Criteria::INNER_JOIN)
            ->_if($nm = $this->fetch($conditions, 'name'))
            ->useI18nQuery($locale, null, Criteria::INNER_JOIN)
            ->filterByName('%' . $nm . '%', Criteria::LIKE)
            ->endUse()
            ->_endif()
            ->_if(($id = $this->fetch($conditions, 'id', 0)) > 0)
            ->filterById('%' . $id . '%', Criteria::LIKE)
            ->_endif()
            ->_if(($ci = $this->fetch($conditions, 'category_id', 0)) > 0)
            ->filterByCategoryId($ci)
            ->_endif()
            ->_if($up = $this->fetch($conditions, 'url_pattern'))
            ->filterByUrlPattern('%' . $up . '%', Criteria::LIKE)
            ->_endif()
            ->orderById(Criteria::DESC)
            ->paginate($page, $perPage);

    }

    /**
     * Returns value from array by key or default value
     * @param array $data
     * @param string $key
     * @param bool $default
     * @return bool
     */
    private function fetch($data, $key, $default = false) {

        return array_key_exists($key, $data) ? $data[$key] : $default;

    }

    /**
     * Get all categories used by patterns
     * @param $locale
     * @return ObjectCollection|SCategory[]
     */
    public function getCategoriesJoinPattern($locale) {

        return SCategoryQuery::create()
            ->joinWithI18n($locale, Criteria::INNER_JOIN)
            ->joinSFilterPattern()
            ->find();

    }

    /**
     * Get categories Collection with each level in tree
     * @param $locale
     * @return ObjectCollection
     */
    public function getCategoriesWithLevels($locale) {

        $categories = SCategoryQuery::create()->setComment(__METHOD__)->getTree(0, SCategoryQuery::create()->setComment(__METHOD__)->joinWithI18n($locale, Criteria::INNER_JOIN))->getCollection();

        return $categories;
    }

    /**
     * Provides data for brand|property|[property_value] selects for create/edit pages
     * @param int $categoryId
     * @param string $locale
     * @param null $selectedProperty
     * @param bool $multi
     * @return array
     * @throws PropelException
     */
    public function getSelectsData($categoryId, $locale, $selectedProperty = null, $multi = false) {

        $data = [];
        $data['brands'] = $this->getBrands($categoryId, $locale);
        $data['properties'] = $this->getProperties($categoryId, $locale);
        if ($multi) {
            $all = [
                    'id'    => 'all',
                    'value' => lang('All', 'admin'),
                   ];
            array_unshift($data['brands'], $all);
            array_unshift($data['properties'], $all);

        } else {
            $empty = [
                      'id'    => '',
                      'value' => '-',
                     ];
            array_unshift($data['brands'], $empty);
            array_unshift($data['properties'], $empty);

        }

        if (!$multi && $data['properties']) {
            $data['values'] = $selectedProperty ? $this->getPropertyValues($selectedProperty, $locale) : [];
            if (count($data['values'])) {
                array_unshift($data['values'], ['id' => 0, 'value' => lang('All', 'admin')]);
            }
        }
        $data['index'] = [
                          ''        => '-',
                          'index'   => 'index',
                          'noindex' => 'noindex',
                         ];
        $data['follow'] = [
                           ''         => '-',
                           'follow'   => 'follow',
                           'nofollow' => 'nofollow',
                          ];

        return $data;

    }

    /**
     * Provides property values list
     * @param int $propertyId
     * @param string $locale
     * @return array
     * @throws PropelException
     */
    public function getPropertyValues($propertyId, $locale) {

        $propertyValues = SPropertyValueQuery::create()
            ->select(['id', 'value'])
            ->withColumn('SPropertyValue.Id', 'id')
            ->withColumn('SPropertyValueI18n.Value', 'value')
            ->orderByPosition()
            ->joinWithI18n($locale, Criteria::INNER_JOIN)->filterByPropertyId($propertyId)->find()->toArray();

        return $propertyValues;
    }

    /**
     * Provides properties list
     *
     * @param int $categoryId
     * @param null|string $locale
     * @param null|array $filterIds
     * @return array
     * @throws PropelException
     */
    public function getProperties($categoryId, $locale, $filterIds = null) {

        $properties = SPropertiesQuery::create()
            ->select(['id', 'value', 'url'])
            ->withColumn('SProperties.Id', 'id')
            ->withColumn('SPropertiesI18n.Name', 'value')
            ->withColumn('SProperties.CsvName', 'url')
            ->_if($filterIds)
            ->filterById($filterIds, Criteria::IN)
            ->_endif()
            ->orderByPosition()
            ->joinWithI18n($locale)
            ->useShopProductPropertiesCategoriesQuery()
            ->filterByCategoryId($categoryId)
            ->endUse()
            ->find()
            ->toArray();

        return $properties;
    }

    /**
     * @param array $data
     * @return \SProducts|\SProperties|\SPropertyValue
     */
    public function getProductsCount($data) {

        $count = \SProductsQuery::create()
            ->select('count')
            ->withColumn('COUNT(' . SProductsTableMap::COL_ID . ')', 'count')
            ->_if($bid = $this->fetch($data, 'brand_id'))
            ->filterByBrandId($bid)
            ->_endif()
            ->useSProductPropertiesDataQuery()
            ->_if($pid = $this->fetch($data, 'property_id'))
            ->filterByPropertyId($pid)
            ->_endif()
            ->_if($vid = $this->fetch($data, 'value_id'))
            ->filterByValueId($vid)
            ->_endif()
            ->endUse()
            ->findOne();

        return $count;

    }

    /**
     * Provides brands list
     * @param int $categoryId
     * @param null|string $locale
     * @param null|array $filterIds
     * @return array
     * @throws PropelException
     */
    public function getBrands($categoryId, $locale, $filterIds = null) {

        $brands = SBrandsQuery::create()
            ->select(['id', 'value', 'url'])
            ->withColumn('SBrands.Id', 'id')
            ->withColumn('SBrandsI18n.Name', 'value')
            ->withColumn('SBrands.Url', 'url')
            ->_if($filterIds)
            ->filterById($filterIds, Criteria::IN)
            ->_endif()
            ->orderByPosition()
            ->joinWithI18n($locale)
            ->useSProductsQuery()
            ->useShopProductCategoriesQuery()
            ->filterByCategoryId($categoryId)
            ->endUse()
            ->endUse()
            ->distinct()
            ->find()
            ->toArray();

        return $brands;
    }

}