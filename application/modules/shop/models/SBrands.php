<?php

use Base\SBrands as BaseSBrands;
use Map\SBrandsTableMap;
use Map\SBrandsI18nTableMap;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\TableMap;


/**
 * Skeleton subclass for representing a row from the 'shop_brands' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.Shop
 */
class SBrands extends BaseSBrands
{

    /**
     * @var string
     */
    public $entityName = 'brand';

    /**
     * SBrands constructor.
     */
    public function __construct() {

        parent::__construct();
        $this->currentLocale = \MY_Controller::getCurrentLocale();
    }

    /**
     * @return array
     */
    public function attributeLabels() {

        return [
                'Title'           => ShopCore::t(lang('Title', 'admin')),
                'Url'             => ShopCore::t(lang('URL', 'admin')),
                'Description'     => ShopCore::t(lang('Описание', 'admin')),
                'MetaTitle'       => ShopCore::t('Meta Title'),
                'MetaDescription' => ShopCore::t('Meta Description'),
                'MetaKeywords'    => ShopCore::t('Meta Keywords'),
               ];
    }

    /**
     * @return array
     */
    public function rules() {

        return [
                [
                 'field' => 'Name',
                 'label' => $this->getLabel('Title'),
                 'rules' => 'required|max_length[255]',
                ],
                [
                 'field' => 'Url',
                 'label' => $this->getLabel('Url'),
                 'rules' => 'alpha_dash|max_length[255]',
                ],
               ];
    }

    /*-------------------------------------------Clear Cache----------------------------------------------------------*/

    /**
     * @param ConnectionInterface|null $con
     * @return bool
     * @throws PropelException
     */
    public function postSave(ConnectionInterface $con = null) {

        parent::postSave($con);

        if ($this->getUrl() == '') {
            ShopCore::$ci->load->helper('translit');
            $this->setUrl(translit_url($this->getName()));
            $this->save();
        }

        $this->hasCustomData = false;
        $this->customFields = false;
        if ($this->hasCustomData === false) {
            $this->collectCustomData($this->entityName, $this->getId());
        }
        $this->saveCustomData();

        return true;
    }

    /**
     * @param ConnectionInterface|null $con
     * @return bool|void
     */
    public function postDelete(ConnectionInterface $con = null) {

        parent::postDelete($con);

    }

    /*-------------------------------------------Clear Cache END------------------------------------------------------*/

    /**
     * Populates the translatable object using an array.
     *
     * @param      array $arr An array to populate the object from.
     * @param      string $keyType The type of keys the array uses.
     * @return     void
     * @throws PropelException
     */
    public function fromArray($arr, $keyType = SBrandsTableMap::TYPE_PHPNAME) {

        $keys = SBrandsI18nTableMap::getFieldNames($keyType);

        if (array_key_exists('Locale', $arr)) {
            $this->setLocale($arr['Locale']);
        } else {
            $defaultLanguage = getDefaultLanguage();
            $this->setLocale($defaultLanguage['identif']);
        }

        foreach ($keys as $key) {
            if (array_key_exists($key, $arr)) {
                $methodName = set . $key;
                $this->$methodName($arr[$key]);
            }
        }

        parent::fromArray($arr, $keyType);
    }

    /**
     * @param string $keyType
     * @return array
     * @throws PropelException
     */
    public function getTranslatableFieldNames($keyType = TableMap::TYPE_PHPNAME) {

        $keys = SBrandsI18nTableMap::getFieldNames($keyType);
        $keys = array_flip($keys);

        if (array_key_exists('Locale', $keys)) {
            unset($keys['Locale']);
        }

        if (array_key_exists('Id', $keys)) {
            unset($keys['Id']);
        }

        return array_flip($keys);
    }

    /**
     * @param string $keyType
     * @param bool $includeLazyLoadColumns
     * @param array $alreadyDumpedObjects
     * @param bool $includeForeignObjects
     * @return array
     */
    public function toArray($keyType = TableMap::TYPE_PHPNAME, $includeLazyLoadColumns = true, $alreadyDumpedObjects = [], $includeForeignObjects = false) {

        $result = parent::toArray($keyType, $includeLazyLoadColumns, $alreadyDumpedObjects, $includeForeignObjects);

        $translatableFieldNames = $this->getTranslatableFieldNames();
        foreach ($translatableFieldNames as $fieldName) {
            $methodName = 'get' . $fieldName;
            $result[$fieldName] = $this->$methodName();
        }

        return $result;
    }

    public function translatingRules() {

        $rules = $this->rules();
        $translatingRules = [];
        $translatableFieldNames = $this->getTranslatableFieldNames();

        foreach ($rules as $rule) {
            if (in_array($rule['field'], $translatableFieldNames)) {
                $translatingRules[$rule['field']] = $rule['rules'];
            }
        }

        return $translatingRules;
    }

}

// SBrands