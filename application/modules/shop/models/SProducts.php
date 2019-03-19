<?php

use Base\SProducts as BaseSProducts;
use CMSFactory\assetManager;
use Currency\Currency;
use Map\SProductsI18nTableMap;
use mod_discount\Discount_product;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveRecord\ActiveRecordInterface;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Connection\PropelPDO;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Propel;

/**
 * Skeleton subclass for representing a row from the 'shop_products' table.
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
class SProducts extends BaseSProducts
{

    public static $obj_discount;

    /**
     * @var bool
     */
    public static $IS_ADMIN_PART = FALSE;

    public $relatedProductsCache = null;

    /**
     * @var SProductVariants
     */
    protected static $variants;

    /**
     * @var SProductVariants[]
     */
    public $variantsCache = null;

    /**
     * @var SProductVariants
     */
    public $firstVariantCache = null;

    public $appliedDiscounts;

    /**
     * @var bool
     */
    public $DFA = true;

    /**
     * @var string
     */
    public $entityName = 'product';

    /**
     * SProducts constructor.
     */
    public function __construct() {

        parent::__construct();
        $this->currentLocale = \MY_Controller::getCurrentLocale();
    }

    public function attributeLabels() {

        return [
                'Name'             => ShopCore::t('Название'),
                'Price'            => ShopCore::t('Цена'),
                'Number'           => ShopCore::t('Артикул'),
                'ShortDescription' => ShopCore::t('Краткое Описание'),
                'FullDescription'  => ShopCore::t('Полное Описание'),
                'MetaTitle'        => ShopCore::t('Meta Title'),
                'MetaDescription'  => ShopCore::t('Meta Description'),
                'MetaKeywords'     => ShopCore::t('Meta Keywords'),
                'Categories'       => ShopCore::t('Дополнительные Категории'),
                'CategoryId'       => ShopCore::t('Категория'),
                'Active'           => ShopCore::t('Активен'),
                'Hit'              => ShopCore::t('Хит'),
                'Hot'              => ShopCore::t('Новинка'),
                'Action'           => ShopCore::t('Акция'),
                'Brand'            => ShopCore::t('Бренд'),
                'Stock'            => ShopCore::t('Количество'),
                'RelatedProducts'  => ShopCore::t('Связанные товары'),
                'tpl'              => ShopCore::t('Шаблон продукта'),
               ];
    }

    public function rules() {

        return [
                [
                 'field' => 'Name',
                 'label' => lang('Название товара'),
                 'rules' => 'required|max_length[500]',
                ],
                [
                 'field' => 'variants[PriceInMain][]',
                 'label' => $this->getLabel('Price'),
                 'rules' => 'trim|required',
                ],
                [
                 'field' => 'CategoryId',
                 'label' => $this->getLabel('CategoryId'),
                 'rules' => 'required|integer',
                ],                [
                                   'field' => 'Url',
                                   'label' => lang('Url'),
                                   'rules' => 'alpha_dash|max_length[255]',
                                  ],
               ];
    }

    /**
     * @return mixed
     */
    public function getNumber() {

        return $this->getFirstVariant()->getNumber();
    }

    /**
     * After Save model
     *
     * @return boolean|string
     */
    public function postSave() {
        $route = $this->getRoute();

        if ($route) {
            $route->setType('product');
            $route->setParentUrl($this->getMainCategory()->getFullUrl());
            $route->setEntityId($this->getId());
            $route->save();
        }

        $this->hasCustomData = false;
        $this->customFields = false;
        if ($this->hasCustomData === false) {
            $this->collectCustomData($this->entityName, $this->getId());
        }
        $this->saveCustomData();

        parent::postSave();
    }

    /**
     * @param ConnectionInterface|null $con
     *
     * @return bool
     */
    public function preDelete(ConnectionInterface $con = null) {
        parent::preDelete($con);
        // Delete product variants
        $productVariants = SProductVariantsQuery::create()
            ->filterByProductId($this->getId())
            ->find();

        if ($productVariants->count() > 0) {
            foreach ($productVariants as $v) {
                $v->delete();
            }
        }

        if (count($this->getSProductImagess()) > 0) {
            foreach ($this->getSProductImagess() as $image) {
                if (!$image->isDeleted()) {
                    $image->delete();
                }
            }
        }

        return true;
    }

    /**
     * @param ConnectionInterface|null $con
     *
     * @return bool|void
     */
    public function postDelete(ConnectionInterface $con = null) {
        parent::postDelete($con);
        if ($this->getRoute()) {
            $this->getRoute()->delete();
        }
    }

    /**
     * @return float|string
     */
    public function getOldPrice() {

        $ci =& get_instance();
        if ($ci->uri->segment(5) == 'products') {
            return parent::getOldPrice();
        }
        $decimal_place = Currency::create()->getCurrencyDecimalPlaces(Currency::create()->getMainCurrency()->getId());
        return round(parent::getOldPrice(), $decimal_place);
    }

    /**
     * Get first product variant.
     *
     * @param null|string $type
     * @return mixed|ObjectCollection|SProductVariants|SProductVariants[]
     */
    public function getFirstVariant($type = null) {
        if (!isset($this->firstVariantCache[$type])) {
            $this->firstVariantCache[$type] = $this->getProductVariants(null, null, $type)->getFirst();
        }
        return $this->firstVariantCache[$type];
    }

    /**
     * Load product variants and apply discounts
     *
     * @param Criteria $criteria
     * @param PropelPDO $con
     * @param null|string $type
     * @param null|string $locale
     * @return ObjectCollection|SProductVariants[]
     * @throws PropelException
     */
    public function getProductVariants($criteria = null, PropelPDO $con = null, $type = null, $locale = null) {

        $locale = $locale ?: MY_Controller::getCurrentLocale();

        $key = ($criteria ? md5($criteria->toString()) : '') . $type . $locale;

        if (!isset($this->variantsCache[$key])) {

            $criteria = $criteria ?: SProductVariantsQuery::create();
            $criteria->joinWithI18n($locale, Criteria::LEFT_JOIN);

            if ($type === null) {
                $criteria->withColumn('IF(shop_product_variants.stock > 0, 1, 0)', 'allstock');
                $criteria->orderBy('allstock', Criteria::DESC);
            }

            $criteria->orderByPosition();

            Propel::disableInstancePooling();

            $variants = parent::getProductVariants($criteria);

            Propel::enableInstancePooling();

            if ($type != 'kit') {

                foreach ($variants as $v) {

                    if (!$v->hasVirtualColumn('appliedDiscount')) {

                        $arr_for_discount = [
                                             'product_id'  => $this->getId(),
                                             'category_id' => $this->getCategoryId(),
                                             'brand_id'    => $this->getBrandId(),
                                             'vid'         => $v->getId(),
                                             'id'          => $this->getId(),
                                            ];
                        assetManager::create()->discount = 0;

                        Discount_product::create()->getProductDiscount($arr_for_discount);

                        if ($discount = assetManager::create()->discount) {
                            $price_new = ((float) $discount['price'] - (float) $discount['discount_value'] < 0) ? 1 : (float) $discount['price'] - (float) $discount['discount_value'];
                            $v->setVirtualColumn('origPrice', $discount['price']);
                            $v->setVirtualColumn('numDiscount', $discount['discount_value']);
                            $v->setVirtualColumn('origprice', $discount['price']);
                            $v->setVirtualColumn('numdiscount', $discount['discount_value']);
                            $v->setVirtualColumn('appliedDiscount', true);

                            $v->setPrice($price_new);
                            $this->appliedDiscounts = true;
                        }
                        $v->setVirtualColumn('discount', $discount);
                    }
                }
            }

            $this->variantsCache[$key] = $variants;

        }

        return $this->variantsCache[$key];
    }

    /**
     * Get first product variant for kit.
     *
     * @param ShopKitProduct $shopKitProduct
     * @return mixed|ObjectCollection|SProductVariants|SProductVariants[]
     * @throws PropelException
     */
    public function getKitFirstVariant(ShopKitProduct $shopKitProduct) {

        $variants = $this->getKitProductVariants($shopKitProduct);

        return count($variants) > 0 ? $variants[0] : $variants;
    }

    /**
     * @param ShopKitProduct $shopKitProduct
     * @param null|Criteria $criteria
     * @param PropelPDO|null $con
     * @return ObjectCollection|SProductVariants[]
     * @throws PropelException
     */
    public function getKitProductVariants(ShopKitProduct $shopKitProduct, $criteria = null, PropelPDO $con = null) {

        $criteria = SProductVariantsQuery::create(null, $criteria)
            ->joinWithI18n(MY_Controller::getCurrentLocale(), Criteria::LEFT_JOIN)
            ->orderByPosition(Criteria::ASC);
        $variants = parent::getProductVariants($criteria, $con);

        if ($variants->count() > 0) {
            foreach ($variants as $variant) {
                $variant->setVirtualColumn('origPrice', $variant->getPrice());
                $price = $variant->getPrice() - ($variant->getPrice() / 100 * $shopKitProduct->getDiscount());
                $variant->setVirtualColumn('economy', $variant->getPrice() / 100 * $shopKitProduct->getDiscount());
                $variant->setPrice($price);
            }
        }

        return $variants;
    }

    /**
     * @return float
     * @throws PropelException
     */
    public function getRating() {

        $rating = $this->getSProductsRating() ? $this->getSProductsRating()->getRating() / $this->getVotes() : 0;
        return round($rating);
    }

    /**
     * @return int
     * @throws PropelException
     */
    public function getVotes() {

        return $this->getSProductsRating() ? $this->getSProductsRating()->getVotes() : 0;
    }

    /**
     * Check if product has applied discounts
     *
     * @return bool
     */
    public function hasDiscounts() {

        return $this->appliedDiscounts ? true : false;
    }

    /**
     * @param string|array $value
     * @return SProducts
     */
    public function setRelatedProducts($value) {

        if (is_array($value)) {
            $value = implode(',', $value);
        }

        return parent::setRelatedProducts($value);
    }

    /**
     * Get related products list.
     * @return array|bool|mixed|null|ObjectCollection
     */
    public function getRelatedProductsModels() {

        if ($this->relatedProductsCache !== null) {
            return $this->relatedProductsCache;
        }

        $ids = explode(',', $this->getRelatedProducts());
        $ids = array_map('trim', $ids);

        if (is_array($ids) && count($ids) > 0) {
            $models = SProductsQuery::create()
                ->joinWithI18n(MY_Controller::getCurrentLocale())
                ->orderByCreated(Criteria::DESC);
            if (!self::$IS_ADMIN_PART) {
                $models->filterByActive(1);
                $models->filterByArchive(0);
            }
            $models = $models->findPks($ids);

            if (count($models) > 0) {
                $this->relatedProductsCache = $models;
                return $models;
            }
        }

        $this->relatedProductsCache = false;
        return false;
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
            ->where('SProducts.Id NOT IN ?', $this->getId())
            ->filterByHit(1)
            ->filterByCategoryId($this->getCategoryId())
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
            ->where('SProducts.Id NOT IN ?', $this->getId())
            ->filterByCategoryId($this->getCategoryId())
            ->filterByHot(1)
            ->limit($limit)
            ->find();

        if (count($models) > 0) {
            return $models;
        }

        return false;
    }

    /**
     * Get products list from the same category as current product.
     *
     * @param integer $limit
     * @return array|bool|mixed|ActiveRecordInterface[]|ObjectCollection|SProducts[]
     */
    public function getSameBrandCategoryProductsModels($limit = 6) {

        $models = SProductsQuery::create()
            ->joinWithI18n(MY_Controller::getCurrentLocale())
            ->orderByCreated(Criteria::DESC)
            ->where('SProducts.Id NOT IN ?', $this->getId())
            ->filterByCategoryId($this->getCategoryId())
            ->filterByBrandId($this->getBrandId())
            ->limit($limit)
            ->find();

        if (count($models) > 0) {
            return $models;
        }

        return false;
    }

    /**
     * Get products list from the same category with a similar price as current product.
     *
     * @param integer $limit
     * @param int|float $price_percent
     * @return array|bool|mixed|ActiveRecordInterface[]|ObjectCollection|SProducts[]
     */
    public function getSimilarPriceProductsModels($limit = 6, $price_percent = 20) {

        if (($price_percent <= 100) and ($price_percent >= 0)) {
            $price_percent *= 0.01;
        } else {
            $price_percent = 0.2;
        }

        $low_similar = $this->getFirstVariant()->getPrice() - $this->getFirstVariant()->getPrice() * $price_percent;
        $high_similar = $this->getFirstVariant()->getPrice() + $this->getFirstVariant()->getPrice() * $price_percent;

        $models = SProductsQuery::create()
            ->joinWithI18n(MY_Controller::getCurrentLocale())
            ->leftJoin('ProductVariant')
            ->filterByCategoryId($this->getCategoryId())
            ->useProductVariantQuery()
            ->filterByPrice($low_similar, Criteria::GREATER_EQUAL)
            ->filterByPrice($high_similar, Criteria::LESS_EQUAL)
            ->filterByStock(0, Criteria::GREATER_THAN)
            ->filterByProductId($this->getId(), Criteria::NOT_IN)
            ->endUse()
            ->orderByCreated(Criteria::DESC)
            ->filterByActive(1)
            ->filterByArchive(0)
            ->groupById()
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
     * @param array $arr An array to populate the object from.
     * @param string $keyType The type of keys the array uses.
     * @return void
     */
    public function fromArray($arr, $keyType = TableMap::TYPE_PHPNAME) {

        $peerName = get_class($this) . I18nPeer;
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
                $methodName = set . $key;
                $this->$methodName($arr[$key]);
            }
        }

        parent::fromArray($arr, $keyType);
    }

    /**
     * @param ConnectionInterface|null $con
     * @return SProductsRating
     */
    public function getSProductsRating(ConnectionInterface $con = null) {

        if ($this->start_rating_is_loaded) {

            return $this->singleSProductsRating;

        } else {
            $this->start_rating_is_loaded = true;
            return parent::getSProductsRating($con);
        }
    }

    /**
     * @param string $keyType
     * @return array
     * @throws PropelException
     */
    public function getTranslatableFieldNames($keyType = TableMap::TYPE_PHPNAME) {

        $keys = SProductsI18nTableMap::getFieldNames($keyType);
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
    public function toArray($keyType = TableMap::TYPE_PHPNAME, $includeLazyLoadColumns = true, $alreadyDumpedObjects = [], $includeForeignObjects = false) {

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
     * get kits where this products models is a main product
     *
     * @param Criteria $criteria Optional Criteria to build the query from
     * @param bool $admin
     * @return ObjectCollection|ShopKit[]
     * @throws PropelException
     */
    public function getKits($criteria = null, $admin = false) {

        if (!($criteria instanceof Criteria)) {
            $criteria = ShopKitQuery::create();
            if (!$admin) {
                $criteria->filterByActive(TRUE);
            }
            $criteria->orderByPosition(Criteria::ASC);
        }

        return $this->getShopKits($criteria, null, $admin);
    }

    /**
     * @param Criteria|null $criteria
     * @param ConnectionInterface|null $con
     * @param bool $admin
     * @return ObjectCollection|ShopKit[]
     * @throws PropelException
     */
    public function getShopKits(Criteria $criteria = null, ConnectionInterface $con = null, $admin = false) {

        if (null === $this->collShopKits || null !== $criteria) {
            if ($this->isNew() && null === $this->collShopKits) {
                // return empty collection
                $this->initShopKits();
            } else {
                $collShopKits = ShopKitQuery::create(null, $criteria)
                    ->filterBySProducts($this);
                if (!$admin) {
                    $collShopKits = $collShopKits->filterByActive(TRUE);
                }
                $collShopKits = $collShopKits->orderByPosition(Criteria::ASC)
                    ->find($con);
            }
        }

        /** @var ShopKit $kit */
        foreach ($collShopKits as $key => $kit) {
            if (self::$IS_ADMIN_PART) {
                $this->collShopKits = $collShopKits;
                continue;
            }

            if (!$kit->getMainProduct()->getFirstVariant('kit')->getStock() or $kit->getMainProduct()->getArchive()) {
                break;
            }

            $shopKitProducts = $kit->getShopKitProducts();
            if ($shopKitProducts->count() < 1) {
                $collShopKits->remove($key);
            }

            foreach ($shopKitProducts as $product) {
                if ($product->getSProducts()->getFirstVariant('kit')->getStock() && $product->getSProducts()->getActive() && !$product->getSProducts()->getArchive() && $product->getSProducts()->getName()) {
                    $this->collShopKits = $collShopKits;
                } else {
                    $collShopKits->remove($key);
                    break;
                }
            }
        }

        return $this->collShopKits;
    }

    /**
     * Check show kits for logged user or not
     * @deprecated since 4.9
     * @return ObjectCollection|ShopKit[]
     * @throws PropelException
     */
    public function getShopKitsLoggedUsersCheck() {

        return $this->getShopKits();
    }

    /**
     * Get product properties
     * @param string $type Type of property (main, showInCompare, showInFilter, showOnProductPage)
     * @return array
     */
    public function getProductProperties($type = null) {

        /** Get product properties for product */
        $properties = SPropertiesQuery::create()
            ->filterByActive(1);

        /** Is main property */
        if ($type == 'main') {
            $properties = $properties->filterByMainProperty(1);
        }

        /** Show on product page */
        if ($type == 'showOnProductPage') {
            $properties = $properties->filterByShowOnSite(1);
        }

        /** Show in compare */
        if ($type == 'showInCompare') {
            $properties = $properties->filterByShowInCompare(1);
        }

        /** Show in filter  */
        if ($type == 'showInFilter') {
            $properties = $properties->filterByShowInFilter(1);
        }

        $properties = $properties->joinSProductPropertiesData()
            ->joinWithI18n(MY_Controller::getCurrentLocale())
            ->where('SProductPropertiesData.Locale = ?', MY_Controller::getCurrentLocale())
            ->where('SProductPropertiesData.ProductId = ?', $this->getId())
            ->where('SProductPropertiesData.Value>""')
            ->select(['Id', 'ShowInCompare', 'ShowInFilter', 'ShowOnSite', 'ShowInFilter', 'MainProperty', 'SPropertiesI18n.Name', 'SPropertiesI18n.Description'])
            ->withColumn('SProductPropertiesData.Value', 'Value')
            ->distinct()
            ->orderByPosition()
            ->find()
            ->toArray();

        /** Prepare first property */
        if ($properties != null) {

            $propertiesRes[$properties[0]['SPropertiesI18n.Name']] = $properties[0];
            $propertiesRes[$properties[0]['SPropertiesI18n.Name']] = [];
            unset($propertiesRes[$properties[0]['SPropertiesI18n.Name']]['SPropertiesI18n.Name']);
        }

        /** Prepare result array for properties */
        foreach ($properties as $prop) {
            if (array_key_exists($prop['SPropertiesI18n.Name'], $propertiesRes)) {
                $propertiesRes[$prop['SPropertiesI18n.Name']][] = $prop['Value'];
            } else {
                $propertiesRes[$prop['SPropertiesI18n.Name']] = $prop;
                $propertiesRes[$prop['SPropertiesI18n.Name']] = [$prop['Value']];
            }
        }

        return $propertiesRes;
    }

    /**
     * Get product additional images
     * @param string $orderByMethod Possible values 'asc' and 'desc'.
     * @return SProducts
     * @throws PropelException
     */
    public function getSProductAdditionalImages($orderByMethod = 'asc') {

        $c = new Criteria();
        if (strtolower($orderByMethod) == 'desc') {
            $c->addDescendingOrderByColumn('position');
        } else {
            $c->addAscendingOrderByColumn('position');
        }

        return parent::getSProductImagess($c);
    }

    /**
     * Enable admin part, user is in admin part
     */
    public static function enableAdminPart() {

        self::$IS_ADMIN_PART = TRUE;
    }

}

// SProducts