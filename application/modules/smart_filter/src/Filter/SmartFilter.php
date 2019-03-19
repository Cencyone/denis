<?php namespace smart_filter\src\Filter;

use CI_Controller;
use CI_DB_active_record;
use core\src\UrlParser;
use Doctrine\Common\Cache\CacheProvider;
use MY_Controller;
use ShopCore;
use smart_filter\src\Filter\Filters\DelegationFilter;
use SProductsQuery;
use Symfony\Component\Config\Definition\Exception\Exception;

class SmartFilter
{

    /**
     * @var CI_DB_active_record
     */
    private $db;

    /**
     * @var CI_Controller
     */
    private $ci;

    /**
     * @var FilterParameters
     */
    private $parameters;

    /**
     * @var DelegationFilter
     */
    private $filter;

    /**
     * @var UrlParser
     */
    private $parser;

    /**
     * @param int $categoryId
     * @param array $getParams
     * @param UrlParser $parser
     * @param CacheProvider $cache
     */
    public function __construct($categoryId, $getParams, UrlParser $parser, CacheProvider $cache = null) {
        $this->ci = &get_instance();
        $this->db = $this->ci->db;
        $this->parser = $parser;

        $this->parameters = new FilterParameters($getParams, $categoryId, MY_Controller::getCurrentLocale());
        $this->formGetFromPhysicalUrl();
        $this->filter = new DelegationFilter($this->parameters, $this->db, $cache);
        $this->filter->combine();

    }

    /**
     * Applying all filter conditions to query
     * @param SProductsQuery $query
     */
    public function applyFilterConditions(SProductsQuery $query) {

        $this->filter->filter($query);
    }

    /**
     * returns array of stdClass brands objects
     * @return null|array
     */
    public function getBrands() {
        return $this->filter->getBrands();
    }

    /**
     * Get all possible properties and values array for each property
     * with number of products for each value
     *
     * @return \stdClass[]
     */
    public function getProperties() {
        return $this->filter->getProperties();
    }

    /**
     * Array with min and max price
     *
     * @return array [minCost => int, maxCost => int]
     */
    public function getPriceRange() {
        return $this->filter->getPriceRange();
    }

    /**
     * @return array
     */
    public function getSelectedProperties() {
        return $this->filter->getSelectedProperties();
    }

    /**
     * @return array
     */
    public function getSelectedBrands() {
        return $this->filter->getSelectedBrands();
    }

    /**
     * Physical pages support
     */
    private function formGetFromPhysicalUrl() {

        if (!$this->parser->brandSegmentIsFirst()) {
            throw new Exception('Unknown url segment');
        }

        if ($brands = $this->parser->getBrands()) {
            $brandIds = $this->fetchBrands($brands);
            $this->parameters->setBrands($brandIds);
        }

        if ($getProperties = $this->parser->getProperties()) {
            $propertyIds = $this->fetchProperties($getProperties);
            $this->parameters->setPropertyValueIds($propertyIds);
        }

        $this->modifyGetParameters();
    }

    /**
     * @param array $getBrands
     * @return array
     */
    private function fetchBrands($getBrands) {
        $brandUrls = [];
        /** @var \CI_DB_mysqli_result $query */
        $query = $this->db->select('id, url')->where_in('url', $getBrands)
            ->order_by('url')
            ->get('shop_brands');
        if ($query->num_rows()) {
            $dbBrands = $query->result_array();
            $brandUrls = array_column($dbBrands, 'url');
            $brandIds = array_column($dbBrands, 'id');
        }

        if ($getBrands !== $brandUrls) {
            throw new Exception('Unknown url segment');
        }

        return $brandIds;
    }

    /**
     * @param array $getProperties
     * @return array
     */
    private function fetchProperties($getProperties) {
        $getValueIds = [];
        foreach ($getProperties as $values) {
            $getValueIds = array_merge($getValueIds, $values);
        }

        /** @var \CI_DB_mysqli_result $query */
        $query = $this->db->select('shop_product_properties.id, csv_name, shop_product_property_value.id as value_id')
            ->join('shop_product_property_value', 'shop_product_property_value.property_id = shop_product_properties.id')
            ->where_in('csv_name', array_keys($getProperties))
            ->where_in('shop_product_property_value.id', $getValueIds)
            ->order_by('shop_product_properties.id, shop_product_property_value.id')
            ->get('shop_product_properties');

        $dbProperties = [];
        if ($query->num_rows()) {
            $propertyIds = [];
            $properties = $query->result_array();

            foreach ($properties as $property) {
                $dbProperties[$property['csv_name']][] = $property['value_id'];
                $propertyIds[$property['id']][] = $property['value_id'];
            }

        }

        if ($dbProperties !== $getProperties) {
            throw new Exception('Unknown url segment');
        }

        return $propertyIds;
    }

    private function modifyGetParameters() {
        $_GET = array_replace($_GET, $this->parameters->toArray());
        ShopCore::$_GET = array_replace(ShopCore::$_GET ?: $_GET, $this->parameters->toArray());
    }

}