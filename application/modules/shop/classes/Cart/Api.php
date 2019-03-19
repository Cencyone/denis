<?php

namespace Cart;

use Currency\Currency;
use Propel\Runtime\ActiveQuery\Criteria;

/**
 *
 *
 * @author
 */
class Api
{

    /**
     * Instance of Api class
     * @var Api
     */
    protected static $_Instance;

    /**
     * Hold errors from parent class methods
     * @var array
     */
    public $errors = [];

    /**
     * Hold success data from parent class methods
     * @var array
     */
    public $data = [];

    /**
     * Instance of BaseCart
     * @var BaseCart
     */
    private $baseCart;

    public $ci;

    public function __construct() {
        $this->baseCart = BaseCart::getInstance();
        $this->ci = & get_instance();
        $this->ci->load->module('core');
    }

    /**
     * Create singleton Api class
     * @return Api
     */
    public static function create() {
        (null !== self::$_Instance) OR self::$_Instance = new self();
        self::$_Instance->key = null;
        return self::$_Instance;
    }

    /**
     *
     * @param string $name
     * @param array $params
     * @return string
     */
    public function __call($name, $params = []) {
        if (!method_exists($this, $name)) {
            return json_encode(['success' => false, 'errors' => true, 'message' => 'Method not found.']);
        }
    }

    public function index() {
        $this->ci->core->error_404();
    }

    /**
     *
     * @todo Check getItems from Base Class
     */
    public function sync() {
        $response = [];
        $CSID = $this->_getCSID();

        /** Load items from cart */
        if ($this->_checkResult($this->baseCart->getItems())) {
            $items = is_array($this->data) ? $this->data : [];
        } else {
            $errors = $this->errors ? $this->errors : '';
            return json_encode(['success' => false, 'errors' => true, 'message' => $errors]);
        }
        foreach ($items as $item) {

            if ($item->instance == 'SProducts') {
                $product = $item->getSProducts();

                /** Calculate discount */
                $discount = 0;
                if ($item->originPrice && $item->price) {
                    $discount = (100 / $item->originPrice) * ($item->originPrice - $item->price);
                }

                /** Prepare product item response array */
                $response['cartItem_' . $item->getProductId() . '_' . $item->getId()] = [
                                                                                         'id'         => $item->getProductId(),
                                                                                         'vId'        => $item->getId(),
                                                                                         'price'      => $item->price,
                                                                                         'addprice'   => $item->toCurrency('Price', $CSID),
                                                                                         'origprice'  => $item->originPrice,
                                                                                         'name'       => $product->getName(),
                                                                                         'vname'      => $item->getName(),
                                                                                         'count'      => $item->quantity,
                                                                                         'maxcount'   => $item->getStock(),
                                                                                         'number'     => $item->getNumber(),
                                                                                         'url'        => shop_url('product/' . $product->getUrl()),
                                                                                         'img'        => $item->getSmallPhoto(),
                                                                                         'prodstatus' => promoLabelBtn($product->getAction(), $product->getHot(), $product->getHit(), $discount),
                                                                                        ];
            } else {
                if ($item->instance == 'ShopKit') {

                    /** Prepare kit item response array */
                    $response['cartItem_' . implode(',', $item->getProductIdCart()) . '_' . $item->getMainProduct()->firstVariant->getId()] = [
                                                                                                                                               'count'      => $item->quantity,
                                                                                                                                               'kit'        => true,
                                                                                                                                               'price'      => $item->price,
                                                                                                                                               'prices'     => json_encode($item->getPriceCart()),
                                                                                                                                               'addprice'   => $item->getTotalPrice($CSID),
                                                                                                                                               'addprices'  => json_encode($item->getPriceCart($CSID)),
                                                                                                                                               'origprice'  => $item->originPrice,
                                                                                                                                               'origprices' => json_encode($item->getOrigPriceCart()),
                                                                                                                                               'id'         => implode(',', $item->getProductIdCart()),
                                                                                                                                               'name'       => json_encode($item->getNamesCart()),
                                                                                                                                               'kitId'      => $item->getId(),
                                                                                                                                               'vId'        => $item->getMainProduct()->firstVariant->getId(),
                                                                                                                                               'url'        => json_encode($item->getUrls()),
                                                                                                                                               'img'        => json_encode($item->getImgs()),
                                                                                                                                               'maxcount'   => $item->getSProducts()->firstVariant->getStock(),
                                                                                                                                               'prodstatus' => json_encode($item->getKitStatus()),
                                                                                                                                              ];
                }
            }
        }

        return json_encode(['success' => true, 'errors' => false, 'data' => ['items' => $response]]);
    }

    /**
     * Get quantity cart items
     * @return int
     */
    public function getAmountInCart() {
        $id = $this->ci->input->post('id');
        $instance = $this->ci->input->post('instance');
        $items = BaseCart::getInstance()->getItems();
        foreach ($items['data'] as $itemData) {
            if ($itemData->instance == $instance & $itemData->id == $id) {
                return $itemData->quantity;
            }
        }
        return 0;
    }

    /**
     * Get kit discount
     * @deprecated since version 4.5.2 use getKitDiscount
     * @return string
     */
    public function get_kit_discount() {
        $this->getKitDiscount();
    }

    /**
     * Get kit discount
     * @return string
     */
    public function getKitDiscount() {
        $items = $this->baseCart->getItems();
        $discount = 0;
        if ($items) {
            foreach ($items as $item) {
                if ($item instanceof \ShopKit) {
                    /** Calculate discount */
                    $discount += ($item->getTotalPriceOld() - $item->getTotalPrice()) * $item->quantity;
                }
            }

            $discount = number_format($discount, \ShopCore::app()->SSettings->getPricePrecision(), '.', '');
        }

        return json_encode(['success' => true, 'errors' => false, 'data' => $discount]);
    }

    /**
     * Set quantity for product by variant id
     * @param integer $vId
     * @return string
     */
    public function setQuantityProductByVariantId($vId) {
        $quantity = (int) $this->ci->input->get('quantity') ?: 1;

        $data = [
                 'instance' => 'SProducts',
                 'id'       => $vId,
                ];

        if ($this->_checkResult($this->baseCart->setQuantity($data, $quantity))) {
            $response = [
                         'success' => true,
                         'errors'  => false,
                         'data'    => $this->data,
                        ];
        } else {
            $response = [
                         'success' => false,
                         'errors'  => true,
                         'message' => $this->errors,
                        ];
        }
        return json_encode($response);
    }

    /**
     * Set quantity for kit by id
     * @param int $kitId
     * @return string
     */
    public function setQuantityKitById($kitId) {
        $quantity = (int) $this->ci->input->get('quantity') ?: 1;

        $data = [
                 'instance' => 'ShopKit',
                 'id'       => $kitId,
                ];

        if ($this->_checkResult($this->baseCart->setQuantity($data, $quantity))) {
            $response = [
                         'success' => true,
                         'errors'  => false,
                         'data'    => $this->data,
                        ];
        } else {
            $response = [
                         'success' => false,
                         'errors'  => true,
                         'message' => $this->errors,
                        ];
        }
        return json_encode($response);
    }

    /**
     * Add to cart product by variant id
     * @param integer $id
     * @return string
     */
    public function addProductByVariantId($id) {
        return $this->_addItem(CartItem::INSTANCE_PRODUCT, $id);
    }

    /**
     * Add kit to cart
     * @param integer $kitId
     * @return string
     */
    public function addKit($kitId) {
        return $this->_addItem(CartItem::INSTANCE_KIT, $kitId);
    }

    /**
     * Get product by variant id
     * @param integer $id
     * @return string
     */
    public function getProductByVariantId($id) {
        return $this->_getItem(CartItem::INSTANCE_PRODUCT, $id);
    }

    /**
     * Get kit by id
     * @param integer $kitId
     * @return string
     */
    public function getKit($kitId) {
        return $this->_getItem(CartItem::INSTANCE_KIT, $kitId);
    }

    /**
     * Remove kit by kit id
     * @param integer $kitId
     * @return string
     */
    public function removeKit($kitId) {
        return $this->_remove(CartItem::INSTANCE_KIT, $kitId);
    }

    /**
     * Remove product by variant id
     * @param integer $id
     * @return string
     */
    public function removeProductByVariantId($id) {
        return $this->_remove(CartItem::INSTANCE_PRODUCT, $id);
    }

    /**
     * Remove all items from cart
     * @return string
     */
    public function removeAll() {
        if ($this->_checkResult($this->baseCart->removeAll())) {
            $response = [
                         'success' => true,
                         'errors'  => false,
                        ];
        } else {
            $response = [
                         'success' => false,
                         'errors'  => true,
                         'message' => $this->errors,
                        ];
        }
        return json_encode($response);
    }

    /**
     * Get total cart items count
     * @return string
     */
    public function getTotalItemsCount() {
        $count = $this->baseCart->getTotalItems();

        if ($count || $count === 0) {
            if (is_int($count) && $count > 0) {
                $response = [
                             'success' => true,
                             'errors'  => false,
                             'data'    => $count,
                            ];
            } else {
                $response = [
                             'success' => true,
                             'errors'  => false,
                             'data'    => 0,
                            ];
            }
        } else {
            $response = [
                         'success' => false,
                         'errors'  => true,
                         'message' => 'Can not get total count.',
                        ];
        }

        return json_encode($response);
    }

    /**
     * Get cart price with discounts
     * @return string
     */
    public function getPrice() {
        return $this->_returnValidPrice($this->baseCart->getTotalPrice());
    }

    /**
     * Get cart origin price without discounts
     * @return string
     */
    public function getOriginPrice() {
        return $this->_returnValidPrice($this->baseCart->getOriginTotalPrice());
    }

    /**
     * Set total cart price
     * @param float $price
     * @return string
     */
    public function setTotalPrice($price) {
        if ((is_numeric($price))) {
            $this->baseCart->setTotalPrice($price);
            $response = [
                         'success' => true,
                         'errors'  => false,
                        ];
        } else {
            $response = [
                         'success' => false,
                         'errors'  => true,
                         'message' => 'Not valid price value.',
                        ];
        }
        return json_encode($response);
    }

    /**
     * Get cart data
     * @return string
     */
    public function getData() {
        if ($this->_checkResult($this->baseCart->getItems())) {
            $response = [
                         'success' => true,
                         'errors'  => false,
                         'data'    => $this->data,
                        ];
        } else {
            $response = [
                         'success' => false,
                         'errors'  => true,
                         'message' => $this->errors,
                        ];
        }
        return json_encode($response);
    }

    /**
     * Validate and return valid price
     * @param float $price
     * @return string
     */
    private function _returnValidPrice($price) {
        if ($price || $price === 0) {
            if (is_numeric($price) && $price > 0) {
                $response = [
                             'success' => true,
                             'errors'  => false,
                             'data'    => (float) $price,
                            ];
            } else {
                $response = [
                             'success' => true,
                             'errors'  => false,
                             'data'    => 0,
                            ];
            }
        } else {
            $response = [
                         'success' => false,
                         'errors'  => true,
                         'message' => 'Not valid price value.',
                        ];
        }
        return json_encode($response);
    }

    /**
     * Make general remove item from cart
     * @param string $instance
     * @param integer $id
     * @return string
     */
    private function _remove($instance, $id) {
        if ($id) {
            $data = [
                     'instance' => $instance,
                     'id'       => $id,
                    ];

            if ($this->_checkResult($this->baseCart->removeItem($data))) {
                $response = [
                             'success' => true,
                             'errors'  => false,
                            ];
            } else {
                $response = [
                             'success' => false,
                             'errors'  => true,
                             'message' => $this->errors,
                            ];
            }
        } else {
            $response = [
                         'success' => false,
                         'errors'  => true,
                         'message' => 'You have not specified item id.',
                        ];
        }
        return json_encode($response);
    }

    /**
     * Make general get item from cart
     * @param string $instance
     * @param integer $id
     * @return string
     */
    private function _getItem($instance, $id) {
        if ($id) {
            $data = [
                     'instance' => $instance,
                     'id'       => $id,
                    ];

            if ($this->_checkResult($this->baseCart->getItem($data))) {
                $response = [
                             'success' => true,
                             'errors'  => false,
                             'data'    => $this->data->data,
                            ];
            } else {
                $response = [
                             'success' => false,
                             'errors'  => true,
                             'message' => $this->errors,
                            ];
            }
        } else {
            $response = [
                         'success' => false,
                         'errors'  => true,
                         'message' => 'You have not specified item id.',
                        ];
        }
        return json_encode($response);
    }

    /**
     * Make general add item to cart
     * @param string $instance
     * @param integer $id
     * @return string
     */
    private function _addItem($instance, $id) {
        if ($id) {

            $quantity = (int) $this->ci->input->get('quantity') ? (int) $this->ci->input->get('quantity') : 1;

            $data = [
                     'instance' => $instance,
                     'id'       => $id,
                     'quantity' => $quantity,
                    ];

            if ($this->_checkResult($this->baseCart->addItem($data))) {
                $response = [
                             'success' => true,
                             'errors'  => false,
                            ];
            } else {
                $response = [
                             'success' => false,
                             'errors'  => true,
                             'message' => $this->errors,
                            ];
            }
        } else {
            $response = [
                         'success' => false,
                         'errors'  => true,
                         'message' => 'You have not specified item id.',
                        ];
        }
        return json_encode($response);
    }

    /**
     * Get CSID
     * @return int
     */
    private function _getCSID() {
        if (count(Currency::create()->getCurrencies()) > 1 AND Currency::create()->default) {
            $currentCurrency = \SCurrenciesQuery::create()->setComment(__METHOD__)->filterById(Currency::create()->default->getId(), Criteria::NOT_EQUAL)->findOne()->getId();
        }

        Currency::create()->initCurrentCurrency(null);
        Currency::create()->initAdditionalCurrency($currentCurrency);
        $nextCurrency = \SCurrenciesQuery::create()->setComment(__METHOD__)->filterById((int) $currentCurrency)->findOne();
        return $nextCurrency->getId();
    }

    /**
     * Check result received from parent class methods
     * @param array $result
     * @return boolean
     */
    private function _checkResult($result = []) {
        if (is_array($result)) {
            if ($result['success']) {
                if ($result['data']) {
                    $this->data = $result['data'];
                } else {
                    $this->data = '';
                }
                return TRUE;
            }

            if ($result['errors']) {
                $this->errors = $result['message'];
                return FALSE;
            }
        } else {
            $this->errors = 'Not valid results from parent methods';
            return FALSE;
        }
    }

}