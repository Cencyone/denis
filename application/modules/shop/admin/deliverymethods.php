<?php

use Propel\Runtime\ActiveQuery\Criteria;

/**
 * ShopAdminBrands
 *
 * @uses ShopController
 * @package
 * @version $id$
 * @copyright 2010 Siteimage
 * @author <dev@imagecms.net>
 * @license
 */
class ShopAdminDeliverymethods extends ShopAdminController
{

    public $defaultLanguage = null;

    public function __construct() {
        parent::__construct();

        ShopController::checkVar();
        ShopAdminController::checkVarAdmin();

        $this->defaultLanguage = getDefaultLanguage();
    }

    public function index() {
        $model = SDeliveryMethodsQuery::create()
                ->joinWithI18n(MY_Controller::defaultLocale(), Criteria::JOIN)
                ->orderByPosition(Criteria::ASC)
                ->find();

        $this->render(
            'list',
            ['model' => $model]
        );
    }

    /**
     * Create new brand
     *
     * @access public
     */
    public function create() {
        $model = new SDeliveryMethods;
        $model->setLocale($this->defaultLanguage['identif']);

        if ($this->input->post()) {
            $_POST['Description'] = strtr($this->input->post('Description'), ['"' => '&quot;']);
            if (substr(trim($this->input->post('Price'), ' '), -1) == '%') {
                $_POST['IsPriceInPercent'] = true;
            } else {
                $_POST['IsPriceInPercent'] = false;
            }

            if (!$this->input->post('delivery_sum_specified')) {
                $model->fromArray($this->input->post());
                $this->form_validation->set_rules($model->rules(false));
            } else {
                $model->fromArray($this->input->post());
                $this->form_validation->set_rules($model->rules(true));
            }

            if ($this->form_validation->run($this) == FALSE) {
                showMessage(validation_errors(), '', 'r');
            } else {
                $model->setPricedescription($this->input->post('pricedescription'));
                $model->save();

                // Clear payment systems relation
                ShopDeliveryMethodsSystemsQuery::create()
                        ->filterByDeliveryMethodId($model->getId())
                        ->delete();

                $model->setDeliverySumSpecified((int) $this->input->post('delivery_sum_specified'));
                $model->setDeliverySumSpecifiedMessage($this->input->post('delivery_sum_specified_message'));

                if ($this->input->post('paymentMethods')) {
                    $pm = SPaymentMethodsQuery::create()->setComment(__METHOD__)->findPks($this->input->post('paymentMethods'));
                    $model->setPaymentMethodss($pm);
                }

                $model->save();
                $last_delivery_id = $this->db->order_by('id', 'desc')->get('shop_delivery_methods')->row()->id;
                $this->lib_admin->log(lang('Delivery created', 'admin') . '. Id: ' . $last_delivery_id);
                showMessage(lang('Delivery created', 'admin'));
                if ($this->input->post('action') == 'close') {
                    pjax('/admin/components/run/shop/deliverymethods/index');
                } else {
                    pjax('/admin/components/run/shop/deliverymethods/edit/' . $model->getId() . '/' . $locale);
                }
            }
        } else {
            $this->render(
                'create',
                [
                 'model'          => $model,
                 'paymentMethods' => SPaymentMethodsQuery::create()->setComment(__METHOD__)->joinWithI18n(MY_Controller::defaultLocale(), Criteria::JOIN)->orderByPosition()->find(),
                ]
            );
        }
    }

    public function change_delivery_status($id) {
        $model = SDeliveryMethodsQuery::create()->setComment(__METHOD__)->findPk($id);
        if ($model->getEnabled()) {
            $model->setEnabled('0');
        } else {
            $model->setEnabled('1');
        }
        $model->save();
        $this->lib_admin->log(lang('Status delivery was edited', 'admin') . '. Id: ' . $id);
    }

    public function edit($deliveryMethodId = null, $locale = null) {
        $locale = $locale == null ? $this->defaultLanguage['identif'] : $locale;

        $model = SDeliveryMethodsQuery::create()->setComment(__METHOD__)->findPk((int) $deliveryMethodId);
        if ($model === null) {
            $this->error404(lang('Delivery method is not found', 'admin'));
        }

        if ($this->input->post()) {
            $_POST['Description'] = strtr($this->input->post('Description'), ['"' => '&quot;']);
            if (!$this->input->post('delivery_sum_specified')) {
                $this->form_validation->set_rules($model->rules(false));
            } else {
                $this->form_validation->set_rules($model->rules(true));
            }

            if (substr(trim($this->input->post('Price'), ' '), -1) == '%') {
                $_POST['IsPriceInPercent'] = true;
            } else {
                $_POST['IsPriceInPercent'] = false;
            }
            if ($this->form_validation->run($this) == FALSE) {
                showMessage(validation_errors(), '', 'r');
            } else {
                if (!$this->input->post('Enabled')) {
                    $_POST['Enabled'] = false;
                }

                $_POST['Locale'] = $locale;

                $model->fromArray($this->input->post());
                $model->setDeliverySumSpecified((int) $this->input->post('delivery_sum_specified'));
                $model->setDeliverySumSpecifiedMessage($this->input->post('delivery_sum_specified_message'));

                $model->setPricedescription($this->input->post('pricedescription'));
                $model->setDescription($this->input->post('Description'));
                $model->save();

                // Clear payment systems relation
                ShopDeliveryMethodsSystemsQuery::create()->setComment(__METHOD__)->filterByDeliveryMethodId($model->getId())
                        ->delete();

                if ($this->input->post('paymentMethods')) {
                    $pm = SPaymentMethodsQuery::create()->setComment(__METHOD__)->findPks($this->input->post('paymentMethods'));
                    $model->setPaymentMethodss($pm);
                }

                $model->setDescription($this->input->post('Description'));
                $model->save();

                $this->lib_admin->log(lang('Delivery was edited', 'admin') . '. Id: ' . $deliveryMethodId);
                showMessage(lang('Changes have been saved', 'admin'));
                if ($this->input->post('action') == 'close') {

                    pjax('/admin/components/run/shop/deliverymethods/edit/' . $deliveryMethodId . '/' . $locale);
                } else {
                    pjax('/admin/components/run/shop/deliverymethods/index');
                }
            }
        } else {
            $model->setLocale($locale);

            $this->render(
                'edit',
                [
                 'descr'          => strtr($model->getDescription(), ['&quot;' => '"']),
                 'descrPrice'     => strtr($model->getpriceDescription(), ['&quot;' => '"']),
                 'model'          => $model,
                 'languages'      => ShopCore::$ci->cms_admin->get_langs(true),
                 'paymentMethods' => SPaymentMethodsQuery::create()->setComment(__METHOD__)->joinWithI18n($locale, Criteria::JOIN)->orderByPosition()->find(),
                 'locale'         => $locale,
                ]
            );
        }
    }

    /**
     * Delete delivery method by id.
     *
     * @access public
     */
    public function deleteAll() {
        if (!$this->input->post('ids')) {
            showMessage(lang('No data transmitted', 'admin'), '', 'r');
            exit;
        }
        $count = count($this->input->post('ids'));
        if ($count > 0) {
            $model = SDeliveryMethodsQuery::create()
                    ->findPks($this->input->post('ids'));

            if (!empty($model)) {
                foreach ($model as $order) {
                    $order->delete();
                }
                $this->lib_admin->log(lang('Delivery was removed', 'admin') . '. Ids: ' . implode(', ', $this->input->post('ids')));
                showMessage(lang('Delivery method removed', 'admin'));
            }
        }
    }

    protected function redirect($model = null, $locale = null) {
        // Redirect to list
        if ($this->input->post('_add')) {
            $this->ajaxShopDiv('deliverymethods/index');
        }

        // Redirect to create new object
        if ($this->input->post('_create')) {
            $this->ajaxShopDiv('deliverymethods/create');
        }

        if ($this->input->post('_edit')) {
            $this->ajaxShopDiv('deliverymethods/edit/' . $model->getId() . '/' . $locale);
        }
    }

    /**
     *
     * @return boolean
     */
    public function save_positions() {
        $positions = $this->input->post('positions');
        if (count($positions) == 0) {
            return false;
        }

        foreach ($positions as $key => $val) {
            $query = 'UPDATE `shop_delivery_methods` SET `position`=' . $key . ' WHERE `id`=' . (int) $val . '; ';
            $this->db->query($query);
        }
        showMessage(lang('Positions saved', 'admin'));
    }

}