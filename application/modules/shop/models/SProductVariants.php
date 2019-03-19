<?php

use Base\SProductVariants as BaseSProductVariants;
use Currency\Currency;
use MediaManager\Image;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\TableMap;
use VariantPriceType\BaseVariantPriceType;

/**
 * Skeleton subclass for representing a row from the 'shop_product_variants' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.Shop
 */
class SProductVariants extends BaseSProductVariants
{

    public static $oldprice = 0;

    private $imageVariants = [];

     //Upload images path
    private $uploadProductsPath = '/uploads/shop/products/';

    public function __construct() {
        //Image variants names
        $this->imageVariants = Image::create()->getImageVarintsNames();
    }

    /**
     * @return string
     */
    public function getNameOrProductName() {

        $name = $this->getName() ?: $this->getSProducts()->getName();

        return $name;
    }

    /**
     * public function hydrate($row, $startcol = 0, $rehydrate = false)
     * {
     * parent::hydrate($row, $startcol, $rehydrate);
     * }
     * @param ConnectionInterface $con
     * @return bool
     */
    public function preDelete(ConnectionInterface $con = null) {

        // Delete images
        if (file_exists(ShopCore::$imagesUploadPath . $this->getMainimage())) {
            @unlink(ShopCore::$imagesUploadPath . $this->getMainimage());
        }

        if (file_exists(ShopCore::$imagesUploadPath . $this->getSmallimage())) {
            @unlink(ShopCore::$imagesUploadPath . $this->getSmallimage());
        }

        return true;
    }

    /**
     * $addPriceId передается id дополнительной категории цены вариантов
     *
     * @param int $addPriceId
     * @return float
     */
    public function getPrice($addPriceId = 0) {

        if (MY_Controller::isPremiumCMS()) {
            // Юзать при смене цен
            $price = $this->getVariantPriceType($addPriceId);

        }

        return round($price ?: parent::getPrice(), ShopCore::app()->SSettings->getPricePrecision());
    }

    /**
     * @param float $price
     * @param bool $isUseConsiderDiscount
     * @return float
     * @throws PropelException
     */
    private function checkWhenUsedAdditionalPrice($price, $isUseConsiderDiscount = false) {

        if ($this->hasVirtualColumn('discount')) {

            if ($isUseConsiderDiscount) {

                if ($price != parent::getPrice() && $this->getVirtualColumn('discount') == false) {

                    return $price;
                }

            } else {

                if ($price != parent::getPrice()) {

                    return $price;
                }
            }

        } else {

            if ($price != parent::getPrice() && !$this->hasVirtualColumn('economy')) {

                return $price;

            }
        }
    }

    /**
     * @param int $addPriceId
     * @return float
     * @throws PropelException
     */
    private function getVariantPriceType($addPriceId = 0) {

        $priceType = BaseVariantPriceType::create()
            ->setConfig($this->getId(), $addPriceId);

            /** @var BaseVariantPriceType $priceType */
            $priceType->setPriceType();

        if ($priceType->getPrice()) {

            return $this->checkWhenUsedAdditionalPrice($priceType->getPrice(), $priceType->isUseConsiderDiscount());
        }
    }

    /**
     * Populates the translatable object using an array.
     *
     * @param array $arr An array to populate the object from.
     * @param string $keyType The type of keys the array uses.
     * @param null|string $loc
     */
    public function fromArray($arr, $keyType = TableMap::TYPE_PHPNAME, $loc = null) {

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

                if ($loc) {

                    if ($key != 'Name') {
                        $methodName = set . $key;
                        $this->$methodName($arr[$key]);
                    }
                } else {
                    $methodName = set . $key;
                    $this->$methodName($arr[$key]);
                }
            }
        }

        parent::fromArray($arr, $keyType);
    }

    /**
     * @param string $keyType
     * @return array
     */
    public function getTranslatableFieldNames($keyType = TableMap::TYPE_PHPNAME) {

        $peerName = get_class($this) . I18nPeer;
        $keys = $peerName::getFieldNames($keyType);
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

    /**
     * @return SProductsI18n
     */
    public function getSproductsI18n() {

        return $this->getSProductsI18ns(
            SProductsI18nQuery::create()
                ->filterByLocale(MY_Controller::getCurrentLocale())
        )->getFirst();
    }

    /**
     * @param float $v
     * @param null|int $currencyId
     * @return $this
     * @throws PropelException
     */
    public function setPrice($v, $currencyId = null) {

        if ($v !== null) {
            $v = (double) $v;
        }

        if ((double) $this->price !== $v or $currencyId !== $this->currency) {
            if ($currencyId) {
                $v = Currency::create()->toMain($v, $currencyId);
            }
            if ($this->position == 0) {
                $productModel = SProductsQuery::create()->setComment(__METHOD__)->findPk($this->getProductId());
                if (count($productModel) > 0) {
                    $productModel->save();
                }
            }
            $this->price = $v;
            $this->modifiedColumns[] = 'price';
        }
        return $this;
    }

    /**
     * @param string $name
     * @param array $params
     * @return string
     */
    public function __call($name, $params = []) {

        $method = strtolower(substr($name, 3, -5));
        $prefix = strtolower(substr($name, -5));

        $this->imageVariants = $this->imageVariants?: Image::create()->getImageVarintsNames();

        if (in_array($method, $this->imageVariants) && $prefix == 'photo') {
            if ($this->hasMainImage()) {
                return $this->uploadProductsPath . $method . '/' . $this->getMainimage();
            } else {
                return $this->uploadProductsPath . '../nophoto/nophoto.jpg';
            }
        }
    }

    public function postSave(ConnectionInterface $con = null) {
        if (MY_Controller::isPremiumCMS()) {
               BaseVariantPriceType::recountFinalPriceForVariant($this);
        }
        parent::postSave($con);
    }

    public function hasMainImage() {

        return $this->getMainimage() != null;
    }

    public function getFinalPrice() {

        return $this->getPrice();
    }

    /**
     * Get percent value of discount
     * or of difference between old price and price as discount
     *
     * Only for template usage!!!
     *
     * @param bool|true $round
     *
     * @return float|int
     * @throws PropelException
     */
    public function getDiscountPercent($round = true) {

        $price = $this->getOriginPrice();
        $discount = $this->getDiscountStatic();

        if ($discount > 0) {
            $percent = $discount / $price * 100;
            return $round ? intval(round($percent)) : $percent;
        }

        return 0;
    }

    /**
     * Initial price without discounts or old price if discount not exists or null if there are none modifiers
     * formatted according to main currency format
     *
     * Only for template usage!!!
     *
     * @return float
     * @throws PropelException
     */
    public function getOriginPrice() {

        $originPrice = $this->hasVirtualColumn('origPrice') ? $this->getVirtualColumn('origPrice') : null;

        $useOldPrice = !$this->hasVirtualColumn('numDiscount') || !((int) $this->getVirtualColumn('numDiscount') > 0);
        $originPrice = $useOldPrice ? $this->getSProducts()->getOldPrice() : $originPrice;

        return ($originPrice > $this->getPrice()) ? $originPrice : $this->getPrice();
    }

    /**
     * Discount value or difference between old price and price in main currency
     *
     * Only for template usage!!!
     *
     * @return float
     * @throws PropelException
     */
    public function getDiscountStatic() {

        return $this->getOriginPrice() - $this->getPrice();
    }

}