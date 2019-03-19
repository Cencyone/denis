<?php

namespace exchange\classes;

use CI_DB_active_record;
use CI_DB_result;
use Exception;
use MY_Controller;
use SimpleXMLElement;
use SPropertyValue;
use SPropertyValueQuery;

/**
 * Base class for import/export
 *
 * each class that extends ExchangeBase can
 * request its properties from db and xml
 *
 * @author kolia
 * @property CI_DB_active_record $db
 */
abstract class ExchangeBase
{

    /**
     * "multisingleton"
     * @var array
     */
    protected static $instances = [];

    /**
     *
     * @var CI_DB_active_record
     */
    protected $db;

    /**
     *
     * @var ExchangeDataLoad
     */
    protected $dataLoad;

    /**
     *
     * @var SimpleXMLElement
     */
    protected $importData;

    /**
     * Current locale
     * @var string
     */
    protected $locale;

    /**
     *
     * @var SimpleXMLElement
     */
    protected $xml;

    /**
     * Storing results about queries
     * @var array
     */
    public static $stats = [];

    protected function __construct() {

        $this->dataLoad = ExchangeDataLoad::getInstance();
        $this->locale = MY_Controller::getCurrentLocale();
        $ci = &get_instance();
        $this->db = $ci->db;
    }

    private function __clone() {

    }

    /**
     *
     * @return Properties|Products
     */
    public static function getInstance() {

        $class = get_called_class();
        if (!isset(self::$instances[$class])) {
            self::$instances[$class] = new $class;
        }
        return self::$instances[$class];
    }

    /**
     * @param string $name
     * @return array|bool|\SplFixedArray
     */
    public function __get($name) {

        return $this->dataLoad->$name;
    }

    /**
     * Alias for CI insert_batch
     * @param string $tableName
     * @param array $data
     * @throws Exception
     */
    protected function insertBatch($tableName, $data) {

        if (FALSE == (count($data) > 0)) {
            return;
        }

        foreach ($data as $value) {
            $this->db->set($value)->insert($tableName);
        }

        $error = $this->db->_error_message();

        if (!empty($error)) {
            throw new Exception("Error on inserting into `{$tableName}`: " . $error);
        }
        // gathering statistics
        ExchangeBase::$stats[] = [
                                  'query type'    => 'insert',
                                  'table name'    => $tableName,
                                  'affected rows' => count($data),
                                 ];
    }

    /**
     * Alias for CI insert_batch
     * @param string $tableName
     * @param array $data
     * @throws Exception
     * @return bool|void
     */
    protected function insertPropertiesData($tableName, $data) {

        if (FALSE == (count($data) > 0)) {
            return false;
        }

        foreach ($data as $value) {

            $property_value = SPropertyValueQuery::create()
                ->useSPropertyValueI18nQuery()
                ->filterByLocale($value['locale'])
                ->filterByValue($value['value'])
                ->endUse()
                ->findOneByPropertyId($value['property_id']);

            if (!$property_value) {

                $property_value = new SPropertyValue();
                $property_value->setPropertyId($value['property_id']);
                $property_value->setLocale($value['locale']);
                $property_value->setValue($value['value']);
                $property_value->save();

            }

            $this->db->set($value)->insert($tableName);

            $import_data = [
                            'property_id' => $value['property_id'],
                            'product_id'  => $value['product_id'],
                            'value_id'    => $property_value->getId(),
                           ];

            /** @var CI_DB_result $test */
            $test = $this->db->get_where('shop_product_properties_data', $import_data);

            if ($test->num_rows() > 0) {
                return false;
            }

            $this->db->insert('shop_product_properties_data', $import_data);

        }

        $error = $this->db->_error_message();

        if (!empty($error)) {
            throw new Exception("Error on inserting into `{$tableName}`: " . $error);
        }
        // gathering statistics
        ExchangeBase::$stats[] = [
                                  'query type'    => 'insert',
                                  'table name'    => $tableName,
                                  'affected rows' => count($data),
                                 ];
    }

    public function setXml(SimpleXMLElement $xml) {

        $this->xml = $xml;
    }

    /**
     * Alias for CI update_batch
     * @param string $tableName
     * @param array $data
     * @param string $keyToCompare
     * @throws \Exception
     */
    protected function updateBatch($tableName, array $data, $keyToCompare) {

        if (FALSE == (count($data) > 0)) {
            return;
        }

        if ('shop_product_categories' == $tableName) {
            return;
        }

        $this->_updatePerOne($tableName, $data, $keyToCompare);

        // gathering statistics
        ExchangeBase::$stats[] = [
                                  'query type'    => 'update',
                                  'table name'    => $tableName,
                                  'affected rows' => count($data),
                                 ];
    }

    /**
     *
     * @param string $tableName
     * @param array $data
     * @param string $keyToComare
     * @throws Exception
     */
    public function _updateBatchGroup($tableName, array $data, $keyToComare) {

        $this->db->update_batch($tableName, $data, $keyToComare);

        $error = $this->db->_error_message();

        if (!empty($error)) {
            throw new Exception("Error on updating `{$tableName}`: " . $error);
        }
    }

    /**
     *
     * @param string $tableName
     * @param array $data
     * @param string $keyToCompare
     * @throws Exception
     */
    public function _updatePerOne($tableName, array $data, $keyToCompare) {
          $this->db->update_batch($tableName, $data, $keyToCompare );

//        foreach ($data as $rowData) {
//            $this->db->update($tableName, $rowData, [$keyToCompare => $rowData[$keyToCompare]], 1);
//
//            $error = $this->db->_error_message();
//            if (empty($error)) {
//                continue;
//            }
//
//            log_message('error', sprintf("DB error: '%s'", $error));
//
//            if (config_item('update:failOnError') == true) {
//                throw new Exception("Error on updating `{$tableName}`: " . $error);
//            }
//        }
    }

    /**
     * Return statistic
     * @return array
     */
    public function getStats() {

        return $this->stats;
    }

    /**
     * Sets the data for import, starts import
     * @param SimpleXMLElement $importData
     */
    public function import(SimpleXMLElement $importData) {

        if (!count($importData) > 0) {
            return;
        }
        $this->importData = $importData;
        $this->import_();
    }

    /**
     * Runs the import process
     */
    abstract protected function import_();

}