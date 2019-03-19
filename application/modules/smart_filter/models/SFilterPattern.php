<?php

namespace smart_filter\models;

use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;
use SCategory;
use SCategoryQuery;
use smart_filter\models\Base\SFilterPattern as BaseSFilterPattern;

/**
 * Skeleton subclass for representing a row from the 'smart_filter_patterns' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class SFilterPattern extends BaseSFilterPattern
{

    /**
     * @param ConnectionInterface|null $con
     * @return bool
     */
    public function preSave(ConnectionInterface $con = null) {

        if ($this->isNew()) {
            $this->setCreated(time());
        }
        $this->setUpdated(time());
        return parent::preSave($con);
    }

    /**
     * @return string
     */
    public function getFullUrl() {
        return '/' . $this->getCategory()->getRouteUrl() . '/' . $this->getUrlPattern();
    }

    /**
     * @return SCategory
     */
    public function getCategory() {
        return SCategoryQuery::create()->findOneById($this->getCategoryId());
    }

    /**
     * @return null|string
     * @deprecated
     */
    public function getDataCategoryUrl() {

        $data = $this->getData();
        return isset($data['category_url']) ? $data['category_url'] : null;
    }

    /**
     * @return null|int
     */
    public function getDataBrandId() {

        $data = $this->getData();
        return isset($data['brand_id']) ? $data['brand_id'] : null;
    }

    /**
     * @return null|int
     */
    public function getDataPropertyId() {

        $data = $this->getData();
        return isset($data['property_id']) ? $data['property_id'] : null;

    }

    /**
     * @return null|int
     */
    public function getDataPropertyValueId() {

        $data = $this->getData();
        return isset($data['value_id']) ? $data['value_id'] : null;
    }

    /**
     * @return mixed
     */
    public function getData() {

        return json_decode(parent::getData(), true);

    }

    /**
     * @return string
     * @throws PropelException
     */
    public function getMetaRobots() {
        return trim($this->getMetaIndex() . ', ' . $this->getMetaFollow(), ', ');
    }

    /**
     * @return bool
     */
    public function hasValuesSubstitution() {
        return substr($this->getUrlPattern(), -1) === '*';
    }

    /**
     * @param string|array $data
     * @return $this|SFilterPattern
     */
    public function setData($data) {

        if (is_array($data)) {
            $data = json_encode($data);
        }
        return parent::setData($data);
    }

}