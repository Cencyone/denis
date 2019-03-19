<?php

use Cart\BaseCart;
use Cart\BaseOrder;
use cmsemail\email;
use CMSFactory\Events;
use Currency\Currency;
use Propel\Runtime\ActiveQuery\Criteria;

(defined('BASEPATH')) OR exit('No direct script access allowed');

/**
 * Shop Cart Controller
 *
 * @property Users user2
 * @uses ShopController
 * @package Shop
 * @copyright 2013 ImageCMS
 * @author <dev@imagecms.net>
 */
class Order extends BaseOrder
{

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

    /**
     * Order constructor.
     */
    public function __construct() {
        parent::__construct();
        $this->core->core_data['data_type'] = 'order';
        if (!$this->useDeprecated) {
            $this->baseCart = BaseCart::getInstance();
        }
    }

    /**
     * Render order summary Page for User.
     * @param string $orderSecretKey Order Secret key. Random string
     */
    public function view($orderSecretKey = null) {

        /** Get SOrders Model */
        $model = SOrdersQuery::create()->setComment(__METHOD__)->findOneByKey($orderSecretKey);

        ($model !== null) OR $this->core->error_404();

        /** Init Payment Systems */
        ShopCore::app()->SPaymentSystems->init($model);

        /** Init Payment Method for order */
        if ($model->getSDeliveryMethods() instanceof SDeliveryMethods) {
            $cr = new Criteria();
            $cr->add('active', TRUE, Criteria::EQUAL);
            $cr->add('shop_payment_methods.id', $model->getPaymentMethod(), Criteria::EQUAL);
            $paymentMethods = $model->getSDeliveryMethods()->getPaymentMethodss($cr);
        }

        /** Start Render Template */
        $this->core->set_meta_tags(ShopCore::t(lang('Order view') . ' #' . $model->getId()));

        $this->template->registerMeta('ROBOTS', 'NOINDEX, NOFOLLOW');
        $this->template->registerJsScript($this->load->library('lib_seo')->renderGAForCart($model, $this->core->settings));
        $delivery = SDeliveryMethodsQuery::create()
            ->setComment(__METHOD__)
            ->findPk($model->getDeliveryMethod());

        if (!empty($delivery)) {
            if ($delivery->getLocale() !== MY_Controller::getCurrentLocale()) {
                $delivery->setLocale(MY_Controller::getCurrentLocale());
            }
        }

        if (!empty($paymentMethods['0'])) {
            if ($paymentMethods['0']->getLocale() !== MY_Controller::getCurrentLocale()) {
                $paymentMethods['0']->setLocale(MY_Controller::getCurrentLocale());
            }
        }

        if ($delivery) {
            $freeFrom = $delivery->getFreeFrom();
        }

        $answerNotification = $this->db
            ->select('message')
            ->where(['name' => 'order', 'locale' => MY_Controller::getCurrentLocale()])
            ->get('answer_notifications')
            ->first_row()->message;

        Events::create()->registerEvent($model)->runFactory();

        /** Render template * */
        $this->render(
            'order_view',
            [
             'model'              => $model,
             'freeFrom'           => $freeFrom,
             'paymentMethod'      => $paymentMethods['0'],
             'answerNotification' => $answerNotification,
            ]
        );
    }

    /**
     * Save ordered products to database
     */
    public function make_order() {
        $this->load->library('form_validation');
        $this->form_validation->set_message('phone', lang('numeric'));

        $this->form_validation->set_rules('userInfo[fullName]', Fields::Name(), 'required|max_length[50]');
        $this->form_validation->set_rules('userInfo[phone]', Fields::Phone(), 'trim|required|xss_clean|phone');
        $this->form_validation->set_rules('userInfo[email]', Fields::Email(), 'valid_email|required|max_length[100]');
        $this->form_validation->set_rules('userInfo[deliverTo]', lang('Delivery address'), '');
        $this->form_validation->set_rules('userInfo[commentText]', lang('Order comment'), '');

        //set validation for get_value helper
        $this->form_validation->set_rules('deliveryMethodId');
        if (is_array($this->input->post('paymentMethodId'))) {
            $this->form_validation->set_rules('paymentMethodId[' . $this->input->post('deliveryMethodId') . ']');
        } else {
            $this->form_validation->set_rules('paymentMethodId');
        }

        // If use password, set validation for password fields
        if ((int) $this->input->post('usePassword') === 1) {
            $this->form_validation->set_rules('newPassword', lang('Password'), 'required');
            $this->form_validation->set_rules('newPassconf', lang('Repeat password'), 'required|matches[newPassword]');
        }

        $user = new \SUserProfile();
        $this->form_validation = $user->validateCustomData($this->form_validation);
        unset($user);
        $order = new \SOrders();
        $this->form_validation = $order->validateCustomData($this->form_validation);
        unset($order);

        if ($this->form_validation->run()) {
            /* changing counts of discount applies */
            Events::create()->registerEvent([], 'Cart:OrderValidated')->runFactory();
            Events::create()->removeEvent('Cart:OrderValidated'); //this event is only for discounts

            $cart = BaseCart::getInstance();

            $cartTotalPrice = $cart->getTotalPrice();

            /** Check delivery method. * */
            $deliveryMethod = SDeliveryMethodsQuery::create()
                ->setComment(__METHOD__)
                ->findPk((int) $this->input->post('deliveryMethodId'));

            if ($deliveryMethod) {
                $cart->setDeliveryMethod($deliveryMethod);
                $deliveryMethodId = $deliveryMethod->getId();
                $deliveryPrice = $cart->getDeliveryPrice();
            }

            $paymentId = $this->input->post('paymentMethodId');
            if ($paymentId) {
                $paymentId = is_array($paymentId) ? $paymentId[$deliveryMethodId] : $paymentId;
            }

            /** Check if payment method exists.* */
            $paymentMethod = SPaymentMethodsQuery::create()
                ->setComment(__METHOD__)
                ->findPk($paymentId);

            if ($paymentMethod === null) {
                $paymentMethodId = 0;
            } else {
                $paymentMethodId = $paymentMethod->getId();
            }

            /** Set user id if logged in * */
            if ($this->dx_auth->is_logged_in() === true) {
                $user_id = $this->dx_auth->get_user_id();
            } else {

                $postUserInfo = $this->input->post('userInfo');

                if ((int) $this->input->post('usePassword') === 1) {
                    $userInfo = $this->_createUser($postUserInfo['email'], $this->input->post('newPassword'));
                } else {
                    $userInfo = $this->_createUser($postUserInfo['email']);
                }
                $user_id = $userInfo['id'];
                Events::create()->registerEvent($userInfo, 'FrontOrder:userCreate');
                Events::runFactory();
            }

            /** Prepare order data * */
            $data = [];
            $userInfo = $this->input->post('userInfo');
            $data['userId'] = $user_id;
            $data['deliveryMethodId'] = $deliveryMethodId;
            $data['deliveryPrice'] = $deliveryPrice;
            $data['paymentMethodId'] = $paymentMethodId;
            $data['userFullName'] = $userInfo['fullName'];
            $data['userSurname'] = $userInfo['surname'];
            $data['userEmail'] = $userInfo['email'];
            $data['userPhone'] = $userInfo['phone'];
            $data['userDeliverTo'] = $userInfo['deliverTo'];
            $data['userCommentText'] = $userInfo['commentText'];
            $data['userIp'] = $this->input->ip_address();

            try {
                /** Products for admin's email (variant_name, quantity, price) * */
                $products = [];

                $order = $this->create($data, $products);
            } catch (Exception $exc) {
                $this->orderError($exc->getMessage());
            }

            Events::create()->raiseEvent(['order' => $order, 'price' => $order->getTotalPrice()], 'Cart:MakeOrder');

            /** Save to order history table * */
            try {
                $this->saveOrdersHistory($order->getId(), $user_id, $userInfo['commentText']);
            } catch (Exception $exc) {
                echo $exc->getMessage();
                log_message('error', 'Order: ' . $exc->getMessage());
            }

            /** Prepare products for email to administrator * */
            $productsForEmail = (new \Order\TableRenderer($order, $this->template))->render();

            /** Prepare email data * */
            preg_match('@^(?:http://)?([^/]+)@i', site_url(), $matches);
            $host = 'http://' . $matches[1] . '/';
            $checkLink = "$host/admin/components/run/shop/orders/createPdf/{$order->getId()}";
            /** Getting the site's default currency symbol * */
            $defaultCurrency = Currency::create()->getSymbol();

            $emailData = [
                          'userName'       => $order->getUserFullName(),
                          'userEmail'      => $order->getUserEmail(),
                          'userPhone'      => $order->getUserPhone(),
                          'userDeliver'    => $order->getUserDeliverTo(),
                          'orderLink'      => shop_url('order/view/' . $order->getKey()),
                          'products'       => $productsForEmail,
                          'deliveryPrice'  => $deliveryPrice . ' ' . $defaultCurrency,
                          'checkLink'      => $checkLink,
                          'totalPrice'     => $cartTotalPrice . ' ' . $defaultCurrency,
                          'deliveryMethod' => $order->getDeliveryMethodName() ?: '',
                          'paymentMethod'  => $order->getPaymentMethodName(),
                         ];

            /** Send email * */
            email::getInstance()->sendEmail($order->getUserEmail(), 'make_order', $emailData);

            /** Set flash data* */
            $this->session->set_flashdata('makeOrderForGA', true);
            $this->session->set_flashdata('makeOrderForTpl', true);
            $this->session->set_flashdata('orderMaked', true);
            $this->session->set_flashdata('makeOrderNotif', true);

            if ($this->input->post('gift_ord')) {
                $this->session->set_flashdata('makeOrderGiftKey', $this->input->post('gift'));
            }
            $this->session->set_flashdata('makeOrder', true);

            /** Redirect to view ordered prducts. * */
            redirect(shop_url('order/view/' . $order->getKey()));
        } else {
            $this->orderError(validation_errors());
        }
    }

    /**
     * @param string $errors
     */
    private function orderError($errors) {
        $this->session->set_flashdata('validation_errors', $errors);
        $this->session->set_flashdata(
            'formData',
            ['deliveryMethodId' => $this->input->post('deliveryMethodId')]
        );
        redirect(shop_url('cart'));
    }

    /**
     * Create new user
     * @param email $email
     * @param string $password
     * @return array
     */
    protected function _createUser($email, $password = null) {
        $userInfo = ['id' => NULL];
        if ((int) ShopCore::app()->SSettings->getUserInfoRegister() === 1) {

            $this->load->model('dx_auth/users', 'user2');

            if (!$password) {
                $password = self::createCode();
            }

            if ($this->dx_auth->is_email_available($email)) {
                $postUserInfo = $this->input->post('userInfo');
                $userInfo = $this->dx_auth->register($postUserInfo['fullName'], $password, $email, $postUserInfo['deliverTo'], '', $postUserInfo['phone'], TRUE);
                $userInfo['id'] = NULL;

                if ($query = $this->user2->get_user_by_email($email) AND $query->num_rows() == 1) {
                    $userInfo['id'] = $query->row()->id;
                    $userInfo['fullName'] = $postUserInfo['fullName'];
                }
            } else {

                $userInfo = $this->user2->get_user_by_email($email)->row_array();
            }

        }
        return $userInfo;
    }

    /**
     * Check if delivery method exists.
     * @param integer $deliveryMethodId
     * @return boolean
     */
    public function delivery_method_id_check($deliveryMethodId = 0) {
        $deliveryMethod = SDeliveryMethodsQuery::create()
            ->setComment(__METHOD__)
            ->findPk((int) $deliveryMethodId);

        if ($deliveryMethod === null) {
            $this->form_validation->set_message('delivery_method_id_check', lang('This method of delivery does not exist'));
            echo 'false';
        } else {
            echo 'true';
        }
    }

    /**
     * Get Payment Methods by ID
     * @param integer $deliveryId
     * @return string
     * @author <dev@imagecms.net>
     * @copyright (c) 2013 ImageCMS
     */
    public function getPaymentsMethods($deliveryId = null) {

        if ($deliveryId == null) {
            $response = [
                         'success' => false,
                         'errors'  => true,
                         'message' => 'Delivery id is null.',
                        ];
        }

        $paymentMethods = ShopDeliveryMethodsSystemsQuery::create()->setComment(__METHOD__)->filterByDeliveryMethodId($deliveryId)->find();
        foreach ($paymentMethods as $paymentMethod) {
            $paymentMethodsId[] = $paymentMethod->getPaymentMethodId();
        }
        $paymentMethod = SPaymentMethodsQuery::create()
            ->setComment(__METHOD__)
            ->filterByActive(true)
            ->where('SPaymentMethods.Id IN ?', $paymentMethodsId)
            ->orderByPosition()
            ->find();

        $jsonData = [];

        /** @var SPaymentMethods $pm */
        foreach ($paymentMethod->getData() as $pm) {
            $jsonData[] = [
                           'id'          => $pm->getId(),
                           'name'        => $pm->getName(),
                           'description' => $pm->getDescription(),
                          ];
        }
        $response = [
                     'success' => true,
                     'errors'  => false,
                     'data'    => $jsonData,
                    ];
        return json_encode($response);
    }

    /**
     * Get Payment Methods by ID
     * @param integer $deliveryId string tpl
     * @param string $tpl
     * @return string
     * @author <dev@imagecms.net>
     * @copyright (c) 2013 ImageCMS
     */
    public function getPaymentsMethodsTpl($deliveryId = null, $tpl = 'default') {

        $paymentMethods = ShopDeliveryMethodsSystemsQuery::create()->setComment(__METHOD__)->filterByDeliveryMethodId($deliveryId)->find();
        foreach ($paymentMethods as $paymentMethod) {
            $paymentMethodsId[] = $paymentMethod->getPaymentMethodId();
        }
        $paymentMethod = SPaymentMethodsQuery::create()
            ->setComment(__METHOD__)
            ->joinWithI18n(MY_Controller::getCurrentLocale(), Criteria::JOIN)
            ->filterByActive(true)
            ->where('SPaymentMethods.Id IN ?', $paymentMethodsId)
            ->orderByPosition()
            ->find();

        $this->template->assign('payments', $paymentMethod);
        $this->template->display('shop/payments/' . $tpl);
    }

}

/* End of file cart.php */