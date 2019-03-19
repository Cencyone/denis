<?php

use cmsemail\email;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Exception\PropelException;

/**
 * ShopAdminNotifications
 *
 * @property Lib_admin lib_admin
 * @uses ShopController
 * @package
 * @version $id$
 * @copyright 2010 Siteimage
 * @author <dev@imagecms.net>
 * @license
 */
class ShopAdminNotifications extends ShopAdminController
{

    protected $perPage = 10;

    public function __construct() {

        parent::__construct();

        \ShopController::checkVar();
        \ShopAdminController::checkVarAdmin();

        $this->defaultLanguage = getDefaultLanguage();
        $this->load->helper('Form');
        $this->avalaibleComments = $this->db->get_where('components', ['name' => 'comments'])->row() ? false : TRUE;
    }

    /**
     * Display notifications list
     *
     * @access public
     * @param null|int $statusId
     * @param int $offset
     * @param string $orderField
     * @param string $orderCriteria
     */
    public function index($statusId = NULL, $offset = 0, $orderField = '', $orderCriteria = '') {

        $model = SNotificationsQuery::create()
            ->_if($statusId && $statusId != 'All')
            ->filterByStatus($statusId)
            ->_endif()
            ->joinSProductVariants(null, 'left join')
            ->joinSProducts(null, 'left join');

        if ($orderField !== '' && $orderCriteria !== '' && (method_exists($model, 'filterBy' . $orderField) || $orderField == 'SProductVariants.Stock' || $orderField == 'SProducts.Name')) {
            switch ($orderCriteria) {
                case 'ASC':
                    $nextOrderCriteria = 'DESC';
                    $model = $model->orderBy($orderField, Criteria::ASC);
                    break;

                case 'DESC':
                    $nextOrderCriteria = 'ASC';
                    $model = $model->orderBy($orderField, Criteria::DESC);
                    break;
                default :
                    $model = $model->orderById(Criteria::DESC);
                    break;
            }
        } else {
            $model = $model->orderById(Criteria::DESC);
        }

        $totalNotifications = $this->_count($model);

        $model = $model
            ->distinct()
            ->limit($this->perPage)
            ->offset((int) $offset)
            ->find();

        $notificationStatuses = SNotificationStatusesQuery::create()->setComment(__METHOD__)->joinWithI18n($this->defaultLanguage['identif'], Criteria::LEFT_JOIN)->orderByPosition(Criteria::ASC)->find();
        // Create pagination
        $this->load->library('pagination');
        $config['base_url'] = $this->createUrl('notifications/index/' . ($statusId ?: 'All') . '/' . ShopCore::$_GET['status'] . '/');
        $config['total_rows'] = $totalNotifications;
        $config['container'] = 'shopAdminPage';
        $config['uri_segment'] = 8;
        $config['per_page'] = $this->perPage;

        $config['separate_controls'] = true;
        $config['full_tag_open'] = '<div class="pagination pull-left"><ul>';
        $config['full_tag_close'] = '</ul></div>';
        $config['controls_tag_open'] = '<div class="pagination pull-right"><ul>';
        $config['controls_tag_close'] = '</ul></div>';
        $config['next_link'] = lang('Next', 'admin') . '&nbsp;&gt;';
        $config['prev_link'] = '&lt;&nbsp;' . lang('Prev', 'admin');
        $config['cur_tag_open'] = '<li class="btn-primary active"><span>';
        $config['cur_tag_close'] = '</span></li>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['num_tag_close'] = '</li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';

        $this->pagination->num_links = 6;
        $this->pagination->initialize($config);
        foreach ($model as $variant) {
            $quantity = SProductVariantsQuery::create()->setComment(__METHOD__)->filterById($variant->getVariantId())->findOne();
            if ($quantity) {
                $productsQuaintity[$variant->getVariantId()] = $quantity->getStock();
                $variantsName[$variant->getVariantId()] = $quantity->getName();
            }
        }

        $emails = [];
        foreach ($this->db->select('user_email')->get('shop_notifications')->result_array() as $mail) {
            $emails[] = $mail['user_email'];
        }

        /**
         * Make hash for pagination links
         */
        if ($statusId) {
            $hash = 'notification_' . $statusId;
        } else {
            $hash = 'notification_all';
        }
        $pagination = $this->pagination->create_links_ajax();
        $pagination = preg_replace('/href="(.*?)"/', 'href="$1#' . $hash . '"', $pagination);

        $this->render(
            'list',
            [
             'model'                => $model,
             'pagination'           => $pagination,
             'totalNotifications'   => $totalNotifications,
             'nextOrderCriteria'    => $nextOrderCriteria,
             'orderField'           => $orderField,
             'notificationStatuses' => $notificationStatuses,
             'productsQuaintity'    => $productsQuaintity,
             'emails'               => array_values(array_unique($emails)),
             'variantsName'         => $variantsName,
            ]
        );
    }

    /**
     * Edit order info
     *
     * @access public
     * @param int $id
     * @throws PropelException
     */
    public function edit($id) {

        $model = SNotificationsQuery::create()
            ->findPk((int) $id);

        if ($model === null) {
            $this->error404(lang('Notification is not found', 'admin'));
        }
        $ci = get_instance();
        $ci->load->model('dx_auth/users', 'users');

        if ($this->input->post()) {
            $model->setStatus($this->input->post('Status'));
            $model->setActiveTo(strtotime($this->input->post('ActiveTo')));
            $model->setManagerId($this->dx_auth->get_user_id());
            $model->setUserComment($this->input->post('UserComment'));
            $model->setUserPhone($this->input->post('UserPhone'));
            $model->save();
        } else {
            if ($query = $ci->users->get_user_by_id($model->getManagerId()) AND $query->num_rows() == 1) {
                $row = $query->row();
                $managerName = $row->username;
            } else {
                $managerName = lang('Manager does not exist or is not set', 'admin');
            }

            $notificationStatuses = SNotificationStatusesQuery::create()->setComment(__METHOD__)->joinWithI18n(MY_Controller::defaultLocale(), Criteria::LEFT_JOIN)->find();

            $product = SProductsQuery::create()->setComment(__METHOD__)->findPk($model->getProductId());
            $variant = SProductVariantsQuery::create()
                ->joinI18n('locale', $this->defaultLanguage['identif'])
                ->findPk($model->getVariantId());
            $this->render(
                'edit',
                [
                 'product'              => $product,
                 'variant'              => $variant,
                 'notificationStatuses' => $notificationStatuses,
                 'model'                => $model,
                 'managerName'          => $managerName,
                ]
            );
        }

        if ($this->input->post()) {
            $this->lib_admin->log(lang('Notification updated', 'admin') . '. Id: ' . $id);
            showMessage(lang('Notification updated', 'admin'));
            $action = $this->input->post('action');
            if ($action == 'edit') {
                pjax('/admin/components/run/shop/notifications/edit/' . $id);
            } else {
                pjax('/admin/components/run/shop/notifications');
            }
        }
    }

    /**
     * @param int $notificationId
     * @param int $statusId
     * @throws PropelException
     */
    public function changeStatus($notificationId, $statusId) {

        $model = SNotificationsQuery::create()
            ->findPk($notificationId);

        $newStatusId = SNotificationStatusesQuery::create()->setComment(__METHOD__)->joinWithI18n(MY_Controller::defaultLocale())->findPk((int) $statusId);
        if (!empty($newStatusId)) {
            if ($model !== null) {
                $model->setStatus($statusId);
                $model->setManagerId($this->dx_auth->get_user_id());
                $model->save();

                //                "Статус уведомления о появлении изменён на Выполнен. Id:X"
                $message = lang('Notification status changed to', 'admin') . ' ' . $newStatusId->getName() . '. '
                    . lang('Id:', 'admin') . ' '
                    . $notificationId;
                $this->lib_admin->log($message);
                showMessage(lang('Notification status updated', 'admin'));
            }
        }
    }

    /**
     * @param int $notificationId
     * @throws PropelException
     */
    public function notifyByEmail($notificationId) {

        $model = SNotificationsQuery::create()
            ->findPk($notificationId);

        if ($model->getNotifiedByEmail() != true) {
            $model->setNotifiedByEmail(true);
            $model->save();
        }

        $product = SProductsQuery::create()->setComment(__METHOD__)->findPk($model->getProductId());
        if ($product) {
            email::getInstance()->sendEmail(
                $model->getUserEmail(),
                'notification_email',
                [
                 'status'      => $model->getSNotificationStatuses()->getName(),
                 'userName'    => $model->getUserName(),
                 'userEmail'   => $model->getUserEmail(),
                 'productName' => $product->getName(),
                 'productLink' => site_url($product->getRouteUrl()),
                ]
            );
        }
        showMessage(lang('User is notified by E-mail', 'admin') . '( ' . $model->getUserEmail() . ' )');
        pjax('/admin/components/run/shop/notifications');
    }

    public function deleteAll() {

        if (!$this->input->post('ids')) {
            showMessage(lang('No data transmitted', 'admin'), '', 'r');
            exit;
        }
        if (count($this->input->post('ids')) > 0) {

            $model = SNotificationsQuery::create()
                ->findPks($this->input->post('ids'));

            if (!empty($model)) {
                foreach ($model as $order) {
                    $order->delete();
                }

                $this->lib_admin->log(lang('Notifications Removed', 'admin') . '. Ids: ' . implode(', ', $this->input->post('ids')));
                showMessage(lang('Uninstalling', 'admin'));
            }
        }
    }

    public function ajaxDeleteNotifications() {

        if (count($this->input->post('ids')) > 0) {
            $model = SNotificationsQuery::create()
                ->findPks($this->input->post('ids'));

            if (!empty($model)) {
                foreach ($model as $notification) {
                    $notification->delete();
                }
            }
        }
    }

    /**
     * @param int $status
     */
    public function ajaxChangeNotificationsStatus($status) {

        if (count($this->input->post('ids')) > 0) {
            $model = SNotificationsQuery::create()
                ->findPks($this->input->post('ids'));
            $newStatusId = SNotificationStatusesQuery::create()->findPk((int) $status);
            if (!empty($newStatusId)) {
                if (!empty($model)) {
                    foreach ($model as $notification) {
                        $notification->setManagerId($this->dx_auth->get_user_id());
                        $notification->setStatus((int) $status);
                        $notification->save();
                    }
                    showMessage(lang('Order status changed', 'admin'));
                }
            }
        }
    }

    /**
     * @param int $offset
     * @param string $orderField
     * @param string $orderCriteria
     */
    public function search($offset = 0, $orderField = '', $orderCriteria = '') {

        if ($offset == 0 && $this->input->get('per_page')) {
            $offset = $this->input->get('per_page');
        }

        $model = SNotificationsQuery::create()->setComment(__METHOD__)->joinSProductVariants(null, Criteria::LEFT_JOIN)->joinSProducts(null, Criteria::LEFT_JOIN);

        if (ShopCore::$_GET['status_id'] != null) {
            $model = $model->filterByStatus(ShopCore::$_GET['status_id']);
        }

        if (ShopCore::$_GET['notification_id']) {
            $model = $model->filterById(ShopCore::$_GET['notification_id']);
        }

        if (ShopCore::$_GET['user_email']) {
            $model = $model->filterByUserEmail('%' . ShopCore::$_GET['user_email'] . '%', Criteria::LIKE);
        }

        if (ShopCore::$_GET['user_phone']) {
            $model = $model->where('SNotifications.UserPhone LIKE "%' . encode(ShopCore::$_GET['user_phone']) . '%"');
        }

        if (ShopCore::$_GET['created'] && !ShopCore::$_GET['actual']) {
            $model = $model->where('SNotifications.DateCreated >= ?', strtotime(ShopCore::$_GET['created']));
        } elseif (!ShopCore::$_GET['created'] && ShopCore::$_GET['actual']) {
            $model = $model->where('SNotifications.ActiveTo <= ?', strtotime(ShopCore::$_GET['actual']));
        } elseif (ShopCore::$_GET['created'] && ShopCore::$_GET['actual']) {
            $model = $model->where('SNotifications.DateCreated >= ?', strtotime(ShopCore::$_GET['created']));
            $model = $model->where('SNotifications.ActiveTo <= ?', strtotime(ShopCore::$_GET['actual']));
        }

        // Count total notifications
        $totalNotifications = $this->_count($model);

        $nextOrderCriteria = '';

        if ($orderField !== '' && $orderCriteria !== '' && (method_exists($model, 'filterBy' . $orderField) || $orderField == 'SProductVariants.Stock' || $orderField == 'SProducts.Name')) {
            switch ($orderCriteria) {
                case 'ASC':
                    $nextOrderCriteria = 'DESC';
                    $model = $model->orderBy($orderField, Criteria::ASC);
                    break;

                case 'DESC':
                    $nextOrderCriteria = 'ASC';
                    $model = $model->orderBy($orderField, Criteria::DESC);
                    break;
            }
        } else {
            $model->orderBy('NotifiedByEmail', Criteria::ASC);
            $model->orderBy('SProductVariants.Stock', Criteria::DESC);
        }

        $model = $model
            ->limit($this->perPage)
            ->offset((int) $offset)
            ->distinct()
            ->find();

        $getData = ShopCore::$_GET;
        unset($getData['per_page']);
        $queryString = '?' . urlencode(http_build_query($getData));

        $notificationStatuses = SNotificationStatusesQuery::create()
            ->joinWithI18n(MY_Controller::defaultLocale(), Criteria::LEFT_JOIN)
            ->orderByPosition(Criteria::ASC)
            ->find();

        $this->load->library('pagination');
        $config['base_url'] = $this->createUrl('notifications/search/?' . $this->input->server('QUERY_STRING'));
        $config['total_rows'] = $totalNotifications;
        $config['container'] = 'shopAdminPage';
        $config['uri_segment'] = 7;
        $config['per_page'] = $this->perPage;

        $config['separate_controls'] = true;
        $config['full_tag_open'] = '<div class="pagination pull-left"><ul>';
        $config['full_tag_close'] = '</ul></div>';
        $config['controls_tag_open'] = '<div class="pagination pull-right"><ul>';
        $config['controls_tag_close'] = '</ul></div>';
        $config['next_link'] = lang('Next', 'admin') . '&nbsp;&gt;';
        $config['prev_link'] = '&lt;&nbsp;' . lang('Prev', 'admin');
        $config['cur_tag_open'] = '<li class="btn-primary active"><span>';
        $config['cur_tag_close'] = '</span></li>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['num_tag_close'] = '</li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';

        $config['page_query_string'] = TRUE;
        $this->pagination->num_links = 6;
        $this->pagination->initialize($config);

        foreach ($model as $variant) {
            $quantity = SProductVariantsQuery::create()->setComment(__METHOD__)->filterById($variant->getVariantId())->findOne();
            if ($quantity) {
                $productsQuaintity[$variant->getVariantId()] = $quantity->getStock();
            }
        }

        $emails = [];
        foreach ($this->db->select('user_email')->get('shop_notifications')->result_array() as $mail) {
            $emails[] = $mail['user_email'];
        }

        ShopCore::$_GET['status'] = -1;

        echo $this->render(
            'list',
            [
             'model'                => $model,
             'pagination'           => $this->pagination->create_links_ajax(),
             'totalNotifications'   => $totalNotifications,
             'nextOrderCriteria'    => $nextOrderCriteria,
             'orderField'           => $orderField,
             'queryString'          => $queryString,
             'notificationStatuses' => $notificationStatuses,
             'productsQuaintity'    => $productsQuaintity,
             'emails'               => array_values(array_unique($emails)),
            ]
        );
    }

    /**
     * Count total notifications in the list
     *
     * @param SNotificationsQuery $object
     * @return int
     */
    protected function _count(SNotificationsQuery $object) {

        $object = clone $object;
        return $object->count();
    }

}