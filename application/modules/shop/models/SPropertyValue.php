<?php

use Base\SPropertyValue as BaseSPropertyValue;

/**
 * Skeleton subclass for representing a row from the 'shop_product_property_value' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class SPropertyValue extends BaseSPropertyValue
{

    public function getName() {
        return parent::getValue();
    }

}