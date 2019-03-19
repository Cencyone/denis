<?php

namespace CustomFields;

use CustomFieldsDataQuery;
use Exception;
use Map\CustomFieldsTableMap;
use MY_Controller;
use Propel\Runtime\ActiveQuery\Criteria;

/**
 * Class CustomFields
 * @package CustomFields
 */
class CustomFields
{

    /**
     * @var CustomFields
     */
    private static $_instance;

    /**
     * @var bool
     */
    private $isExecuted = false;

    /**
     * @var string product|brand|category
     */
    private $entity;

    /**
     * @var array
     */
    private $fields;

    /**
     * CustomFields constructor.
     */
    protected function __construct() {

    }

    /**
     * @return CustomFields
     */
    static public function getInstance() {

        if (null === self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * @param string|null $fieldName
     * @param int|null $entityId
     * @return array|string
     */
    public function getFields($entityId = null, $fieldName = null) {

        if (!$this->fields) {
            $this->getCustomFields();
        }
        if ($entityId && !$fieldName) {
            return $this->fields[$entityId];
        }
        return $this->fields[$entityId][$fieldName];
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function getCustomFields() {

        $fields = CustomFieldsDataQuery::create()
            ->withColumn(CustomFieldsTableMap::COL_FIELD_NAME, 'name')
            ->filterBydata('', Criteria::NOT_EQUAL)
            ->filterByLocale(MY_Controller::getCurrentLocale())
            ->useCustomFieldsQuery(null, Criteria::LEFT_JOIN)
            ->filterByEntity($this->getEntity())
            ->filterByIsActive(1)
            ->endUse()
            ->find()
            ->toArray();
        foreach ($fields as $field) {
            $this->setFields($field['entityId'], $field['name'], $field['data']);
        }

        $this->isExecuted = true;

        return $this;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getEntity() {

        if ($this->entity === null) {
            throw new Exception('Entity not setted');
        }

        return $this->entity;
    }

    /**
     * @param string $entity
     * @return $this
     */
    public function setEntity($entity) {

        $this->entity = $entity;
        return $this;
    }

    /**
     * @param string $key1
     * @param string $key2
     * @param string $value
     */
    private function setFields($key1, $key2, $value) {

        $this->fields[$key1][$key2] = $value;
    }

    protected function __clone() {

    }

}