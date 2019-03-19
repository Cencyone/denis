<?php

use Base\SCategory as BaseSCategory;
use CMSFactory\Tree\TreeCollection;
use CMSFactory\Tree\TreeItemInterface;
use core\models\Route;
use core\models\RouteQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Map\SCategoryTableMap;
use Map\SCategoryI18nTableMap;
use Propel\Runtime\ActiveRecord\ActiveRecordInterface;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\TableMap;

/**
 * Skeleton subclass for representing a row from the 'shop_category' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @method string getRouteUrl()
 * @package    propel.generator.Shop
 */
class SCategory extends BaseSCategory implements TreeItemInterface
{

    /**
     * @var string
     */
    public $entityName = 'category';

    public function __construct() {
        parent::__construct();
        $this->currentLocale = \MY_Controller::getCurrentLocale();
    }

    public function getFullPath() {
        return $this->getFullUrl();
    }

    public function setFullPath($fullPAth) {
        return $this->setParentUrl($fullPAth);
    }

    public function attributeLabels() {
        return [
                'Id'           => ShopCore::t('Id'),
                'Name'         => lang('Title', 'admin'),
                'Url'          => ShopCore::t('URL'),
                'Description'  => lang('Description', 'admin'),
                'H1'           => ShopCore::t('H1'),
                'MetaDesc'     => ShopCore::t('Meta Description'),
                'MetaKeywords' => ShopCore::t('Meta Keywords'),
                'MetaTitle'    => ShopCore::t('Meta Title'),
                'ParentId'     => lang('Parent', 'admin'),
                'Active'       => lang('Active', 'admin'),
                'tpl'          => lang('Category template', 'admin'),
                'order_method' => lang('Sorting method by default', 'admin'),
               ];
    }

    /**
     * Validation rules
     *
     * @access public
     * @return array
     */
    public function rules() {
        return [
                [
                 'field' => 'Name',
                 'label' => $this->getLabel('Name'),
                 'rules' => 'required|max_length[255]',
                ],
                [
                 'field' => 'Url',
                 'label' => $this->getLabel('Url'),
                 'rules' => 'alpha_dash|max_length[255]',
                ],
               ];
    }

    /**
     * preSave hook.
     *
     * @access public
     * @param ConnectionInterface $con
     * @return bool
     */
    public function preSave(ConnectionInterface $con = null) {
        /**
         *  Translit category name to url if url empty.
         */
        $name = $this->currentTranslations[MY_Controller::getCurrentLocale()]->name;

        if ($this->getUrl() == '') {
            $ci = &get_instance();
            $ci->load->helper('translit');
            $this->setUrl(translit_url($name));
        }

        return parent::preSave($con);
    }

    /*-------------------------------------------Clear Cache----------------------------------------------------------*/

    /**
     * Clear cache after create/update categories
     *
     * @param ConnectionInterface $con
     * @return bool|void
     */
    public function postSave(ConnectionInterface $con = null) {
        $route = $this->getRoute();
        if ($route && !$route->getEntityId()) {
            $route->setType(Route::TYPE_SHOP_CATEGORY);
            $route->setEntityId($this->getId());
        }

        parent::postSave($con);

        $this->hasCustomData = false;
        $this->customFields = false;
        if ($this->hasCustomData === false) {
            $this->collectCustomData($this->entityName, $this->getId());
        }
        $this->saveCustomData();
    }

    /**
     * @param ConnectionInterface|null $con
     * @return bool
     * @throws PropelException
     */
    public function postDelete(ConnectionInterface $con = null) {

        RouteQuery::create()->deleteWithChildren($this->getRoute());
        return parent::postDelete($con);
    }

    /*-------------------------------------------Clear Cache END -----------------------------------------------------*/

    /**
     * @return bool
     */
    public function preInsert() {

        // Select max position
        $maxPositionCategory = SCategoryQuery::create()
            ->orderByPosition('desc')
            ->findOne();

        /**
         *  Set max position to all new categories
         */
        if ($maxPositionCategory) {
            $this->setPosition($maxPositionCategory->getPosition() + 1);
        } else {
            $this->setPosition(1);
        }

        return true;
    }

    /**
     * @return bool
     */
    public function hasSubCats() {

        $sub_cats = SCategoryQuery::create()
            ->filterByParentId($this->getId())
            ->count();

        return $sub_cats > 0;
    }

    /**
     * @param $id
     * @return ObjectCollection|SCategory[]
     */
    public function getChildsByParentId($id) {
        return SCategoryQuery::create()->setComment(__METHOD__)->findByParentId($id);
    }

    /**
     * @param $id
     * @return ObjectCollection|SCategory[]
     */
    public function getChildsByParentIdI18n($id) {
        return SCategoryQuery::create()
            ->filterByActive(1)
            ->joinWithI18n(MY_Controller::getCurrentLocale())
            ->orderByPosition()
            ->findByParentId($id);
    }

    /**
     * @param string $criteria
     * @param bool|string $locale
     * @return array|mixed|ObjectCollection
     */
    public function buildCategoryPath($criteria = Criteria::ASC, $locale = FALSE) {
        $ids = [];
        $result = [];
        $pathArray = unserialize($this->getFullPathIds());
        // Push self id
        array_push($pathArray, $this->getId());

        if (count($pathArray) >= 1) {
            foreach ((array) $pathArray as $val) {
                array_push($ids, $val);
            }

            $result = SCategoryQuery::create()
                ->useRouteQuery()
                ->orderByParentUrl($criteria)
                ->endUse()
                ->_if($locale)
                ->joinWithI18n(\MY_Controller::getCurrentLocale())
                ->_endif()
                ->findPks($ids);
        }
        return $result;
    }

    /**
     * @return int
     */
    public function countProperties() {
        $cr = SPropertiesQuery::create()
            ->joinWithI18n(MY_Controller::getCurrentLocale())
            ->filterByActive(TRUE)
            ->filterByShowOnSite(TRUE);
        return $this->getProperties($cr)->count();
    }

    /**
     * @param bool $showOnSite
     * @return ObjectCollection|SProperties[]
     */
    public function getProperties($showOnSite = true) {

        $cr = SPropertiesQuery::create()
            ->orderByPosition(Criteria::ASC)
            ->joinWithI18n(MY_Controller::getCurrentLocale())
            ->filterByActive(TRUE);

        if ($showOnSite) {
            $cr->filterByShowOnSite(TRUE);
        }

        return $cr->find();
    }

    /**
     * Get sample hits list from the same category as current product.
     *
     * @param integer $limit
     * @return array|bool|mixed|ActiveRecordInterface[]|ObjectCollection|SProducts[]
     */
    public function getSampleHitsModels($limit = 5) {
        $models = SProductsQuery::create()
            ->joinWithI18n(MY_Controller::getCurrentLocale())
            ->orderByCreated(Criteria::DESC)
            ->filterByHit(1)
            ->filterByCategoryId($this->getId())
            ->limit($limit)
            ->find();

        if (count($models) > 0) {
            return $models;
        }

        return false;
    }

    /**
     * Get sample popular list from the same category as current product.
     *
     * @param integer $limit
     * @return array|bool|mixed|ActiveRecordInterface[]|ObjectCollection|SProducts[]
     */
    public function getSamplePopularModels($limit = 5) {
        $models = SProductsQuery::create()
            ->joinWithI18n(MY_Controller::getCurrentLocale())
            ->orderByViews(Criteria::DESC)
            ->where('SProducts.Views > ?', 1)
            ->filterByCategoryId($this->getId())
            ->limit($limit)
            ->find();

        if (count($models) > 0) {
            return $models;
        }

        return false;
    }

    /**
     * Get sample new products list from the same category as current product.
     *
     * @param integer $limit
     * @return array|bool|mixed|ActiveRecordInterface[]|ObjectCollection|SProducts[]
     */
    public function getSampleNewestModels($limit = 6) {
        $models = SProductsQuery::create()
            ->joinWithI18n(MY_Controller::getCurrentLocale())
            ->orderByCreated(Criteria::DESC)
            ->filterByCategoryId($this->getId())
            ->filterByHot(1)
            ->limit($limit)
            ->find();

        if (count($models) > 0) {
            return $models;
        }

        return false;
    }

    /**
     * Populates the translatable object using an array.
     *
     * @param      array $arr An array to populate the object from.
     * @param      string $keyType The type of keys the array uses.
     * @return     void
     */
    public function fromArray($arr, $keyType = TableMap::TYPE_PHPNAME) {
        $peerName = get_class($this) . 'I18nPeer';
        $keys = $peerName::getFieldNames($keyType);

        if (array_key_exists('Locale', $arr)) {
            $this->setLocale($arr['Locale']);
            unset($arr['Locale']);
        } else {
            $defaultLanguage = getDefaultLanguage();
            $this->setLocale($defaultLanguage['identif']);
        }

        foreach ($keys as $key) {
            if (array_key_exists($key, $arr)) {
                $methodName = 'set' . $key;
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
    public function getTranslatableFieldNames($keyType = SCategoryTableMap::TYPE_PHPNAME) {
        $keys = SCategoryI18nTableMap::getFieldNames($keyType);
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
     * @throws PropelException
     */
    public function toArray($keyType = SCategoryTableMap::TYPE_PHPNAME, $includeLazyLoadColumns = true, $alreadyDumpedObjects = [], $includeForeignObjects = false) {
        $result = parent::toArray($keyType, $includeLazyLoadColumns, $alreadyDumpedObjects, $includeForeignObjects);

        $translatableFieldNames = $this->getTranslatableFieldNames();
        foreach ($translatableFieldNames as $fieldName) {
            $methodName = 'get' . $fieldName;
            $result[$fieldName] = $this->$methodName();
        }

        return $result;
    }

    /**
     * @return array
     * @throws PropelException
     */
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

    /**
     * @return string
     */
    public function makePageKeywords() {
        return $this->getMetaKeywords() ?: $this->virtualColumns['title'];
    }

    /**
     * @return string
     */
    public function makePageTitle() {
        return $this->getMetaTitle() ?: implode(' - ', $this->buildCategoryPath(Criteria::DESC)->toKeyValue('Id', 'Name'));
    }

    /**
     * @return string
     */
    public function makePageDesc() {
        return $this->getMetaDesc() ?: $this->getName();
    }

    /**
     * @return TreeCollection
     */
    public function getTree() {
        return SCategoryQuery::create()->setComment(__METHOD__)->getTree($this->getId());
    }

}

// SCategory