<?php

use Base\SProperties as BaseSProperties;
use Map\SPropertiesI18nTableMap;
use Map\SPropertiesTableMap;
use Propel\Runtime\Connection\ConnectionInterface;

/**
 * Skeleton subclass for representing a row from the 'shop_product_properties' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.Shop
 */
class SProperties extends BaseSProperties
{

    public function attributeLabels() {
        return [
                'ProductId'       => lang('ProductId', 'admin'),
                'Name'            => lang('Title', 'admin'),
                'CsvName'         => lang('CSV column name ', 'admin'),
                'Multiple'        => lang('Multiple selection', 'admin'),
                'Active'          => lang('Active', 'admin'),
                'Position'        => lang('Position', 'admin'),
                'Data'            => lang('Value', 'admin'),
                'ShowOnSite'      => lang('Show on product page', 'admin'),
                'MainProperty'    => lang('Main property', 'admin'),
                'ShowInCompare'   => lang('Show in product compare', 'admin'),
                'ShowInFilter'    => lang('Show in filter', 'admin'),
                'UseInCategories' => lang('Use in categories', 'admin'),
               ];
    }

    public function rules() {
        return [
                [
                 'field' => 'Name',
                 'label' => $this->getLabel('Name'),
                 'rules' => 'required',
                ],
               ];
    }

    /**
     * Create array from text.
     *
     * @access public
     * @return array|false
     */
    public function _dataToArray() {
        $data = trim($this->getData());

        //ensure that data is not already serialized
        if (is_array($array = @unserialize($data))) {
            return $array;
        }

        if ($data) {
            $result = explode("\n", $data);
            if (count($result) > 0) {
                $result = array_map('trim', $result);
                $result = array_map(
                    function ($item) {
                        return mb_substr($item, 0, 500);
                    },
                    $result
                );
                $result = array_unique($result);
            } else {
                return false;
            }
        }

        if (isset($result) && count($result) > 0) {
            return $result;
        } else {
            return false;
        }
    }

    /**
     * @param null $locale
     * @return \Propel\Runtime\Collection\ObjectCollection|SPropertyValue[]
     */
    public function getTranslatedValues($locale = null) {
        $locale = $locale ?: $this->getLocale();
        $values = $this->getSPropertyValues(\SPropertyValueQuery::create()->setComment(__METHOD__)->joinWithI18n($locale, \Propel\Runtime\ActiveQuery\Criteria::LEFT_JOIN)->orderByPosition());

        foreach ($values as $value) {
            $value->setLocale($locale);
        }
        return $values;
    }

    /**
     * Populates the translatable object using an array.
     *
     * @param      array $arr An array to populate the object from.
     * @param      string $keyType The type of keys the array uses.
     * @return     void
     */
    public function fromArray($arr, $keyType = SPropertiesTableMap::TYPE_PHPNAME) {

        $keys = SPropertiesI18nTableMap::getFieldNames($keyType);
        $locale = array_key_exists('Locale', $arr) ? $arr['Locale'] : \MY_Controller::defaultLocale();

        $valuesDb = $this->getTranslatedValues($locale);

        if (array_key_exists('property_value', $arr) && is_array($arr['property_value'])) {
            $valuesPost = $arr['property_value'];

            $updated = [];

            //update
            foreach ($valuesDb as $valueDb) {
                $id = 'id_' . $valueDb->getId();

                if (array_key_exists($id, $valuesPost)) {
                    if ($valuesPost[$id] != null) {

                        $valueDb->setValue($valuesPost[$id]);
                        $updated[$id] = $valueDb;
                        $valueDb->save();
                    }
                } else {
                    $valueDb->delete();
                }
            }

            //create && set positions
            $position = 0;
            foreach ($valuesPost as $key => $value) {
                if (strpos($key, 'id') === false) {
                    $dbValue = (new SPropertyValue())
                        ->setLocale($locale)
                        ->setValue($value)
                        ->setPosition(++$position);

                    $this->addSPropertyValue($dbValue);
                } elseif (array_key_exists($key, $updated)) {
                    $dbValue = $updated[$key];
                    $dbValue
                        ->setPosition(++$position)
                        ->save();
                }

            }
        } else {
            $valuesDb->delete();
        }

        $this->setLocale($locale);

        foreach ($keys as $key) {
            if (array_key_exists($key, $arr)) {
                $methodName = 'set' . $key;
                $this->$methodName($arr[$key]);
            }
        }

        parent::fromArray($arr, $keyType);
    }

}

// SProperties