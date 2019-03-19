<?php

use Base\ShopKitProduct as BaseShopKitProduct;
use Currency\Currency;


/**
 * Skeleton subclass for representing a row from the 'shop_kit_product' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.Shop
 */
class ShopKitProduct extends BaseShopKitProduct
{

    public function getKitProductPrice($CS = null) {

        if ($this->getSProducts()->getFirstVariant('kit')->hasVirtualColumn('discount')) {

            $price = $this->getSProducts()->getFirstVariant('kit')->getVirtual('origPrice');

        } else {

            $price = $this->getSProducts()->getFirstVariant('kit')->getPrice();
        }

        return Currency::create()->convert($price, $CS);
    }

    public function getKitNewPrice($CS = null) {

        return $this->getKitProductPrice($CS) - $this->getKitProductPrice($CS) * $this->getDiscount() / 100;

    }

    /**
     * Get percent value of discount
     * Alias for getDiscount method
     * Alias added for product variants interface compatibility
     *
     * @param bool $round
     * @return int|float
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function getDiscountPercent($round = true) {
        return $round ? intval(round($this->getDiscount())) : $this->getDiscount();
    }

    /**
     * Final price with discount in main currency
     * @return float
     */
    public function getFinalPrice() {
        return $this->getKitNewPrice();
    }

    /**
     * Initial price without discounts
     * @return float
     */
    public function getOriginPrice() {
        return $this->getKitProductPrice();
    }

    /**
     * Discount value in main currency
     * @return float|int
     */
    public function getDiscountStatic() {
        $discount = $this->getKitProductPrice() * $this->getDiscount() / 100;
        return ($discount > 0) ? $discount : 0;
    }

} // ShopKitProduct