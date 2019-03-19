<?php

namespace Cart;

use Cart\ItemsTypes\ItemKit;
use Cart\ItemsTypes\ItemVariant;
use Exception;
use ShopCore;
use ShopKit;
use SProductVariants;

/**
 * PROPERTIES OF CLASS INSTANCE:
 * @property int $id
 * @property int $quantity
 * @property float $price
 * @property float $originPrice
 *
 * ADDITIONAL FIELDS OF PRODUCT:
 * int $variantId
 * int $productId
 * string $variantName
 *
 * @author kolia
 */
class CartItem
{
    /* possible types */

    const INSTANCE_PRODUCT = 'SProducts';
    const INSTANCE_KIT = 'ShopKit';
    const TYPE_PRODUCT = 1;
    const TYPE_KIT = 2;

    /**
     * Main data
     * @var array
     */
    public $data = [
                    'instance'    => NULL, // SProducts|ShopKit
                    'id'          => NULL, // id of variant
                    'quantity'    => NULL, // quantity in cart
                    'price'       => NULL, // changed price (may be because discount, or some else module)
                    'originPrice' => NULL, // original price from DB
                   ];

    /**
     * Additional data (for setting any variables to object)
     * @var array
     */
    protected $additionalData = [];

    /**
     * Instance(object) of concrete type that holds specific
     * for each type of item functionality. For it's public methods
     * can be reached from instance of CartItem
     * @var ItemKit|ItemVariant
     */
    protected $itemTypeConcrete;

    /**
     *
     * @param string|int $instance instance or type
     * @param integer $id
     * @param integer $quantity (optional) default 1
     * @param null|float $price
     * @throws Exception
     */
    public function __construct($instance, $id, $quantity = 1, $price = null) {
        if (is_int($instance)) {
            $instance = self::convertType($instance);
        }
        $this->data['instance'] = $instance;
        $this->data['id'] = $id;

        $this->data['quantity'] = $quantity;

        $this->itemTypeConcrete = $this->getItemType($instance);

        $this->data['originPrice'] = $this->itemTypeConcrete->getOriginPrice();
        $this->data['originPrice'] = number_format($this->data['originPrice'], ShopCore::app()->SSettings->getPricePrecision(), '.', '');
        $this->data['price'] = $price ?: $this->itemTypeConcrete->getPrice();
        $this->data['price'] = number_format($this->data['price'], ShopCore::app()->SSettings->getPricePrecision(), '.', '');

        $this->updateOverallPrice();

        $this->addDeprecatedFields();
    }

    /**
     * For compatibility of deprecated front-end cart.
     * Converts type-instance(string) in both directions. If specify
     * instance(string), then type will be returned, and vice versa.
     * @param string|int $type instance(string) or type of cart item
     * @return string|int instance (string) or type
     */
    public static function convertType($type) {
        switch ($type) {

            // from instance to type
            case self::INSTANCE_KIT:
                return self::TYPE_KIT;
            case self::INSTANCE_PRODUCT:
                return self::TYPE_PRODUCT;

            // from type to instance
            case self::TYPE_KIT:
                return self::INSTANCE_KIT;
            case self::TYPE_PRODUCT:
                return self::INSTANCE_PRODUCT;
        }
        return FALSE;
    }

    public function updateOverallPrice() {
        $this->additionalData['overallPrice'] = $this->data['price'] * $this->data['quantity'];
    }

    /**
     * Returns instance(object) of concrete type of item
     *
     * @param  string $instance
     * @return ItemKit|ItemVariant
     * @throws Exception
     */
    protected function getItemType($instance) {
        switch ($instance) {
            case self::INSTANCE_PRODUCT:
                return new ItemVariant($this);
            case self::INSTANCE_KIT:
                return new ItemKit($this);
        }
        throw new Exception('Uknown instance');
    }

    /**
     * @param string $name
     * @param string $arguments
     * @return mixed
     */
    public function __call($name, $arguments) {
        // redirect to concrete type method
        if (method_exists($this->itemTypeConcrete, $name)) {
            return call_user_func([$this->itemTypeConcrete, $name], $arguments);
        }
        // or to model
        return call_user_func_array([$this->itemTypeConcrete->model, $name], $arguments);
    }

    /**
     * @param string $name
     * @return null|ShopKit|SProductVariants
     */
    public function __get($name) {
        // main data
        switch ($name) {
            case 'id':
            case 'instance':
            case 'quantity':
            case 'price':
                $this->updateOverallPrice();
            case 'originPrice':
                return $this->data[$name];
            case 'model':
                return $this->itemTypeConcrete->model;
        }

        // additional data
        if (array_key_exists($name, $this->additionalData)) {
            return $this->additionalData[$name];
        }

        // fields of specific type
        if (isset($this->itemTypeConcrete->$name)) {
            return $this->itemTypeConcrete->$name;
        }

        return NULL;
    }

    /**
     * @param string $name
     * @param string $value
     * @throws Exception
     */
    public function __set($name, $value) {
        switch ($name) {
            case 'id':
            case 'instance':
                throw new Exception('Property "' . $name . '" can be set only on construct');
                return;
            case 'quantity':
            case 'price':
                $this->data[$name] = $value;
                return;
            case 'originPrice':
                throw new Exception('Property "' . $name . '" can not be changed');
                return;
        }

        // object will be accept any variables set
        $this->additionalData[$name] = $value;
    }

    /**
     * All main and additional fields in array
     * @return array data of current item
     */
    public function toArray() {
        return array_merge($this->data, $this->additionalData);
    }

    public function getFinalPrice() {
        return $this->price;
    }

    public function getQuantity() {
        $this->updateOverallPrice();
        return $this->data['quantity'];

    }

    /**
     * For compatibility of deprecated front-end cart
     * @return string instance+id
     */
    public function getKey() {
        switch ($this->data['instance']) {
            case self::INSTANCE_KIT:
                return self::INSTANCE_KIT . '_' . $this->data['id'];
            case self::INSTANCE_PRODUCT:
                $pId = $this->additionalData['productId'];
                $vId = $this->data['id'];
                return self::INSTANCE_PRODUCT . '_' . $pId . '_' . $vId;
        }
    }

    /**
     * @return array
     */
    public function getData() {
        return $this->data;
    }

    /**
     * @return ItemKit|ItemVariant
     */
    public function getItemTypeConcrete() {
        return $this->itemTypeConcrete;
    }

}