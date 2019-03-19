<?php

namespace Cart;

use CMSFactory\Events;
use DX_Auth;
use Exception;
use MY_Controller;
use SDeliveryMethods;
use ShopController;
use ShopCore;
use ShopKitQuery;
use SProductsQuery;

/**
 *
 *
 * @author DevImageCms
 */
class BaseCart extends ShopController
{

    const MIN_ORDER_PRICE = 1;

    /**
     *
     * @var BaseCart
     */
    private static $instance;

    /**
     * Set in mod_discount::autoload
     * @var string
     */
    public $gift_error;

    /**
     * Set in mod_discount::autoload
     * @var string
     */
    public $discount_info;

    /**
     * Set in mod_discount::autoload
     * @var float
     */
    public $gift_value;

    /**
     * Set in mod_discount::autoload
     *
     * @var string
     */
    public $gift_info;

    /**
     * Items of cart - instances of CartItem
     * @var array
     */
    protected $items = [];

    /**
     * price without discount
     * @var float
     */
    protected $originPrice;

    /**
     * price with discount
     * @var float
     */
    protected $price;

    /**
     * data of  storage
     * @var IDataStorage
     */
    protected $dataStorage;

    /**
     * Total items in cart
     * @var int
     */
    protected $totalItems;

    /** Errors messages */
    protected $errorMessages = null;

    /**
     * @var SDeliveryMethods
     */
    protected $deliveryMethod;

    /**
     * @return BaseCart
     */
    public static function getInstance() {

        if (self::$instance === null) {
            self::$instance = new BaseCart();
        }
        return self::$instance;
    }

    /**
     * __construct
     */
    public function __construct() {

        if (!self::$instance) {
            parent::__construct();
            $this->dataStorage = $this->getStorage();
            try {
                $itemsArray = $this->dataStorage->getData();
                foreach ($itemsArray as $itemData) {

                    // for capability
                    switch ($itemData['instance']) {
                        case 'ShopKit':
                            $itemData['id'] = $itemData['id'] ?: $itemData['kitId'];
                            break;
                        case 'SProducts':
                            $itemData['id'] = $itemData['id'] ?: $itemData['variantId'];
                            break;
                    }

                    $item = new CartItem($itemData['instance'], $itemData['id'], $itemData['quantity']);
                    if (!$this->checkItem($item)) {
                        $this->dataStorage->remove($itemData['instance'], $itemData['id']);
                        continue;
                    }
                    // setting additional params
                    $mandatoryParams = [
                                        'instance',
                                        'id',
                                        'quantity',
                                        'price',
                                        'originPrice',
                                       ];
                    foreach ($itemData as $key => $value) {
                        if (!in_array($key, $mandatoryParams)) {
                            $item->$key = $value;
                        }
                    }
                    $this->items[] = $item;

                    $this->recountOriginTotalPrice();
                    $this->recountTotalPrice();
                }
            } catch (Exception $exc) {
                log_message('error', 'Cart_new: ' . $exc->getMessage());
            }
            self::$instance = $this;
        }
    }

    /**
     * Check that product, kit or kit products is active and available in stock
     *
     * @param CartItem $itemData
     * @return bool
     */
    private function checkItem($itemData) {

        $model = $itemData->model;
        if (!isAviableInStock($itemData->instance, $itemData->id, $itemData->quantity)) {
            $itemData->quantity = $model ? $model->getStock() : 0;
            if ($itemData->quantity < 1) {
                return false;
            }
        }

        switch (get_class($model)) {
            case 'ShopKit':
                /** @var $model \ShopKit */
                $mainProduct = $model->getMainProduct();
                if (!$model->isActive() || !$mainProduct->isActive()) {
                    return false;
                }
                foreach ($model->getShopKitProducts() as $kitProduct) {
                    if (!$kitProduct->getSProducts()->isActive()) {
                        return false;
                    }
                }
                break;
            case 'SProductVariants':
                /** @var $model \SProductVariants */
                return $model->getSProducts()->isActive();
        }
        return true;
    }

    private function __clone() {

    }

    /**
     * get storage object
     * @access protected
     * @author DevImageCms
     * @param null|string $storage
     * @return IDataStorage
     * @copyright (c) 2013, ImageCMS
     */
    protected function getStorage($storage = NULL) {
        if (!$storage) {

            /* @var $ci MY_Controller */
            $ci = &get_instance();

            /* @var $dxAuth DX_Auth */
            $dxAuth = $ci->load->library('DX_Auth');
            if ($dxAuth->is_logged_in()) {
                return new DBStorage();
            } else {
                return new SessionStorage();
            }
        } else {

            switch ($storage) {
                case 'DBStorage':
                    return new DBStorage();
                    break;
                case 'SessionStorage':
                    return new SessionStorage();
                    break;
            }
        }
    }

    /**
     * set Quantity for product in cart
     * @access public
     * @author DevImageCms
     * @param array $data input params:
     * - (string) instance: SProducts|ShopKit
     * - (int) id: product or kit id
     * @param integer $quantity count of products for setting
     * @return array $data params:
     * - (boolean) success: result of setting quantity
     * - (boolean) setquan:
     * - (string) errors: message of error
     * @copyright (c) 2013, ImageCMS
     */
    public function setQuantity($data, $quantity) {

        foreach ($this->items as $key => $item) {
            if ($data['instance'] == $item->instance && $data['id'] == $item->id) {

                if (TRUE == isAviableInStock($data['instance'], $data['id'], $quantity)) {
                    $this->items[$key]->quantity = $quantity;
                    $this->items[$key]->updateOverallPrice();
                    $set = true;
                } else {
                    return [
                            'success' => FALSE,
                            'errors'  => TRUE,
                            'message' => 'Not enough in stock',
                           ];
                }
            }
        }

        if ($set) {
            try {
                $this->dataStorage->setData($this->getArrayToStorage($this->items));
                $data = [
                         'success' => true,
                         'setquan' => true,
                        ];
            } catch (Exception $exc) {
                $data = [
                         'success' => false,
                         'errors'  => $exc->getMessage(),
                        ];
                log_message('error', 'Cart_new: ' . $exc->getMessage());
            }

            $this->recountOriginTotalPrice();
            $this->recountTotalPrice();
        } else {
            $data = [
                     'success' => true,
                     'setquan' => false,
                    ];
        }

        return $data;
    }

    /**
     * set Price Item
     * @access public
     * @author DevImageCms
     * @param array $data input params:
     * - (string) instance: SProducts|ShopKit
     * - (int) id: product or kit id
     * @param float $price new price of products for setting
     * @return array params:
     * - (boolean) success: result of setting price
     * - (boolean) setprice:
     * - (string) errors: message of error
     * @copyright (c) 2013, ImageCMS
     */
    public function setItemPrice($data, $price) {
        foreach ($this->items as $key => $item) {
            if ($data['instance'] == $item->instance && $data['id'] == $item->id) {
                $this->items[$key]->price = $price;
                $set = true;
            }
        }

        if ($set) {
            $this->recountOriginTotalPrice();
            $this->recountTotalPrice();
        } else {
            $data = [
                     'success'  => true,
                     'setprice' => false,
                    ];
        }

        return $data;
    }

    /**
     * Add items to Cart
     * @access public
     * @author DevImageCms
     * @param array $data array with params
     * - (string) instance: required - ShopKit|SProducts
     * - (int) id: required - id of kit or variant
     * - (int) quantity: optional - amount
     * @return array status and errors (if there are so)
     * - (boolean) success: TRUE if item was added with no errors
     * - (string) errors: if success is FALSE, then you can get error that was occured from here
     * @copyright (c) 2013, ImageCMS
     */
    public function addItem(array $data) {
        try {
            $returnData = ['success' => TRUE];
            $recount = FALSE;

            $data['quantity'] = $data['quantity'] > 0 ? $data['quantity'] : 1;
            // if product is already in cart, then only +1 to quantity
            foreach ($this->items as $key => $item) {
                if ($data['instance'] == $item->instance && $data['id'] == $item->id) {
                    // first checking quantity in stock
                    $quantity = $this->items[$key]->quantity + $data['quantity'];

                    if (TRUE == isAviableInStock($data['instance'], $data['id'], $quantity)) {
                        $this->items[$key]->quantity = $quantity;
                        $recount = TRUE;
                    } else {
                        return [
                                'success' => TRUE,
                                'errors'  => 'Not enough in stock',
                               ];
                    }
                }
            }

            if ($recount == FALSE) {
                // for capability with old front-end cart (must be removed in future)
                switch ($data['instance']) {
                    case 'ShopKit':
                        isset($data['id']) ? NULL : $data['id'] = $data['kitId'];
                        break;
                    case 'SProducts':
                        isset($data['id']) ? NULL : $data['id'] = $data['variantId'];
                        break;
                    default:
                        throw new Exception('Unknown instance');
                }

                $isAvailableInStock = isAviableInStock($data['instance'], $data['id'], $data['quantity']);

                $itemExists = isExistsItems($data['instance'], $data['id']);

                if ($itemExists) {
                    if ($isAvailableInStock) {
                        $this->items[] = new CartItem($data['instance'], $data['id'], $data['quantity']);

                    } elseif (!$isAvailableInStock && (($itemStock = getItemStock($data['instance'], $data['id'])) > 0)) {
                        //set item quantity same as stock
                        $this->items[] = new CartItem($data['instance'], $data['id'], $itemStock);
                    } else {
                        $returnData = [
                                       'success' => TRUE,
                                       'errors'  => 'Not enough in stock',
                                      ];
                    }
                } else {
                    $returnData = [
                                   'success' => FALSE,
                                   'errors'  => 'Not item exists',
                                  ];

                }

            }
            $this->dataStorage->setData($this->getArrayToStorage($this->items));
        } catch (Exception $exc) {
            $returnData = [
                           'success' => false,
                           'errors'  => $exc->getMessage(),
                          ];
            log_message('error', 'Cart_new: ' . $exc->getMessage());
        }

        Events::create()->registerEvent($data, 'Cart:addItem');
        Events::runFactory();

        $this->recountOriginTotalPrice();
        $this->recountTotalPrice();

        return $returnData;
    }

    /**
     * remove item from Cart
     * @access public
     * @author DevImageCms
     * @param array $data input params:
     * - (string) instance: SProducts|ShopKit
     * - (int) id: variant id or kit id
     * @return array params:
     * - (boolean) success: result of operation
     * - (boolean) delete: result of delete item
     * - (string) errors: message of error
     * @copyright (c) 2013, ImageCMS
     */
    public function removeItem($data) {
        $unset = false;
        foreach ($this->items as $key => $item) {
            //                    dump($item->model);
            if ($item->model) {
                if ($data['instance'] == $item->instance && $data['id'] == $item->id) {

                    unset($this->items[$key]);
                    $unset = true;
                    break;
                }
            }
        }
        if ($unset) {
            try {
                $this->dataStorage->remove();
                $this->dataStorage->setData($this->getArrayToStorage($this->items));
                $data = [
                         'success' => true,
                         'delete'  => true,
                        ];
            } catch (Exception $exc) {
                $data = [
                         'success' => false,
                         'errors'  => $exc->getMessage(),
                        ];
                log_message('error', 'Cart_new: ' . $exc->getMessage());
            }
            $this->recountOriginTotalPrice();
            $this->recountTotalPrice();
        } else {
            $data = [
                     'success' => true,
                     'delete'  => false,
                    ];
        }

        return $data;
    }

    /**
     * get Total items cart
     * @access public
     * @author DevImageCms
     * @return int $total total items cart
     * @copyright (c) 2013, ImageCMS
     */
    public function getTotalItems() {
        return $this->totalItems;
    }

    /**
     * remove all items from cart
     * @access public
     * @author DevImageCms
     * @param ---
     * @return array params:
     * - (boolean) success: result of operation
     * - (boolean) delete: result of delete items
     * - (string) errors: message of error
     * @copyright (c) 2013, ImageCMS
     */
    public function removeAll() {
        try {
            $this->dataStorage = $this->getStorage();
            $this->dataStorage->remove();

            if ($this->dataStorage instanceof DBStorage) {
                $this->dataStorage = $this->getStorage('SessionStorage');
                if (count($this->dataStorage->getData())) {
                    $this->dataStorage->remove();
                }
            }

            $this->items = [];
            $data = [
                     'success' => true,
                     'delete'  => true,
                    ];
        } catch (Exception $exc) {
            $data = [
                     'success' => false,
                     'errors'  => $exc->getMessage(),
                    ];
            log_message('error', 'Cart_new: ' . $exc->getMessage());
        }

        $this->recountOriginTotalPrice();
        $this->recountTotalPrice();
        return $data;
    }

    /**
     * set cart price
     * @access public
     * @author DevImageCms
     * @param float $price new cart price
     * @copyright (c) 2013, ImageCMS
     */
    public function setTotalPrice($price) {
        $this->price = $price;
    }

    /**
     * get total price cart
     * @access public
     * @author DevImageCms
     * @return float $price total price cart
     * @copyright (c) 2013, ImageCMS
     */
    public function getTotalPrice() {

        return number_format($this->price, ShopCore::app()->SSettings->getPricePrecision(), '.', '');
    }

    /**
     * get total origin price cart
     * @access public
     * @author DevImageCms
     * @return float $origin_price total origin price cart
     * @copyright (c) 2013, ImageCMS
     */
    public function getOriginTotalPrice() {

        return number_format($this->originPrice, ShopCore::app()->SSettings->getPricePrecision(), '.', '');
    }

    /**
     * get one cart items return object classes CartItem
     * @access public
     * @author DevImageCms
     * @param array $data input params:
     * - (string) instance: SProducts|ShopKit
     * - (int) id: product or kit id
     * @return array params:
     * - (boolean) success: result of operation
     * - (array) data: cart items
     * @copyright (c) 2013, ImageCMS
     */
    public function getItem($data) {

        foreach ($this->items as $item) {

            if ($data['instance'] == $item->instance && $data['id'] == $item->id) {
                $data = [
                         'success' => true,
                         'data'    => $item,
                        ];
                return $data;
            }
        }
        $data = [
                 'success' => true,
                 'data'    => false,
                ];
        return $data;
    }

    /**
     * get cart items return array object's classes CartItem
     * @access public
     * @author DevImageCms
     * @param string $instance param for chose items
     * @return array params:
     * - (boolean) success: result of operation
     * - (array) data: cart items
     * @copyright (c) 2013, ImageCMS
     */
    public function getItems($instance = null) {
        $arrayItems = [];
        foreach ($this->items as $item) {
            if ($item->model) {
                if (null === $instance) {
                    $arrayItems[] = $item;
                } else {
                    if ($item->instance == $instance) {
                        $arrayItems[] = $item;
                    }
                }
            }
        }
        return [
                'success' => true,
                'data'    => $arrayItems,
               ];
    }

    /**
     * recount cart price and total items
     * @access private
     * @return BaseCart
     * @author DevImageCms
     * @copyright (c) 2013, ImageCMS
     */
    private function recountTotalPrice() {

        $this->price = 0;
        $this->totalItems = 0;
        if (count($this->items)) {
            foreach ($this->items as $item) {
                if ($item->model) {
                    $this->price += $item->price * $item->quantity;
                    $this->totalItems += $item->quantity;
                }
            }
        }

        return $this;
    }

    /**
     * recount cart origin price
     * @access private
     * @return object BaseCart
     * @author DevImageCms
     * @copyright (c) 2013, ImageCMS
     */
    private function recountOriginTotalPrice() {
        $this->originPrice = 0;
        if (count($this->items)) {
            foreach ($this->items as $item) {
                if ($item->model) {
                    $aux = number_format($item->originPrice, ShopCore::app()->SSettings->getPricePrecision(), '.', '');
                    $this->originPrice += $aux * $item->quantity;
                }
            }
        }
        return $this;
    }

    /**
     * get correct array for set to storage
     * @access private
     * @author DevImageCms
     * @return array $items cart items whith key
     * @copyright (c) 2013, ImageCMS
     */
    private function getArrayToStorage() {
        $result = [];
        foreach ($this->items as $item) {
            if ($item->model) {
                $result[$item->getKey()] = $item->toArray();
            }
        }

        return $result;
    }

    /**
     * Add product to cart from GET data.
     * @param string $instance
     * @return bool
     * @access public
     * @deprecated since 4.8.1
     * @depends 4.5.2
     * @author <dev@imagecms.net>
     * @copyright (c) 2013 ImageMCMS
     */
    public function add($instance = 'SProducts') {
        try {
            if ($instance == 'SProducts') {

                /** Search for product and variant */
                $model = SProductsQuery::create()->setComment(__METHOD__)->filterByActive(TRUE)->findPk($this->input->get('productId'));

                /** Is model or throw Excaption */
                ($model != FALSE) OR throwException('Wrong input data. Can\'t add to Cart');

                /** Add Product item to cart */
                $data = [
                         'model'     => $model,
                         'variantId' => (int) $this->input->get('variantId'),
                         'quantity'  => (int) $this->input->get('quantity'),
                        ];
                ShopCore::app()->SCart->add($data);

                /** Register onAddToCart Event type */
                Events::create()->registerEvent($model);
            } elseif ($instance == 'ShopKit') {

                /** Search for product and its variant */
                $model = ShopKitQuery::create()->setComment(__METHOD__)->filterByActive(TRUE)->findPk((int) $this->input->get('kitId'));

                /** Is model or throw Excaption */
                ($model != FALSE) OR throwException('Wrong input data. Can\'t add to Cart');

                /** Add Product item to cart */
                $data = [
                         'model'    => $model,
                         'quantity' => 1,
                        ];
                ShopCore::app()->SCart->add($data);

                /** Register onAddToCart Event type */
                Events::create()->registerEvent($model);
            }
            return true;
        } catch (Exception $e) {
            $this->errorMessages = $e->getMessage();
            return false;
        }
    }

    /**-----------------------------------------------------------------------------------------------------------------
     *                                                                                                        Addition
     *
     */

    /**
     * Price with discount, gift and delivery
     * @return float
     */
    public function getFinalPrice() {
        return $this->price + $this->getDeliveryPrice();
    }

    /**
     * @return SDeliveryMethods
     */
    public function getDeliveryMethod() {
        return $this->deliveryMethod;
    }

    /**
     * @param SDeliveryMethods $deliveryMethod
     */
    public function setDeliveryMethod($deliveryMethod) {
        $this->deliveryMethod = $deliveryMethod;
    }

    /**
     * Delivery price if isset delivery method and price is grater then delivery method free from
     * @return int|string
     */
    public function getDeliveryPrice() {

        if ($this->getDeliveryMethod()) {

            if ((int) $this->getDeliveryMethod()->getFreeFrom() == 0 || $this->getDeliveryMethod()->getFreeFrom() > $this->price) {

                return $this->deliveryMethod->getPrice();
            }
        }
        return 0;
    }

    /**
     * only gift
     */
    public function getGiftValue() {
        return ($this->gift_value > 0) ? $this->gift_value : 0;
    }

    /**
     * only discount
     */
    public function getDiscountValue() {
        return (isset($this->discount_info['result_sum_discount']) && $this->discount_info['result_sum_discount'] > 0) ? round($this->discount_info['result_sum_discount'], \ShopCore::app()->SSettings->getPricePrecision(), PHP_ROUND_HALF_DOWN) : 0;
    }

    /**
     * discount + gift
     */
    public function getTotalDiscountValue() {
        return $this->getDiscountValue() + $this->getGiftValue();
    }

    public function getOriginPrice() {
        return $this->originPrice;
    }

}