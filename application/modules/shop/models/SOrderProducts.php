<?php

use Base\SOrderProducts as BaseSOrderProducts;

/**
 * Skeleton subclass for representing a row from the 'shop_orders_products' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.Shop
 */
class SOrderProducts extends BaseSOrderProducts
{

    /**
     * Helps to identify if product was deleted
     * @return boolean true if product exist, false if product or variant or kit not found
     */
    public function originalModelExists() {
        if (SProductsQuery::create()->setComment(__METHOD__)->filterById($this->product_id)->count() == 0) {
            return false;
        }

        if (SProductVariantsQuery::create()->setComment(__METHOD__)->filterById($this->variant_id)->count() == 0) {
            return false;
        }

        if (!empty($this->kit_id) && Base\ShopKitQuery::create()->setComment(__METHOD__)->filterById($this->kit_id)->count() == 0) {
            // todo: to think - may be neede to check rows in kit
            return false;
        }

        return true;
    }

    /**
     * Get product variant
     * @return array
     */
    public function getVariant() {
        $variant = SProductVariantsQuery::create()
                ->findOneById($this->getVariantId());

        return $variant ?: [];
    }

    /**
     * @return float
     */
    public function getFinalPrice() {
        return $this->getPrice();
    }

}