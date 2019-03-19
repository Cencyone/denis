<?php

use Base\SProductVariantPrice as BaseSProductVariantPrice;

/**
 * Skeleton subclass for representing a row from the 'shop_product_variants_prices' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class SProductVariantPrice extends BaseSProductVariantPrice
{

    public function addToModel(array $data) {

        $this->setVarId($data['var_id']);
        $this->setTypeId($data['type_id']);
        $this->setPrice($data['price']);
        $this->setProductId($data['prod_id']);
        $this->setFinalPrice($data['final']);

        $this->save();

    }

}