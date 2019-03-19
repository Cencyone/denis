<?php

namespace smart_filter\src\Filter\Filters;

use CI_DB_active_record;
use Doctrine\Common\Cache\CacheProvider;
use Propel\Runtime\ActiveQuery\Criteria;
use smart_filter\src\Filter\FilterParameters;

class DelegationFilter
{

    private $cache;

    /**
     * @var PriceFilter
     */
    private $priceFilter;

    /**
     * @var FilterParameters
     */
    private $parameters;

    /**
     * @var BrandsFilter
     */
    private $brandsFilter;

    /**
     * @var PropertiesFilter
     */
    private $propertiesFilter;

    /**
     * @var array|null
     */
    private $productIdsByPrice;

    /**
     * @var array
     */
    private $properties;

    /**
     * @var array
     */
    private $brands;

    /**
     * @var array
     */
    private $prices;

    /**
     * @var array
     */
    private $propertiesSelectedInFilter;

    /**
     * @var array
     */
    private $brandsSelectedInFilter;

    /**
     * @var array
     */
    private $productIdsByBrands;

    /**
     * @var array
     */
    private $productIdsByProperties;

    /**
     * ValuesCombiner constructor.
     * @param FilterParameters $params
     * @param CI_DB_active_record $db
     * @param CacheProvider $cache
     */
    public function __construct(FilterParameters $params, CI_DB_active_record $db, CacheProvider $cache = null) {

        $this->cache = $cache;
        $this->parameters = $params;
        $this->brandsFilter = new BrandsFilter($params, $db);
        $this->priceFilter = new PriceFilter($params, $db);
        $this->propertiesFilter = new PropertiesFilter($params, $db);
    }

    private function initCached() {
        $tag = $this->parameters->getLocale() . ':' . $this->parameters->getCategoryId();
        $propertiesTag = 'properties:' . $tag;
        $brandsTag = 'brands:' . $tag;
        $pricesTag = 'prices:' . $tag;

        if ($this->cache->contains($propertiesTag)) {
            $this->properties = $this->cache->fetch($propertiesTag);
        } else {
            $this->properties = $this->propertiesFilter->getValues();
            $this->cache->save($propertiesTag, $this->properties);
        }
        if ($this->cache->contains($brandsTag)) {
            $this->brands = $this->cache->fetch($brandsTag);

        } else {
            $this->brands = $this->brandsFilter->getValues();
            $this->cache->save($brandsTag, $this->brands);
        }

        if ($this->cache->contains($pricesTag)) {
            $this->prices = $this->cache->fetch($pricesTag);
        } else {
            $this->prices = $this->priceFilter->getValues();
            $this->cache->save($pricesTag, $this->prices);
        }

    }

    private function init() {
        $this->properties = $this->propertiesFilter->getValues();
        $this->brands = $this->brandsFilter->getValues();
        $this->prices = $this->priceFilter->getValues();

    }

    private function getValues() {

        if ($this->cache) {
            $this->initCached();
        } else {
            $this->init();
        }

        $this->productIdsByPrice = $this->priceFilter->getSelectedInFilterVariants();

        if ($this->properties) {
            $this->propertiesSelectedInFilter = $this->propertiesFilter->getSelectedInFilterVariants($this->properties);
        }
        if ($this->brands) {
            $this->brandsSelectedInFilter = $this->brandsFilter->getSelectedInFilterVariants($this->brands);
        }

        if ($this->brandsSelectedInFilter) {
            $this->productIdsByBrands = $this->brandsFilter->fetchProductIds($this->brandsSelectedInFilter);
        }
        if ($this->propertiesSelectedInFilter) {
            $this->productIdsByProperties = $this->propertiesFilter->fetchProductIds($this->propertiesSelectedInFilter);
        }
    }

    public function combine() {
        //all values
        $this->getValues();

        //change products count depending from selected values in filter
        $this->combineProperties();
        $this->combineBrands();
    }

    private function combineBrands() {
        if ($this->brands) {
            $brandIntersectProducts = $this->getIntersectProducts($this->productIdsByPrice, $this->productIdsByProperties);
            if (is_array($brandIntersectProducts)) {
                $this->brands = $this->brandsFilter->recount($this->brands, $brandIntersectProducts);
            }
        }
    }

    private function combineProperties() {
        if ($this->properties) {
            //change products count depending from selected values in filter
            $this->properties = $this->propertiesFilter->recount(
                $this->properties,
                $this->propertiesSelectedInFilter,
                $this->getIntersectProducts($this->productIdsByPrice, $this->productIdsByBrands)
            );
        }
    }

    public function filter(\SProductsQuery $query) {
        //ids of products for database query
        $filterIds = $this->getIntersectProducts(
            $this->productIdsByPrice,
            $this->getIntersectProducts($this->productIdsByProperties, $this->productIdsByBrands)
        );

        if (is_array($filterIds)) {
            $query->filterById($filterIds, Criteria::IN);
        }
    }

    /**
     * @param array $products1
     * @param array $products2
     * @return array
     */
    private function getIntersectProducts($products1, $products2) {
        if (is_array($products1) && is_array($products2)) {
            return array_intersect($products1, $products2);
        } elseif (is_array($products1)) {
            return $products1;
        } elseif (is_array($products2)) {
            return $products2;
        }
    }

    /**
     * @return array
     */
    public function getProperties() {
        return $this->properties;
    }

    /**
     * @return array
     */
    public function getBrands() {
        return $this->brands;
    }

    /**
     * @return array
     */
    public function getSelectedProperties() {
        return $this->propertiesSelectedInFilter;
    }

    /**
     * @return array
     */
    public function getSelectedBrands() {
        return $this->brandsSelectedInFilter;
    }

    /**
     * @return array|bool|mixed|null
     */
    public function getPriceRange() {
        return $this->prices;
    }

}