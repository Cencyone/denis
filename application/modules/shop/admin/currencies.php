<?php
use CMSFactory\Events;

/**
 * ShopAdminCurrencies
 *
 * @property Lib_admin lib_admin
 * @uses ShopAdminController
 * @package
 * @version $id$
 * @copyright
 * @author <dev@imagecms.net>
 * @license
 */
class ShopAdminCurrencies extends ShopAdminController
{

    public function __construct() {

        parent::__construct();
        \ShopController::checkVar();
        \ShopAdminController::checkVarAdmin();
    }

    /**
     * Display list of currencies
     *
     * @access public
     */
    public function index() {
        $model = SCurrenciesQuery::create()
            ->orderById()
            ->find();

        $this->render(
            'list',
            ['model' => $model]
        );
    }

    /**
     * Create new currency
     *
     * @access public
     * @return void
     */
    public function create() {
        $model = new SCurrencies;

        if ($this->input->post()) {
            $this->form_validation->set_rules($model->rules());

            if ($this->form_validation->run($this) == FALSE) {
                showMessage(validation_errors(), '', 'r');
            } else {
                $data = $this->input->post();
                $data['CurrencyTemplate'] = serialize(
                    [
                     'Thousands_separator' => '.',
                     'Separator_tens'      => ',',
                     'Decimal_places'      => '0',
                     'Zero'                => '0',
                     'Format'              => '# ' . $this->input->post('Symbol'),
                    ]
                );
                $model->fromArray($data);
                $model->save();
                $this->lib_admin->log(lang('Currency created', 'admin') . ' - ' . $data['Name']);
                $this->cache->delete_all();
                showMessage(lang('Currency created', 'admin'));
                if ($this->input->post('action') == 'tomain') {
                    pjax('/admin/components/run/shop/currencies');
                } else {
                    pjax('/admin/components/run/shop/currencies/edit/' . $model->getId());
                }
            }
        } else {
            $this->render(
                'create',
                ['model' => $model]
            );
        }
    }

    /**
     * Edit currency
     *
     * @access public
     */
    public function edit($id) {
        $model = SCurrenciesQuery::create()
            ->findPk($id);

        if ($model === null) {
            $this->error404(lang('Currency not found', 'admin'));
        }

        $currFormate = unserialize($model->getCurrencyTemplate());

        if (!$currFormate || !$currFormate['Format']) {
            \Currency\AdminCurrency::create()->editCurrencyFormat($id, '# ' . $model->getSymbol(), '.', ',', '1', '1');

            $currFormate['Thousands_separator'] = '.';
            $currFormate['Separator_tens'] = ',';
            $currFormate['Decimal_places'] = '1';
            $currFormate['Zero'] = '1';
            $currFormate['Format'] = '# ' . $model->getSymbol();
        }

        $mainCurrency = \Currency\Currency::create()->getMainCurrency();
        $mainDecimal = unserialize($mainCurrency->getCurrencyTemplate());
        $mainDecimal = $mainDecimal['Decimal_places'];
        $mainId = $mainCurrency->getId();

        if ($this->input->post()) {
            $this->form_validation->set_rules($model->rules());

            if ($this->form_validation->run($this) == FALSE) {
                showMessage(validation_errors(), '', 'r');
            } else {
                $data = $this->input->post();
                $format = strtr($data['Format'], [$data['SymbolOld'] => $data['Symbol']]);
                unset($data['SymbolOld']);

                $data['CurrencyTemplate'] = serialize(
                    [
                     'Thousands_separator' => $this->input->post('Thousands_separator'),
                     'Separator_tens'      => $this->input->post('Separator_tens'),
                     'Decimal_places'      => $this->input->post('Decimal_places'),
                     'Zero'                => $this->input->post('Zero') ? '1' : '0',
                     'Format'              => $format,
                    ]
                );

                $model->fromArray($data);
                $model->save();

                if ($id == $mainId) {
                    $allCurrencies = $this->db->get('shop_currencies')->result_array();
                    foreach ($allCurrencies as $value) {
                        $cur_temp = unserialize($value['currency_template']);
                        if ($this->input->post('Decimal_places') < $cur_temp['Decimal_places']) {
                            $cur_temp['Decimal_places'] = $this->input->post('Decimal_places');
                            $this->db->where('id', $value['id'])->update('shop_currencies', ['currency_template' => serialize($cur_temp)]);
                        }
                    }
                }

                \Currency\Currency::create()->checkPrices($model->getId());

                $this->lib_admin->log(lang('Currency was updated', 'admin') . ' - ' . $data['Name']);
                $this->cache->delete_all();
                showMessage(lang('Changes have been saved', 'admin'));

                if ($this->input->post('action') == 'tomain') {
                    pjax('/admin/components/run/shop/currencies');
                } else {
                    pjax('/admin/components/run/shop/currencies/edit/' . $model->getId());
                }
            }
        } else {
            $this->render(
                'edit',
                [
                 'model'       => $model,
                 'currFormat'  => $currFormate,
                 'mainDecimal' => $mainDecimal,
                ]
            );
        }
    }

    /**
     * Set additional currency and unset all previous
     * @access public
     */
    public function showOnSite() {
        if ($this->input->post()) {

            /**
             * Unset all previous additional currencies
             */

            $model = SCurrenciesQuery::create()->setComment(__METHOD__)->findPk((int) $this->input->post('id'));

            if ($model === null) {
                $this->error404(lang('Currency not found', 'admin'));
            }

            $model->setShowOnSite((int) $this->input->post('showOnSite'));
            $model->save();

            if ($model != null) {
                $this->lib_admin->log(lang('Additional currency was edited', 'admin') . ' - ' . $model->getName());
            }
        }
    }

    /**
     * makeCurrencyDefault
     *
     * @access public
     */
    public function makeCurrencyDefault($idDb) {
        if (count(SCurrenciesQuery::create()->setComment(__METHOD__)->find()) > 1) {
            $id = (int) $this->input->post('id');
            if ($id == $idDb) {
                return;
            }

            $model = SCurrenciesQuery::create()->setComment(__METHOD__)->findPk($id);

            if ($model !== null) {
                SCurrenciesQuery::create()->setComment(__METHOD__)->update(['IsDefault' => false]);

                $model->setIsDefault(true);
                if ($model->save()) {
                    echo true;
                }
            }
        }
    }

    /**
     * makeCurrencyMain
     *
     * @access public
     */
    public function makeCurrencyMain() {

        $mainCurrency = $this->db->where('main', 1)->get('shop_currencies')->row();
        $this->makeCurrencyDefault($mainCurrency->id);
        if (count(SCurrenciesQuery::create()->setComment(__METHOD__)->find()) > 1) {
            $id = (int) $this->input->post('id');
            if ($id == $mainCurrency->id) {
                return;
            }

            $this->db->update('shop_payment_methods', ['currency_id' => $id]);
            $model = SCurrenciesQuery::create()->setComment(__METHOD__)->findPk($id);
            if ($model !== null) {
                if ($model->getMain()) {
                    return true;
                }
                SCurrenciesQuery::create()->setComment(__METHOD__)->update(['Main' => false]);
                $model->setMain(true);
                if ($model->save()) {
                    echo true;
                }

                //Пересчет количества символов в шаблоне
                $allCurrencies = $this->db->get('shop_currencies')->result_array();
                $mainDecimal = unserialize($mainCurrency->currency_template);
                $mainDecimal = $mainDecimal['Decimal_places'];

                foreach ($allCurrencies as $value) {
                    $cur_temp = unserialize($value['currency_template']);
                    if ($mainDecimal < $cur_temp['Decimal_places']) {
                        $cur_temp['Decimal_places'] = $mainDecimal;
                        $this->db->where('id', $value['id'])->update('shop_currencies', ['currency_template' => serialize($cur_temp)]);
                    }
                }
                //end Пересчет количества символов в шаблоне

                if ($model !== null) {
                    $this->lib_admin->log(lang('Main currency was edited', 'admin') . ' - ' . $model->getName());
                }

                //recount rates
                $diff = $mainCurrency->rate / $model->getRate();
                $allCurrencies = SCurrenciesQuery::create()
                    ->find();

                foreach ($allCurrencies as $one) {
                    switch ($one->getId()) {
                        case $model->getId():
                            $rate = 1;
                            break;
                        case $mainCurrency->id:
                            $rate = $diff;
                            break;
                        default:
                            $rate = $one->getRate() * $diff;
                            break;
                    }
                    $rate = str_replace(',', '.', (string) $rate);
                    $one->setRate($rate);
                    $one->save();

                };
                \Currency\Currency::create()->checkPrices();

                Events::create()->raiseEvent(['model' => $model], 'ShopAdminCurrencies:makeCurrencyMain');
            }
        }
    }

    /**
     * @param int $currencyId
     */
    public function ajaxDeleteWindow($currencyId) {

        if (self::isPremiumCMS() && (int) $currencyId != null) {

            $model = SProductVariantPriceTypeQuery::create()
                ->findByCurrencyId($currencyId);

        }

        $this->render(
            '_deleteWindow',
            [
             'currencies' => SCurrenciesQuery::create()
                        ->setComment(__METHOD__)
                        ->find(),
             'currencyId' => $currencyId,
             'model'      => $model,
            ]
        );

    }

    /**
     * @param SCurrencies $model
     */
    private function deleteCurrencyAndRelated(SCurrencies $model) {

        if (self::isPremiumCMS()) {

            $id = (int) $this->input->post('id');
            $moveOrDelete = (int) $this->input->post('moveOrDelete');
            $moveTo = (int) $this->input->post('CurrencySelectId');

            $priceTypes = SProductVariantPriceTypeQuery::create()
                ->findByCurrencyId($id);

            switch ($moveOrDelete) {
                case 2 :

                    foreach ($priceTypes as $priceType) {

                        $priceType->delete();
                    }

                    $this->lib_admin->log(lang('Currency and related price types removed', 'admin') . '. Id: ' . $id . ' ' . lang('Name', 'admin') . ' - ' . $model->getName());
                    break;

                case 1 :

                    foreach ($priceTypes as $priceType) {

                        $priceType->setCurrencyId($moveTo);
                        $priceType->save();
                    }

                    $this->lib_admin->log(lang('Currency was removed', 'admin') . '. Id: ' . $id . ' ' . lang('Name', 'admin') . ' - ' . $model->getName());
                    break;

                default :

                    $this->lib_admin->log(lang('Orders status was removed', 'admin') . '. Id: ' . $id . ' ' . lang('Name', 'admin') . ' - ' . $model->getName());
            }

            $model->delete();

        } else {

            $model->delete();
            $this->lib_admin->log(lang('Orders status was removed', 'admin') . '. Id: ' . $model->getId() . ' ' . lang('Name', 'admin') . ' - ' . $model->getName());
        }
    }

    /**
     * Delete currency
     *
     * @access public
     */
    public function delete() {
        $model = SCurrenciesQuery::create()
            ->findPk($this->input->post('id'));

        if ($model !== null) {

            if ($model->getMain() == true) {
                $response = showMessage(lang('Unable to remove the main currency', 'admin'), false, 'r', true);
                echo json_encode(['response' => $response]);
                exit;
            }

            $paymentMethodsCount = SPaymentMethodsQuery::create()->setComment(__METHOD__)->filterByCurrencyId($model->getId())->count();

            if ($paymentMethodsCount > 0) {
                $response = showMessage(lang('Unable to remove currency. The currency used in the payment methods.', 'admin'), false, 'r', true);
                echo json_encode(['response' => $response]);
                exit;
            }

            $payments = $this->db->like('name', 'payment_method_')->get('shop_settings');
            if ($payments) {
                $payments = $payments->result_array();
                foreach ($payments as $payment) {
                    $paymentData = unserialize($payment['value']);
                    if ($paymentData['merchant_currency'] == $model->getId()) {
                        $response = showMessage(lang('Unable to remove currency. The currency used in the payment methods.', 'admin'), false, 'r', true);
                        echo json_encode(['response' => $response]);
                        exit;
                    }
                }
            }

            $productVariantsCount = SProductVariantsQuery::create()
                ->setComment(__METHOD__)
                ->filterByCurrency($model->getId())
                ->count();

            if ($productVariantsCount > 0) {
                $response = showMessage(lang('Error. The currency used in the products. Check the currency options products', 'admin'), false, 'r', true);
                $showRecount = true;
                echo json_encode(['response' => $response, 'recount' => $showRecount, 'id' => $model->getId()]);
                exit;
            }

            $this->deleteCurrencyAndRelated($model);

            $response = showMessage(lang('Currency successfully removed', 'admin'), false, '', true);
            echo json_encode(['response' => $response, 'success' => true]);
            exit;
        }
    }

    public function recount() {

        $id = (int) $this->input->post('id');
        $main_id = \Currency\Currency::create()->main->id;
        $this->db->query('UPDATE `shop_product_variants` SET `price_in_main` = `price` WHERE `currency` =' . $id);
        $this->db->where('currency', $id)->update('shop_product_variants', ['currency' => $main_id]);
        showMessage(lang('Conversion completed. Now the currency may be removed', 'admin'));
    }

    public function checkPrices() {
        if (\Currency\Currency::create()->checkPrices()) {
            showMessage(lang('Prices updated', 'admin'));
        } else {
            showMessage(lang('There was no price for the upgrade', 'admin'));
        }
    }

    /**
     * Validate currency rate
     * @param $rate - rate value
     * @return bool
     */
    public function rate_validate($rate) {
        $rate = str_replace('.', '', $rate);

        if (is_numeric($rate)) {
            return TRUE;
        } else {
            $this->form_validation->set_message('rate_validate', lang('The field %s must be numeric', 'admin'));
            return FALSE;
        }
    }

}