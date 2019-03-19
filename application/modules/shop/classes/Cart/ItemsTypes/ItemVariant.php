<?php

namespace Cart\ItemsTypes;

use Exception;
use SProductVariantsQuery;

/**
 *
 * @property \SProductVariants $model
 * @author
 */
class ItemVariant extends IItemType
{

    public function getPrice() {

        return $this->getOriginPrice();
    }

    public function getOriginPrice() {

        return $this->model->getPrice();
    }

    protected function getModel() {

        $this->model = SProductVariantsQuery::create()->setComment(__METHOD__)->joinWithI18n(\MY_Controller::getCurrentLocale())
            ->filterById((int) $this->cartItem->id)
            ->findOne();

        if ($this->model === null) {
            throw new Exception('Wrong card data');
        }
    }

    protected function addDeprecatedFields() {

        $this->cartItem->variantId = $this->cartItem->id;

        if ($this->model) {
            $this->cartItem->productId = $this->model->getSProducts()->getId();

            $name = $this->model->getName();
            if (empty($name)) {
                $name = $this->model->getSProducts()->getName();
            }
            $this->cartItem->variantName = $name;
        }
    }

}