<?php

use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Exception\PropelException;

/**
 * ShopAdminOrderStatuses
 *
 * @property Lib_admin $lib_admin
 * @uses ShopAdminController
 * @package
 * @version $id$
 * @copyright
 * @author <dev@imagecms.net>
 * @license
 */
class ShopAdminOrderstatuses extends ShopAdminController
{

    /**
     * @var array|null
     */
    public $defaultLanguage = null;

    /**
     * @var string
     */
    public $defaultBackgroundColor = '#7d7c7d';

    /**
     * @var string
     */
    public $defaultFontColor = '#ffffff';

    public function __construct() {

        parent::__construct();

        \ShopController::checkVar();
        \ShopAdminController::checkVarAdmin();

        $this->defaultLanguage = getDefaultLanguage();
    }

    /**
     * Display all order statuses.
     *
     * @access public
     */
    public function index() {

        $model = SOrderStatusesQuery::create()
            ->joinWithI18n(\MY_Controller::defaultLocale(), Criteria::JOIN)
            ->orderByPosition()
            ->find();

        $statusesInUse = [];
        foreach (SOrdersQuery::create()->setComment(__METHOD__)->find() as $order) {
            $statusesInUse[$order->getStatus()] = $order->getStatus();
        }

        $this->render(
            'list',
            [
             'statusesInUse' => $statusesInUse,
             'model'         => $model,
             'locale'        => $this->defaultLanguage['identif'],
            ]
        );
    }

    /**
     * Create new order status.
     *
     * @access public
     */
    public function create() {

        $model = new SOrderStatuses;

        if ($this->input->post()) {

            $this->createDBFields();

            $this->form_validation->set_rules($model->rules());

            if ($this->form_validation->run($this) == FALSE) {
                showMessage(validation_errors());
            } else {
                $_POST['Color'] = $this->input->post('Color') ?: $this->defaultBackgroundColor;
                $_POST['Fontcolor'] = $this->input->post('Fontcolor') ?: $this->defaultFontColor;
                $model->fromArray($this->input->post());

                $posModel = SOrderStatusesQuery::create()
                    ->select('Position')
                    ->where('SOrderStatuses.Id != 2')
                    ->orderByPosition('Desc')
                    ->limit(1)
                    ->find();

                $model->setPosition($posModel[0] + 1);

                $model->save();

                $last_order_status_id = $this->db->order_by('id', 'desc')->get('shop_order_statuses')->row()->id;
                $this->lib_admin->log(lang('Order status established', 'admin') . '. Id: ' . $last_order_status_id);

                showMessage(lang('Order status created', 'admin'));

                $this->input->post('action') ? $action = $this->input->post('action') : $action = 'edit';
                if ($action == 'close') {
                    pjax('/admin/components/run/shop/orderstatuses/index');
                }
                if ($action == 'edit') {
                    pjax('/admin/components/run/shop/orderstatuses/edit/' . $model->getId());
                }
            }
        } else {
            $this->template->registerCssFile('/templates/administrator/js/colorpicker/css/colorpicker.css', 'after');
            $this->template->registerJsFile('/templates/administrator/js/colorpicker/js/colorpicker.js', 'after');
            $this->render(
                'create',
                [
                 'model'  => $model,
                 'locale' => $this->defaultLanguage['identif'],
                ]
            );
        }
    }

    /**
     * Create Db fields if not exists
     */
    private function createDBFields() {

        if (!$this->db->field_exists('color', 'shop_order_statuses') && !$this->db->field_exists('fontcolor', 'shop_order_statuses')) {
            $this->load->dbforge();
            $fields = [
                       'color'     => [
                                       'type'       => 'VARCHAR',
                                       'constraint' => '255',
                                      ],
                       'fontcolor' => [
                                       'type'       => 'VARCHAR',
                                       'constraint' => '255',
                                      ],
                      ];
            $this->dbforge->add_column('shop_order_statuses', $fields);
        }
    }

    /**
     * Edit order satus by id.
     *
     * @access public
     * @param null|int $id
     * @param null|int $locale
     * @throws PropelException
     */
    public function edit($id = null, $locale = null) {

        $locale = $locale == null ? $this->defaultLanguage['identif'] : $locale;

        $model = SOrderStatusesQuery::create()
            ->findPk((int) $id);

        if ($model === null) {
            $this->error404(lang('Order Status not found', 'admin'));
        }

        if ($this->input->post()) {
            $this->createDBFields();

            $this->form_validation->set_rules($model->rules());

            if ($this->form_validation->run($this) == FALSE) {
                showMessage(validation_errors());
            } else {
                $_POST['Active'] = (boolean) $this->input->post('Active');
                $_POST['Locale'] = $locale;
                $_POST['Color'] = $this->input->post('Color') ?: $this->defaultBackgroundColor;
                $_POST['Fontcolor'] = $this->input->post('Fontcolor') ?: $this->defaultFontColor;

                $model->fromArray($this->input->post());
                $model->save();

                $this->lib_admin->log(lang('Order status edited', 'admin') . '. Id: ' . $id);
                showMessage(lang('Changes have been saved', 'admin'));

                $this->input->post('action') ? $action = $this->input->post('action') : $action = 'edit';
                if ($action == 'close') {
                    pjax('/admin/components/run/shop/orderstatuses/index');
                }
            }
        } else {
            $model->setLocale($locale);
            $this->template->registerCssFile('templates/administrator/js/colorpicker/css/colorpicker.css', 'after');
            $this->template->registerJsFile('templates/administrator/js/colorpicker/js/colorpicker.js', 'after');

            $this->render(
                'edit',
                [
                 'model'     => $model,
                 'languages' => ShopCore::$ci->cms_admin->get_langs(true),
                 'locale'    => $locale,
                ]
            );
        }
    }

    /**
     * Delete order status by id.
     *
     * @access public
     */
    public function delete() {

        $id = (int) $this->input->post('id');
        $moveOrDelete = (int) $this->input->post('moveOrDelete');
        $moveTo = (int) $this->input->post('moveTo')[0] ?: (int) $this->input->post('CategoryId');

        if ($id != 1 && $id != 2) {
            $model = SOrderStatusesQuery::create()
                ->findPk($id);

            if ($model) {
                $orders = SOrdersQuery::create()
                    ->filterByStatus($id)
                    ->find();

                if ($moveOrDelete === 2) {

                    if (count($orders)) {
                        foreach ($orders as $order) {
                            $order->delete();
                        }
                    }
                    $model->delete();
                    $this->lib_admin->log(lang('Status and related orders removed', 'admin') . '. Id: ' . $id);
                    showMessage(lang('Status and related orders removed', 'admin'));

                } elseif ($moveOrDelete === 1) {

                    /** Заменяет историю статусов в случае если удаляется статус, который не активный в списке заказов но есть в истории */
                        $ordersHistory = SOrderStatusHistoryQuery::create()
                            ->findByStatusId($id);
                    foreach ($ordersHistory as $orderHist) {
                        SOrderStatusHistoryQuery::create()
                        ->findOneById($orderHist->getId())
                        ->setStatusId($moveTo)
                        ->save();
                    }

                    foreach ($orders as $order) {
                        $order->setStatus($moveTo);
                        $order->save();
                        $this->db->where('order_id', $order->getId())
                            ->where('status_id', $id)
                            ->update('shop_orders_status_history', ['status_id' => $moveTo]);
                    }

                    $model->delete();
                    $this->lib_admin->log(lang('Orders status was removed', 'admin') . '. Id: ' . $id);
                    showMessage(lang('Status removed', 'admin'));

                } elseif ($moveOrDelete === 0) {

                    $model->delete();
                    $this->lib_admin->log(lang('Orders status was removed', 'admin') . '. Id: ' . $id);
                    showMessage(lang('Status removed', 'admin'));
                }
                pjax('/admin/components/run/shop/orderstatuses');
            } else {
                showMessage(lang('Such status does not exist', 'admin'), '', 'r');
            }
        } else {
            showMessage(lang('Unable to remove the base status', 'admin'));
        }
    }

    public function ajaxDeleteWindow($statusId) {

        /**
         * Показывает сколько заказов содержит єтот статус, в том числе и в истории
         * @var \Propel\Runtime\Collection\ObjectCollection  $orders
         */
        $orders = SOrderStatusHistoryQuery::create()
                ->findByStatusId($statusId);

        $this->render(
            '_deleteWindow',
            [
             'statuses' => SOrderStatusesQuery::create()->setComment(__METHOD__)->joinWithI18n(MY_Controller::defaultLocale())->find(),
             'statusId' => $statusId,
             'orders'   => $orders,
            ]
        );
    }

    /**
     * Save order satus positions.
     *
     * @access public
     */
    public function savePositions() {

        $positions = $this->input->post('positions');
        if (count($positions) == 0) {
            return false;
        }

        foreach ($positions as $key => $val) {
            $query = 'UPDATE `shop_order_statuses` SET `position`=' . $key . ' WHERE `id`=' . (int) $val . '; ';
            $this->db->query($query);
        }
        showMessage(lang('Positions saved successfully', 'admin'));
    }

}