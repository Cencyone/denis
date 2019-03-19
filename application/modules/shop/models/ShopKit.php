<?php

use Base\ShopKit as BaseShopKit;
use Currency\Currency;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\PropelPDO;
use Propel\Runtime\Exception\PropelException;

/**
 * Skeleton subclass for representing a row from the 'shop_kit' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.Shop
 */
class ShopKit extends BaseShopKit
{

    /**
     * return all attributes
     * @return array
     */
    public function attributeLabels() {
        return [
                'Id'                  => ShopCore::t('ID'),
                'Active'              => ShopCore::t('Активный'),
                'Name'                => ShopCore::t('Название'),
                'Position'            => ShopCore::t('Позиция'),
                'MainProduct'         => ShopCore::t('Главный товар'),
                'MainProductId'       => ShopCore::t('Главный товар'),
                'AttachedProductsIds' => ShopCore::t('Связанные товары'),
               ];
    }

    private $price = 0;

    private $allPrice = 0;

    /**
     * Validation rules
     * @return array
     */
    public function rules() {
        return [
                [
                 'field' => 'Active',
                 'label' => $this->getLabel('Name'),
                 'rules' => 'is_natural',
                ],
                [
                 'field' => 'Position',
                 'label' => $this->getLabel('Position'),
                 'rules' => 'is_natural',
                ],
                [
                 'field' => 'MainProductId',
                 'label' => $this->getLabel('MainProductId'),
                 'rules' => 'is_natural|required',
                ],
                [
                 'field' => 'AttachedProductsIds',
                 'label' => $this->getLabel('AttachedProductsIds'),
                 'rules' => 'required',
                ],
               ];
    }

    private $arrayForCart = [
                             'id'    => [],
                             'name'  => [],
                             'price' => [],
                            ];

    /**
     * Return The atached to a kit SProducts objects
     *
     * @param Criteria $criteria Optional Criteria to build the query from
     * @param null $con
     * @return SProducts|void
     * @throws PropelException
     */
    public function getAtachedProducts($criteria = null, $con = null) {
        $criteria = ShopKitProductQuery::create($criteria, $con)
            ->select(['ProductId']);
        $pIds = $this->getShopKitProducts($criteria, $con)->toArray();

        if (!empty($pIds)) {
            return SProductsQuery::create()->setComment(__METHOD__)->findPks($pIds);
        }
    }

    public function getStock() {
        $stock = $this->getMainProduct()->getFirstVariant('kit')->getStock();
        foreach ($this->getShopKitProducts() as $item) {
            $stock = min($stock, $item->getSProducts()->getFirstVariant('kit')->getStock());
        }
        return $stock;

    }

    /**
     * Return main product SProducts object
     *
     * @param PropelPDO $con
     * @return SProducts - main product of the kit.
     */
    public function getMainProduct(PropelPDO $con = null) {
        return $this->getSProducts($con);
    }

    /**
     * @param null $criteria
     * @return ObjectCollection|ShopKitProduct[]
     */
    public function getShopKitProducts($criteria = null) {
        $criteria = $criteria === null ? ShopKitProductQuery::create()->setComment(__METHOD__)->orderByProductId(Criteria::ASC) : $criteria;

        $product = parent::getShopKitProducts($criteria);
        $this->arrayForCart = [
                               'id'    => [],
                               'name'  => [],
                               'price' => [],
                              ];

        foreach ($product as $kitProduct) {
            if (gettype($kitProduct) !== 'string') {

                array_push($this->arrayForCart['name'], $kitProduct->getSProducts()->getName());
                array_push($this->arrayForCart['id'], (int) $kitProduct->getSProducts()->getId());

                if ($kitProduct->getSProducts()->getFirstVariant('kit')->hasVirtualColumn('discount')) {

                    $beforePrice = $kitProduct->getSProducts()->getFirstVariant('kit')->getVirtual('origPrice');

                } else {

                    $beforePrice = $kitProduct->getSProducts()->getFirstVariant('kit')->getPrice();

                }

                $kitProduct->setVirtualColumn('beforePrice', $this->moneyFormat($beforePrice));
                $this->price = $beforePrice - ($beforePrice / 100 * $kitProduct->getDiscount());
                $kitProduct->setVirtualColumn('discountProductPrice', $this->moneyFormat($this->price));
                array_push($this->arrayForCart['price'], (float) $this->moneyFormat($this->price));
            }
        }
        return $product;
    }

    /**
     * Get summary price of kit without discounts
     * @return float
     */
    public function getAllPriceBefore() {
        $allPrice = $this->getCalculatePrice('all');
        $formatAllPrice = $allPrice + $this->getMainProductPrice();
        return $this->moneyFormat($formatAllPrice);
    }

    /**
     * Get summary price of kit with discounts
     * @param null|int $CS
     * @return float
     */
    public function getTotalPrice($CS = null) {
        $price = $this->getMainProductPrice($CS);
        foreach ($this->getShopKitProducts() as $kit) {
            $price += $kit->getKitNewPrice($CS);
        }

        return $this->moneyFormat($price);
    }

    /**
     * @param null|int $CS
     * @return string
     */
    public function getTotalPriceOld($CS = null) {
        $price = $this->getMainProductPrice($CS);
        foreach ($this->getShopKitProducts() as $kit) {
            $price += $kit->getKitProductPrice($CS);
        }

        return $this->moneyFormat($price);
    }

    public function getCalculatePrice($type = NULL) {

        $this->allPrice = 0;

        foreach ($this->getShopKitProducts() as $kitProduct) {

            $products = $kitProduct->getSProducts();

            if ($products->getFirstVariant('kit')->hasVirtualColumn('discount')) {
                $price = $products->getFirstVariant('kit')->getVirtual('origPrice');
            } else {
                $price = $products->getFirstVariant('kit')->getPrice();
            }

            if ($type === 'discount') {
                $this->price = $price - ($price / 100 * $kitProduct->getDiscount());
                $this->allPrice += $this->price;
            } elseif ($type === 'all') {

                $this->price = $price;
                $this->allPrice += $this->price;
            }
        }

        return $this->allPrice;
    }

    /**
     * return price of main product in kit
     * @param null|int $CS
     * @return float
     */
    public function getMainProductPrice($CS = null) {

        $variant = $this->getMainProduct()->getFirstVariant('kit');

        if ($variant->hasVirtualColumn('discount')) {

            $price = $variant->getVirtual('origPrice');

        } else {

            $price = $variant->getPrice();
        }
        return Currency::create()->convert($price, $CS);
    }

    /**
     * @deprecated 4.9 move format logic to template
     * @param float $price
     * @return string
     */
    public function moneyFormat($price) {
        return $price;
    }

    /**
     * @return int
     */
    public function countProducts() {
        return $this->getShopKitProducts()->count() - 1;
    }

    /**
     * @return array
     */
    public function getNamesCart() {

        $names = [];
        $names[] = $this->getSProducts()->getName();
        foreach ($this->getShopKitProducts() as $kit) {
            $names[] = $kit->getSProducts()->getName();
        }
        return $names;
    }

    /**
     * @return array
     */
    public function getProductIdCart() {

        $ids = [];

        $ids[] = $this->getSProducts()->getid();
        foreach ($this->getShopKitProducts() as $kit) {
            $ids[] = $kit->getSProducts()->getId();
        }

        return $ids;
    }

    /**
     * @param int $CS
     * @return array
     */
    public function getPriceCart($CS) {
        $this->getMainProduct()->getProductVariants(null, null, 'kit');
        $arr_price = [];
        $arr_price[] = (float) $this->getMainProductPrice($CS);
        foreach ($this->getShopKitProducts() as $kit) {
            $arr_price[] += $this->moneyFormat($kit->getKitNewPrice($CS));
        }

        return $arr_price;
    }

    /**
     * @param int $CS
     * @return array
     */
    public function getOrigPriceCart($CS) {
        $this->getMainProduct()->getProductVariants(null, null, 'kit');
        $arr_price = [];
        $arr_price[] = (float) $this->getMainProductPrice($CS);
        foreach ($this->getShopKitProducts() as $kit) {
            $arr_price[] += $this->moneyFormat($kit->getKitProductPrice($CS));
        }

        return $arr_price;
    }

    public function getUrls() {
        $urls = [];

        $urls[] = shop_url('product/' . $this->getSProducts()->getUrl());
        foreach ($this->getShopKitProducts() as $kit) {
            $urls[] = shop_url('product/' . $kit->getSProducts()->getUrl());
        }

        return $urls;
    }

    public function getImgs() {
        $imgs = [];

        $imgs[] = $this->getSProducts()->getFirstVariant()->getSmallPhoto();
        foreach ($this->getShopKitProducts() as $kit) {
            $imgs[] = $kit->getSProducts()->firstVariant->getSmallPhoto();
        }

        return $imgs;
    }

    public function getKitStatus() {

        $item = $this->getSProducts();
        $arr[] = json_encode(promoLabelBtn($item->getAction(), $item->getHot(), $item->getHit(), 0));
        foreach ($this->getShopKitProducts() as $item) {
            $arr[] = json_encode(promoLabelBtn($item->getSProducts()->getAction(), $item->getSProducts()->getHot(), $item->getSProducts()->getHit(), $item->getDiscount()));
        }

        return $arr;
    }

    /**
     * Get percent value of summary kit discount
     *
     * @param bool $round
     * @return int|float
     * @throws PropelException
     */
    public function getDiscountPercent($round = true) {
        $oldPrice = $this->getTotalPriceOld();
        $totalPrice = $this->getTotalPrice();
        $difference = $oldPrice - $totalPrice;

        if ($difference > 0) {
            $discount = $difference / $oldPrice * 100;
            return $round ? (int) round($discount) : $discount;
        }

    }

    /**
     * Final price with discount in main currency
     *
     * @return float
     */
    public function getFinalPrice() {
        return $this->getTotalPrice();

    }

    /**
     * Initial price without discounts
     *
     * @return float
     */
    public function getOriginPrice() {

        return $this->getTotalPriceOld();
    }

    /**
     * Discount value in main currency
     *
     * @return float
     */
    public function getDiscountStatic() {
        $discount = $this->getOriginPrice() - $this->getFinalPrice();
        return ($discount > 0) ? $discount : 0;

    }

}