<?php

use Map\SPaymentMethodsTableMap;
use Propel\Runtime\ActiveQuery\Criteria;

/**
 * ShopAdminPaymentMethods
 *
 * @property Lib_admin lib_admin
 * @uses ShopAdminController
 * @package
 * @version $id$
 * @copyright
 * @author <dev@imagecms.net>
 * @license
 * @property Lib_admin lib_admin
 */
class ShopAdminPaymentmethods extends ShopAdminController
{

    /**
     * @var array|null
     */
    public $defaultLanguage = null;

    /**
     * ShopAdminPaymentmethods constructor.
     */
    public function __construct() {
        parent::__construct();

        \ShopController::checkVar();
        \ShopAdminController::checkVarAdmin();

        $this->defaultLanguage = getDefaultLanguage();

        $lang = new \MY_Lang();
        $modules = $this->db->like('name', 'payment_method_')->get('components')->result_array();
        foreach ($modules as $value) {
            $lang->load($value['name']);
        }
    }

    /**
     * Display all payment methods.
     *
     * @access public
     */
    public function index() {
        $model = SPaymentMethodsQuery::create()
                ->joinWithI18n(MY_Controller::defaultLocale(), Criteria::JOIN)
                ->orderByPosition()
                ->find();

        $payments = $this->db->like('name', 'payment_method_')
            ->get('shop_settings')
            ->result_array();

        foreach ($payments as $pm) {
            $segments = explode('_', $pm['name']);
            $id = $segments[0];
            unset($segments[0]);
            $name = implode('_', $segments);
            $pm['value'] = unserialize($pm['value']);
            $pm['value'] = $pm['value']['merchant_currency'];

            $paymentsCurrency[$id][$name] = $pm['value'];
        }

        $this->render(
            'list',
            [
             'payments' => $paymentsCurrency,
             'model'    => $model,
            ]
        );
    }

    /**
     * Create new payment method.
     *
     * @access public
     */
    public function create() {
        $model = new SPaymentMethods;
        if ($this->input->post()) {

            $this->form_validation->set_rules($model->rules());

            if ($this->form_validation->run($this) == FALSE) {
                showMessage(validation_errors(), '', 'r');
            } else {
                $model->fromArray($this->input->post());

                $posModel = SPaymentMethodsQuery::create()
                        ->select(SPaymentMethodsTableMap::COL_POSITION)
                        ->orderByPosition(Criteria::DESC)
                        ->limit(1)
                        ->find();

                $model->setPosition($posModel[0] + 1);

                $model->save();
                $last_paymet_id = $this->db->order_by('id', Criteria::DESC)->get('shop_payment_methods')->row()->id;
                $this->lib_admin->log(lang('Payment method is created', 'admin') . '. Id: ' . $last_paymet_id);
                showMessage(lang('Payment method is created', 'admin'));
                if ($this->input->post('action') == 'exit') {
                    pjax('/admin/components/run/shop/paymentmethods/index');
                } else {
                    pjax('/admin/components/run/shop/paymentmethods/edit/' . $model->getId());
                }
            }
        } else {
            $this->render(
                'create',
                [
                 'model'      => $model,
                 'currencies' => SCurrenciesQuery::create()->setComment(__METHOD__)->find(),
                ]
            );
        }
    }

    /**
     * @param int $id
     */
    public function change_payment_status($id) {
        $model = SPaymentMethodsQuery::create()
                ->findPk($id);
        if ($model->getActive()) {
            $model->setActive('0');
        } else {
            $model->setActive('1');
        }
        $model->save();
        $this->lib_admin->log(lang('Status payment was edited', 'admin') . '. Id: ' . $id);
    }

    /**
     * @param int $id
     * @param null|string $locale
     */
    public function edit($id, $locale = null) {
        $locale = $locale ?: $this->defaultLanguage['identif'];

        $model = SPaymentMethodsQuery::create()
                ->findPk((int) $id);

        if (!$model) {
            $this->error404(lang('Payment method is not found.', 'admin'));
        }

        $systemClass = $this->load->module($model->getPaymentSystemName());
        // Get settings form
        if ($model->getPaymentSystemName() != null && method_exists($systemClass, 'getForm')) {
            $systemClass->paymentMethod = $model;
            $paymentSystemForm = $systemClass->getAdminForm($id);
        }

        if ($this->input->post()) {
            $systemClass = $this->load->module($this->input->post('PaymentSystemName'));

            if (method_exists($systemClass, 'saveSettings')) {
                $result = $systemClass->saveSettings($model);
                if ($result !== true) {
                    showMessage($result, '', 'r');
                    exit;
                }
            }

            $this->form_validation->set_rules($model->rules());

            if ($this->form_validation->run($this) == FALSE) {
                showMessage(validation_errors(), '', 'r');
            } else {
                $data = $this->input->post();
                $data['Active'] = (boolean) $this->input->post('Active');
                $data['Locale'] = $locale;

                $model->fromArray($data);
                $model->save();

                $this->lib_admin->log(lang('Payment method was edited', 'admin') . '. Id: ' . $id);
                showMessage(lang('Changes have been saved', 'admin'));
                if ($this->input->post('action') == 'edit') {
                    pjax("/admin/components/run/shop/paymentmethods/edit/$id/$locale");
                } else {
                    pjax('/admin/components/run/shop/paymentmethods');
                }
            }
        } else {
            $model->setLocale($locale);

            $this->render(
                'edit',
                [
                 'model'             => $model,
                 'currencies'        => SCurrenciesQuery::create()->setComment(__METHOD__)->find(),
                 'paymentSystemForm' => $paymentSystemForm,
                 'languages'         => ShopCore::$ci->cms_admin->get_langs(true),
                 'locale'            => $locale,
                 'lang'              => $this->db->where('locale', $this->config->item('language'))->get('languages')->row()->identif,
                ]
            );
        }
    }

    /**
     * Delete payment method by id.
     *
     * @access public
     */
    public function deleteAll() {
        $ids = $this->input->post('ids');
        if ($ids && count($ids) > 0) {
            $models = SPaymentMethodsQuery::create()
                    ->findPks($ids);
            $models->delete();
            $deletePaymentSettings = function($id) {
                $this->db->like('name', "{$id}_payment_method_")->delete('shop_settings');
            };
            array_map($deletePaymentSettings, $ids);
            $this->lib_admin->log(lang('Method of payment is removed', 'admin') . '. Ids: ' . implode(', ', $ids));
            showMessage(lang('Method of payment is removed', 'admin'));
        } else {
            showMessage(lang('No data transmitted', 'admin'), '', 'r');
        }
    }

    /**
     * Save payment methods positions.
     *
     * @access public
     */
    public function savePositions() {
        $positions = $this->input->post('positions');
        if ($positions && count($positions) > 0) {
            foreach ($positions as $id => $pos) {
                SPaymentMethodsQuery::create()
                        ->filterById($pos)
                        ->update(['Position' => (int) $id]);
            }
            showMessage(lang('Positions saved', 'admin'));
        }
    }

    /**
     * @param null $model
     * @param null $locale
     */
    protected function _redirect($model = null, $locale = null) {
        if ($this->input->post('_add')) {
            $redirect_url = 'paymentmethods/index';
        }

        if ($this->input->post('_create')) {
            $redirect_url = 'paymentmethods/create';
        }

        if ($this->input->post('_edit')) {
            $redirect_url = 'paymentmethods/edit/' . $model->getId() . '/' . $locale;
        }

        if (isset($redirect_url) && $redirect_url) {
            $this->ajaxShopDiv($redirect_url);
        }
    }

    /**
     * @param null|string $systemName
     * @param null|int $paymentMethodId
     */
    public function getAdminForm($systemName = null, $paymentMethodId = null) {
        $class = $this->load->module($systemName);
        if (is_object($class)) {
            $class->paymentMethod = SPaymentMethodsQuery::create()->setComment(__METHOD__)->findPk($paymentMethodId);
            echo $class->getAdminForm();
        }
    }

}