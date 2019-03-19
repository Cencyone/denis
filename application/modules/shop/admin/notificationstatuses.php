<?php

use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Exception\PropelException;

/**
 * ShopAdminNotificationStatuses
 *
 * @property Lib_admin lib_admin
 * @property Cms_admin cms_admin
 * @uses ShopAdminController
 * @package
 * @version $id$
 * @copyright
 * @author <dev@imagecms.net>
 * @license
 */
class ShopAdminNotificationstatuses extends ShopAdminController
{

    public $defaultLanguage = null;

    public function __construct() {
        parent::__construct();

        ShopController::checkVar();
        ShopAdminController::checkVarAdmin();

        $this->defaultLanguage = getDefaultLanguage();
    }

    /**
     * Display all notification statuses.
     *
     * @access public
     */
    public function index() {
        $model = SNotificationStatusesQuery::create()->setComment(__METHOD__)->joinWithI18n(MY_Controller::defaultLocale(), Criteria::JOIN)
                ->orderByPosition()
                ->find();

        $statusesInUse = [];

        foreach (SNotificationsQuery::create()->setComment(__METHOD__)->find() as $notification) {
            $statusesInUse[$notification->getStatus()] = $notification->getStatus();
        }

        $this->render(
            'list',
            [
             'statusesInUse' => $statusesInUse,
             'model'         => $model,
            ]
        );
    }

    /**
     * Create new notification status.
     *
     * @access public
     */
    public function create() {
        $model = new SNotificationStatuses();

        if ($this->input->post()) {
            $this->form_validation->set_rules($model->rules());

            if ($this->form_validation->run($this) == FALSE) {
                showMessage(validation_errors(), '', 'r');
            } else {
                $model->fromArray($this->input->post());

                $posModel = SNotificationStatusesQuery::create()
                        ->select('Position')
                        ->orderByPosition('Desc')
                        ->limit(1)
                        ->find();

                $model->setPosition($posModel[0] + 1);

                $model->save();

                $last_stat_pending_id = $this->db->order_by('id', 'desc')->get('shop_notification_statuses')->row()->id;
                $this->lib_admin->log(lang('Pendings status was created', 'admin') . '. Id: ' . $last_stat_pending_id);
                showMessage(lang('Pendings status was created', 'admin'));

                if ($this->input->post('action') == 'back') {
                    pjax('/admin/components/run/shop/notificationstatuses');
                } else {
                    pjax('/admin/components/run/shop/notificationstatuses/edit/' . $model->getId());
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
     * Edit notification status by id.
     *
     * @access public
     * @param null|int $id
     * @param null|string $locale
     * @throws PropelException
     */
    public function edit($id = null, $locale = null) {
        $locale = $locale == null ? MY_Controller::defaultLocale() : $locale;
        $model = SNotificationStatusesQuery::create()
                ->joinWithI18n($locale)
                ->findPk((int) $id);

        if ($model === null) {
            $this->error404(lang('Status pending not found', 'admin'));
        }

        if ($this->input->post()) {
            $this->form_validation->set_rules($model->rules());

            if ($this->form_validation->run($this) == FALSE) {
                showMessage(validation_errors(), '', 'r');
            } else {
                $_POST['Active'] = (boolean) $this->input->post('Active');
                $_POST['Locale'] = $locale;

                $model->fromArray($this->input->post());
                $model->save();

                $this->lib_admin->log(lang('Status pending edited', 'admin') . '. Id: ' . $id);
                //showMessage(lang('Changes have been saved', 'admin'));
                showMessage(lang('Changes were saved', 'admin'));

                //$this->_redirect($model, $locale);
                $active = $this->input->post('action');
                if ($active == 'edit') {
                    pjax('/admin/components/run/shop/notificationstatuses/edit/' . $id);
                } else {
                    pjax('/admin/components/run/shop/notificationstatuses');
                }
            }
        } else {
            $model->setLocale($locale);

            $this->render(
                'edit',
                [
                 'model'     => $model,
                 'languages' => $this->cms_admin->get_langs(true),
                 'locale'    => $locale,
                ]
            );
        }
    }

    /**
     * Delete status by id.
     *
     * @access public
     */
    public function deleteAll() {
        if (!is_array($this->input->post('ids')) || !(count($this->input->post('ids')) > 0)) {
            showMessage(lang('No data transmitted', 'admin'), '', 'r');
            exit;
        }

        $notifStDel = $this->input->post('ids');

        $notifStDb = $this->db
            ->order_by('position')
            ->get('shop_notification_statuses')
            ->result_array();

        if ((count($notifStDb) == 1) && (count($notifStDel) == 1)) {
            showMessage(lang('The last status in the list can not be deleted', 'admin'), '', 'r');
            exit;
        }

        if (count($notifStDel) == count($notifStDb)) {
            array_shift($notifStDel);
        }

        $model = SNotificationStatusesQuery::create()
                ->findPks($notifStDel);

        if (!empty($model)) {
            foreach ($model as $order) {
                $order->delete();
            }

            $this->lib_admin->log(lang('Status pending removed', 'admin') . '. Ids: ' . implode(', ', $this->input->post('ids')));
            showMessage(lang('Arrival notification status was deleted', 'admin'));
        }
    }

    /**
     * Save notification status positions.
     *
     * @access public
     * @throws Exception
     */
    public function savePositions() {

        if (count($this->input->post('positions')) > 0) {

            foreach ($this->input->post('positions') as $id => $pos) {
                SNotificationStatusesQuery::create()
                        ->filterById($pos)
                        ->update(['Position' => (int) $id]);
            }
            showMessage(lang('Positions saved', 'admin'));
        }
    }

}