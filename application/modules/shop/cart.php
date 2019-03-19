<?php

//(defined('BASEPATH')) OR exit('No direct script access allowed');

use Cart\Api;
use Cart\BaseCart;
use Cart\CartItem;
use CMSFactory\assetManager;
use mod_discount\classes\BaseDiscount;

/**
 * Shop Cart Controller
 *
 * @uses ShopController
 * @package Shop
 * @copyright 2013 ImageCMS
 * @author <dev@imagecms.net>
 */
class Cart extends BaseCart
{

    /**
     * @var int
     */
    public $_userId;

    /**
     * Validation ruls for cart page form
     * @var array
     */
    public $validation_rules = [];

    /**
     * Cart tpl name
     * @var string
     */
    public $tplName = 'cart';

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
     *
     * @var BaseCart
     */
    protected $baseCart;

    /**
     * Check use deprecated cart methods or not
     * @var boolean
     */
    private $useDeprecated;

    /** Product quantity max number */
    public $maxRange = 20;

    public function __construct() {

        parent::__construct();
        $this->load->library('Form_validation');
        $this->_userId = $this->dx_auth->get_user_id();
        $this->useDeprecated = $this->config->item('use_deprecated_cart_methods');

        if (!$this->useDeprecated) {
            $this->baseCart = BaseCart::getInstance();
        }

        /**
         * Setting validation rules
         *
         * This is for let our methods know validation rules(which fields are required, for example),
         * before this method be called, so please set validation rules for your fields here
         * instead of direct cascade setting in "$this->form_validation->set_rules".
         * Example usage see this method "_validateUserInfo".
         */
        $this->validation_rules['userInfo[fullName]'] = 'required|max_length[50]';
        $this->validation_rules['userInfo[phone]'] = 'required';
        $this->validation_rules['userInfo[email]'] = 'valid_email|required|max_length[100]';
        $this->validation_rules['deliveryMethodId'] = 'callback_delivery_method_id_check';
    }

    /**
     * Display cart page.
     *
     * @access public
     */
    public function index() {

        /** Set meta tags */
        $this->load->helper('Form');
        $this->core->set_meta_tags(ShopCore::t(lang('Cart')));
        $this->template->registerMeta('ROBOTS', 'NOINDEX, NOFOLLOW');
        $this->core->core_data['data_type'] = 'cart';

        /** Get delivery methods */
        $deliveryMethods = SDeliveryMethodsQuery::create()->setComment(__METHOD__)->getMethods();

        if (count($deliveryMethods)) {

            $paymentMethods = $deliveryMethods->getFirst()->getPaymentMethodss();
        }

        $formData = $this->session->flashdata('formData');
        if ($this->input->post('deliveryMethodId')) {
            $deliveryMethodId = $this->input->post('deliveryMethodId');
        } elseif (is_array($formData) && array_key_exists('deliveryMethodId', $formData)) {
            $deliveryMethodId = $formData['deliveryMethodId'];
        }

        if ($deliveryMethodId) {
            $deliveryMethodSelect = SDeliveryMethodsQuery::create()->setComment(__METHOD__)->getMethod($deliveryMethodId);
            $this->baseCart->setDeliveryMethod($deliveryMethodSelect);
        }

        /** Get cart items */
        if ($this->_checkResult($this->baseCart->getItems())) {
            $items = is_array($this->data) ? $this->data : [];
        } else {
            $errors = $this->errors ?: '';
        }

        /** Set user information into session */
        $this->session->set_userdata($this->input->post('userInfo'));

        /** Check flash data to contains validation errors */
        if ($this->session->flashdata('validation_errors')) {
            $errors = $this->session->flashdata('validation_errors');
        }

        if ($this->baseCart->getTotalPrice() < 0) {
            $this->baseCart->setTotalPrice(0);
        }

        $data = [
                 'cart'            => $this->baseCart,
                 'cartPrice'       => $this->baseCart->getTotalPrice(),
                 'cartOriginPrice' => $this->baseCart->getOriginTotalPrice(),
                 'totalItems'      => $this->baseCart->getTotalItems(),
                 'items'           => $items ?: [],
                 'paymentMethods'  => $paymentMethods,
                 'deliveryMethods' => $deliveryMethods,
                 'profile'         => $this->_getUserProfile(),
                 'errors'          => $errors,
                 'isRequired'      => $this->_isRequired(),
                 'formData'        => !empty($formData) ? $formData : [],
                ];

        if (BaseDiscount::checkModuleInstall()) {
            if ($this->baseCart->gift_error) {
                $data['gift_error'] = $this->baseCart->gift_error;
            }
            if ($this->baseCart->discount_info && $this->baseCart->getOriginTotalPrice() != $this->baseCart->getTotalPrice()) {
                $data['discount'] = $this->baseCart->discount_info;
                $data['discount_val'] = $this->baseCart->getOriginTotalPrice() - $this->baseCart->getTotalPrice();
            }

            if ($this->baseCart->gift_value) {
                $data['gift_key'] = $this->baseCart->gift_info;
                $data['gift_val'] = $this->baseCart->gift_value;

                if (isset($data['discount_val'])) {
                    $data['discount_val'] -= $data['gift_val'];
                }

                //Добавляет скрытое поле ключа сертификата для обсчета в make_order
                assetManager::create()->registerJsScript(
                    'var newElGift = document.createElement("div");'
                    . 'newElGift.innerHTML = "<input type=\"hidden\" value=\"' . $data['gift_key'] . '\" name=\"giftKey\">";'
                    . 'document.forms[1].appendChild(newElGift);'
                );
            }
        }

        if ($deliveryMethodSelect) {
            $data['deliveryMethod'] = $deliveryMethodSelect;
            $data['paymentMethods'] = $deliveryMethodSelect->getPaymentMethodss();
        }
        /** Render cart page */
        assetManager::create()->setData($data)->render($this->tplName, FALSE, FALSE);
    }

    /**
     * Add to cart product by variant id
     * @param integer $id
     */
    public function addProductByVariantId($id) {

        $this->_addItem(CartItem::INSTANCE_PRODUCT, $id);
    }

    /**
     * Add to cart product by variant id
     * @param string $ids
     */
    public function addProductByVariantIds($ids) {

        $array = explode('|', $ids);
        foreach ($array as $id) {
            $this->_addItem(CartItem::INSTANCE_PRODUCT, $id);
        }
    }

    /**
     * Add kit to cart
     * @param integer $kitId
     */
    public function addKit($kitId) {

        $this->_addItem(CartItem::INSTANCE_KIT, $kitId);
    }

    /**
     * Call action Cart Api class
     * @param string $action
     * @param $value
     * @return string
     */
    public function api($action, $value) {

        if (($action && $value) || $action) {
            return Api::create()->$action($value);
        } else {
            if ($this->input->is_ajax_request()) {
                return json_encode(['success' => false, 'errors' => true, 'message' => 'Method not found.']);
            } else {
                $this->core->error_404();
            }
        }
    }

    /**
     * Get product by variant id
     * @param integer $id
     * @return array
     */
    public function getProductByVariantId($id) {

        return $this->_getItem(CartItem::INSTANCE_PRODUCT, $id);
    }

    /**
     * Get kit by id
     * @param integer $kitId
     * @return array
     */
    public function getKit($kitId) {

        return $this->_getItem(CartItem::INSTANCE_KIT, $kitId);
    }

    /**
     * Remove kit by kit id
     * @param integer $kitId
     */
    public function removeKit($kitId) {

        $this->_remove(CartItem::INSTANCE_KIT, $kitId);
    }

    /**
     * Remove product by variant id
     * @param integer $id
     */
    public function removeProductByVariantId($id) {

        $this->_remove(CartItem::INSTANCE_PRODUCT, $id);
    }

    /**
     * Remove all items from cart
     */
    public function removeAll() {

        if ($this->_checkResult($this->baseCart->removeAll())) {
            $this->_redirectToCart();
        } else {
            $this->core->error_404();
        }
    }

    /**
     * Get total cart items count
     * @return int
     */
    public function getTotalItemsCount() {

        $count = $this->baseCart->getTotalItems();
        if ($count || $count === 0) {
            if (is_int($count) && $count > 0) {
                return $count;
            } else {
                return 0;
            }
        } else {
            return FALSE;
        }
    }

    /**
     * Get cart price with discounts
     * @return float
     */
    public function getPrice() {

        $this->_checkResult($this->baseCart->getTotalPrice());
        return $this->_returnValidPrice($this->baseCart->getTotalPrice());
    }

    /**
     * Get cart origin price without discounts
     * @return float
     */
    public function getOriginPrice() {

        $this->_checkResult($this->baseCart->getOriginTotalPrice());
        return $this->_returnValidPrice($this->baseCart->getOriginTotalPrice());
    }

    /**
     * Set total cart price
     * @param float $price
     * @return boolean
     */
    public function setTotalPrice($price) {

        if ((is_numeric($price) && $price > 0)) {
            $this->baseCart->setTotalPrice($price);
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Get cart data
     * @return array
     */
    public function getData() {

        if ($this->_checkResult($this->baseCart->getItems())) {
            return $this->data;
        } else {
            return [];
        }
    }

    /**
     * Return set of required fields in cart form
     * @return boolean
     */
    private function _isRequired() {

        foreach ($this->validation_rules as $validationKey => $validationValue) {
            if (false !== stripos($validationValue, 'required')) {
                $reqArr[$validationKey] = TRUE;
            } else {
                $reqArr[$validationKey] = FALSE;
            }
        }
        return $reqArr;
    }

    /**
     * Validate and return valid price
     * @param float $price
     * @return float
     */
    private function _returnValidPrice($price) {

        if ($price || $price === 0) {
            if (is_numeric($price) && $price > 0) {
                return $price;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    /**
     * Make general remove item from cart
     * @param string $instance
     * @param integer $id
     */
    private function _remove($instance, $id) {

        if ($id) {
            $data = [
                     'instance' => $instance,
                     'id'       => $id,
                    ];

            if ($this->_checkResult($this->baseCart->removeItem($data))) {
                //$this->_redirectBack();
                $this->_redirectToCart();
            } else {
                $this->core->error_404();
            }
        } else {
            $this->core->error_404();
        }
    }

    /**
     * Make general get item from cart
     * @param string $instance
     * @param int $id
     * @return array
     */
    private function _getItem($instance, $id) {

        if ($id) {
            $data = [
                     'instance' => $instance,
                     'id'       => $id,
                    ];
            if ($this->_checkResult($this->baseCart->getItem($data))) {
                return $this->data;
            } else {
                return [];
            }
        } else {
            return [];
        }
    }

    /**
     * Make general add item to cart
     * @param string $instance
     * @param int $id
     */
    private function _addItem($instance, $id) {

        $quantity = (boolean) $this->input->post('quantity') ? (int) $this->input->post('quantity') : 1;

        $data = [
                 'instance' => $instance,
                 'id'       => $id,
                 'quantity' => $quantity,
                ];
        try {
            $result = $this->_checkResult($this->baseCart->addItem($data));
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }

        if ($result == TRUE) {

            $toCart = $this->input->post('redirect') === 'cart';

            if ($this->input->post('mobile') == 1 || $toCart == TRUE) {
                $this->_redirectToCart();
            } else {
                $this->_redirectBack();
            }
        }

        $this->core->error_404();
    }

    /**
     * Redirect to back url
     */
    private function _redirectBack() {

        redirect($this->input->server('HTTP_REFERER'));
    }

    /**
     * Redirect to cart
     */
    private function _redirectToCart() {

        redirect(shop_url('cart'));
    }

    /**
     * Check result received from parent class methods
     * @param array $result
     * @return boolean
     */
    private function _checkResult($result = []) {

        if (is_array($result)) {
            if ($result['success'] == TRUE) {
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

    /**
     * Get user profile data
     * @return array
     */
    private function _getUserProfile() {

        if (!$this->_userId) {
            return [
                    'name'    => $this->session->userdata('fullName'),
                    'surname' => $this->session->userdata('surname'),
                    'phone'   => $this->session->userdata('phone'),
                    'address' => $this->session->userdata('deliverTo'),
                    'email'   => $this->session->userdata('email'),
                   ];
        } else {

            if (!$this->_userId) {
                return [];
            }

            $profile = SUserProfileQuery::create()->setComment(__METHOD__)->filterById($this->_userId)->findOne();
            $user = $this->db->where('id', $this->_userId)->get('users')->row_array();
            if (!$profile) {
                return [];
            }

            if (!($email = $profile->getUserEmail())) {
                $email = $user['email'];
            }

            return [
                    'id'      => $profile->getId(),
                    'name'    => $profile->getName(),
                    'surname' => '',
                    'phone'   => $profile->getPhone(),
                    'address' => $profile->getAddress(),
                    'email'   => $email,
                   ];
        }
    }

    /**
     * Set quantity for product by variant id
     * @param integer $vId
     */
    public function setQuantityProductByVariantId($vId) {

        $data = [
                 'instance' => 'SProducts',
                 'id'       => $vId,
                ];

        $this->updateQuantity($data);
    }

    /**
     *
     * @param integer $kitId
     */
    public function setQuantityKitById($kitId) {

        $data = [
                 'instance' => 'ShopKit',
                 'id'       => $kitId,
                ];

        $this->updateQuantity($data);
    }

    /**
     *
     * @param array $data
     */
    protected function updateQuantity($data) {

        $quantity = (int) $this->input->get('quantity') ?: 1;
        $this->baseCart->setQuantity($data, $quantity);
        $this->_redirectToCart();
        //$this->_redirectBack();
    }

}

/* End of file cart.php */