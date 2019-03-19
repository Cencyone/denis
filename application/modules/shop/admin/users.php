<?php

use Propel\Runtime\ActiveQuery\Criteria;

/**
 * ShopAdminUsers
 *
 * @uses ShopAdminController
 * @package
 * @version $id$
 * @copyright
 * @author <dev@imagecms.net>
 * @license
 */
class ShopAdminUsers extends ShopAdminController
{

    protected $perPage = 15;

    protected $ordersPerPage = 6;

    public function __construct() {
        parent::__construct();

        \ShopController::checkVar();
        \ShopAdminController::checkVarAdmin();
        $this->perPage = $this->input->cookie('per_page') ? $this->input->cookie('per_page') : $this->perPage;
    }

    /**
     * Display all order statuses.
     *
     * @access public
     */
    public function index($offset = 0, $orderField = '', $orderCriteria = '') {
        $model = SUserProfileQuery::create();

        if ($this->input->get('name')) {
            $model = $model->where('SUserProfile.Name LIKE "%' . encode($this->input->get('name')) . '%"');
        }

        if ($this->input->get('dateCreated_f') && $this->input->get('dateCreated_t')) {
            $model = $model->where('FROM_UNIXTIME(SUserProfile.DateCreated, \'%Y-%m-%d\') >= ?', date('Y-m-d', strtotime($this->input->get('dateCreated_f'))));
            $model = $model->where('FROM_UNIXTIME(SUserProfile.DateCreated, \'%Y-%m-%d\') <= ?', date('Y-m-d', strtotime($this->input->get('dateCreated_t'))));
        }

        if ($this->input->get('email')) {
            $model = $model->where('SUserProfile.UserEmail LIKE "%' . encode($this->input->get('email')) . '%"');
        }

        if ($this->input->get('role')) {
            if ((int) $this->input->get('role') > 0) {
                $model = $model->filterByRoleId(encode($this->input->get('role')));
            }

            if ($this->input->get('role') == 'without') {
                $model = $model->where('SUserProfile.RoleId = 0 OR SUserProfile.RoleId IS NULL');
            }
        }

        if ($this->input->get('amout_f') != NULL && $this->input->get('amout_t') != NULL) {
            if ($this->input->get('amout_f')) {
                $amout_f = encode($this->input->get('amout_f'));
            } else {
                $amout_f = 0;
            }
            if ($this->input->get('amout_t')) {
                $amout_t = encode($this->input->get('amout_t'));
            } else {
                $amout_t = 0;
            }

            if ($amout_f < $amout_t) {
                $model = $model->where('SUserProfile.Amout > ?', $amout_f);
                $model = $model->where('SUserProfile.Amout < ?', $amout_t);
            }
        }

        if ($orderField !== '' && $orderCriteria !== '' && method_exists($model, 'filterBy' . $orderField)) {
            switch ($orderCriteria) {
                case 'ASC':
                    $model = $model->orderBy($orderField, Criteria::ASC);
                    $nextOrderCriteria = 'DESC';
                    break;

                case 'DESC':
                    $model = $model->orderBy($orderField, Criteria::DESC);
                    $nextOrderCriteria = 'ASC';
                    break;
            }
        } else {
            $model->orderById('asc');
        }

        // Count total users
        $totalUsers = $this->_count($model);

        $model = $model
            ->offset((int) $this->input->get('per_page'))
            ->limit($this->perPage)
            ->distinct()
            ->find();

        $getData = $this->input->get();
        unset($getData['per_page']);
        $queryString = '?' . http_build_query($getData);

        foreach ($model as $user) {
            $amountPurchases[$user->getId()] = 0;
            foreach (SOrdersQuery::create()->setComment(__METHOD__)->leftJoin('SOrderProducts')->distinct()->filterByUserId($user->getId())->find() as $order) {
                if ($order->getPaid() == TRUE) {
                    /* @var $order SOrders */
                    $amountPurchases[$user->getId()] = $order->getTotalPrice() + $order->getDeliveryPrice();
                }
            }
        }

        // Create pagination

        if ($totalUsers > $this->perPage) {
            $this->load->library('Pagination');

            $config['base_url'] = site_url('admin/components/run/shop/users/index/?' . http_build_query($this->input->get()));
            $config['container'] = 'shopAdminPage';
            $config['uri_segment'] = $this->uri->total_segments();
            $config['container'] = 'shopAdminPage';
            $config['page_query_string'] = true;
            $config['total_rows'] = $totalUsers;
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
            $config['first_tag_open'] = '<li>';
            $config['first_tag_close'] = '</li>';
            $config['last_tag_open'] = '<li>';
            $config['last_tag_close'] = '</li>';

            $this->pagination->num_links = 5;
            $this->pagination->initialize($config);
            $this->template->assign('paginator', $this->pagination->create_links_ajax());
        }
        $usersDatas = [];
        foreach ($model as $o) {
            $usersDatas[] = $o->getFullName();
            $usersDatas[] = $o->getUserEmail();
            $usersDatas[] = $o->getDateCreated();
        }
        $usersDatas = array_unique($usersDatas);
        $roles = [];
        foreach ((array) $this->roles() as $role) {
            $roles[$role->id] = $role->alt_name;
        }

        $this->setBackUrl();
        echo $this->render(
            'list',
            [
             'model'             => $model,
             'amountPurchases'   => $amountPurchases,
             'totalUsers'        => $totalUsers,
             'nextOrderCriteria' => $nextOrderCriteria,
             'orderField'        => $orderField,
             'queryString'       => $queryString,
             'usersDatas'        => $usersDatas,
             'filter_url'        => http_build_query($this->input->get()),
             'cur_uri_str'       => base64_encode($this->uri->uri_string() . '?' . http_build_query($this->input->get())),
             'roles'             => $roles,
            ]
        );
    }

    public function search($offset = 0, $orderField = '', $orderCriteria = '') {

        $model = SOrdersQuery::create();

        if (is_numeric($this->input->get('status_id')) && ($this->input->get('status_id') != '-- none --')) {
            $model = $model->filterByStatus($this->input->get('status_id'));
        }

        if ($this->input->get('order_id')) {
            $model = $model->where('SOrders.Id = ?', $this->input->get('order_id'));
        }

        if ($this->input->get('created_from')) {
            $model = $model->where('FROM_UNIXTIME(SOrders.DateCreated, \'%Y-%m-%d\') = ?', date('Y-m-d', strtotime($this->input->get('date_from'))));
        }

        if ($this->input->get('created_to')) {
            $model = $model->where('FROM_UNIXTIME(SOrders.DateCreated, \'%Y-%m-%d\') <= ?', date('Y-m-d', strtotime($this->input->get('date_to'))));
        }

        if ($this->input->get('dateCreated_f') && $this->input->get('dateCreated_t')) {
            $model = $model->where('FROM_UNIXTIME(SUserProfile.DateCreated, \'%Y-%m-%d\') >= ?', date('Y-m-d', strtotime($this->input->get('dateCreated_f'))));
            $model = $model->where('FROM_UNIXTIME(SUserProfile.DateCreated, \'%Y-%m-%d\') <= ?', date('Y-m-d', strtotime($this->input->get('dateCreated_t'))));
        }

        if ($this->input->get('amout_f') && $this->input->get('amout_t')) {
            $model = $model->where('SUserProfile.Amout > ?', encode($this->input->get('amout_f')));
            $model = $model->where('SUserProfile.Amout < ?', encode($this->input->get('amout_t')));
        }

        if ($this->input->get('customer')) {
            $model->_or()
                ->where('SOrders.UserFullName LIKE ?', '%' . $this->input->get('customer') . '%')
                ->_or()
                ->where('SOrders.UserEmail LIKE ?', '%' . $this->input->get('customer') . '%')
                ->_or()
                ->where('SOrders.UserPhone LIKE ?', '%' . $this->input->get('customer') . '%');
        }

        if ($this->input->get('amount_from')) {
            $model->where('SOrders.TotalPrice >= ?', $this->input->get('amount_from'));
        }

        if ($this->input->get('amount_to')) {
            $model->where('SOrders.TotalPrice <= ?', $this->input->get('amount_to'));
        }

        if (is_numeric($this->input->get('paid')) && ($this->input->get('paid') != '-- none --')) {
            if (!$this->input->get('paid')) {
                $model->where('SOrders.Paid IS NULL');
            }
        } else {
            $model = $model->filterByPaid(true);
        }

        // Count total orders
        $totalOrders = $this->_count($model);

        $nextOrderCriteria = '';

        if ($orderField !== '' && $orderCriteria !== '' && method_exists($model, 'filterBy' . $orderField)) {
            switch ($orderCriteria) {
                case 'ASC':
                    $model = $model->orderBy($orderField, Criteria::ASC);
                    $nextOrderCriteria = 'DESC';
                    break;

                case 'DESC':
                    $model = $model->orderBy($orderField, Criteria::DESC);
                    $nextOrderCriteria = 'ASC';
                    break;
            }
        } else {
            $model->orderById('desc');
        }

        $model = $model
            ->limit($this->perPage)
            ->offset((int) $offset)
            ->distinct()
            ->find();

        $getData = $this->input->get();
        unset($getData['per_page']);
        $queryString = '?' . http_build_query($getData);

        $orderStatuses = SOrderStatusesQuery::create()->setComment(__METHOD__)->orderByPosition(Criteria::ASC)->find();

        $usersDatas = [];
        foreach ($model as $o) {
            $usersDatas[] = $o->getUserFullName();
            $usersDatas[] = $o->getUserEmail();
            $usersDatas[] = $o->getUserPhone();
        }

        $usersDatas = array_unique($usersDatas);

        // Create pagination
        $this->load->library('pagination');
        $config['base_url'] = $this->createUrl('orders/search/');
        $config['container'] = 'shopAdminPage';
        $config['uri_segment'] = 7;
        $config['total_rows'] = $totalOrders;
        $config['per_page'] = $this->perPage;
        $this->pagination->num_links = 6;
        $config['suffix'] = ($orderField != '') ? $orderField . '/' . $orderCriteria . $queryString : $queryString;
        $this->pagination->initialize($config);

        $this->render(
            'list',
            [
             'model'             => $model,
             'pagination'        => $this->pagination->create_links_ajax(),
             'totalOrders'       => $totalOrders,
             'nextOrderCriteria' => $nextOrderCriteria,
             'orderField'        => $orderField,
             'queryString'       => $queryString,
             'deliveryMethods'   => SDeliveryMethodsQuery::create()->setComment(__METHOD__)->find(),
             'paymentMethods'    => SPaymentMethodsQuery::create()->setComment(__METHOD__)->find(),
             'orderStatuses'     => $orderStatuses,
             'usersDatas'        => $usersDatas,
            ]
        );
    }

    private function roles() {
        $this->db->select('shop_rbac_roles.*', FALSE);
        $this->db->select('shop_rbac_roles_i18n.alt_name', FALSE);
        $this->db->where('locale', MY_Controller::getCurrentLocale());
        $this->db->join('shop_rbac_roles_i18n', 'shop_rbac_roles_i18n.id = shop_rbac_roles.id');
        $roles = $this->db->get('shop_rbac_roles')->result();
        return $roles;
    }

    /**
     * Create new user.
     *
     * @access public
     */
    public function create() {

        $model = new SUserProfile();

        if ($this->input->post()) {
            $this->load->model('dx_auth/users', 'user2');
            $val = $this->form_validation->set_rules($model->rules('create'));
            $this->form_validation->set_rules('Phone', lang('Phone'), 'trim|min_length[5]|max_length[20]|xss_clean|callback_check_phone');
            $val = $model->validateCustomData($val);

            if (!$val->run()) {
                showMessage(validation_errors(), '', 'r');
            } else {

                $email = $this->input->post('UserEmail');
                $role = $this->input->post('Role');
                // check user mail
                if ($this->user2->check_email($email)->num_rows() > 0) {
                    showMessage(lang('A user with this email is already registered.', 'admin'), '', 'r');
                    exit;
                }

                $this->load->helper('string');
                $key = random_string('alnum', 5);
                if (ShopCore::$ci->dx_auth->register($val->set_value('Name'), $val->set_value('Password'), $val->set_value('UserEmail'), $val->set_value('Address'), $key, $val->set_value('Phone'), FALSE)) {
                    $user_info = ShopCore::$ci->user2->get_user_by_email($email)->row_array();

                    $model = SUserProfileQuery::create()
                            ->findOneById($user_info['id']);

                    $model->setRoleId($role);
                    $model->setKey($key);
                    $model->setPhone($this->input->post('Phone'));
                    $model->setAddress($this->input->post('Address'));

                    $model->save();

                    /** Init Event. Create Shop user */
                    \CMSFactory\Events::create()->registerEvent(
                        ['user' => $model],
                        'ShopAdminUser:create'
                    );
                    \CMSFactory\Events::runFactory();

                    //set user role
                    $this->user2->set_role($user_info['id'], $role);

                    //$this->lib_admin->log(lang('Created by', 'admin') . ' ' . $val->set_value('Login'));

                    $last_user_id = $this->db->order_by('id', 'desc')->get('users')->row()->id;
                    $this->lib_admin->log(lang('User created', 'admin') . '. Id: ' . $last_user_id);
                    showMessage(lang('User created', 'admin'));

                    $action = $this->input->post('action');
                    if ($action == 'close') {
                        pjax('/admin/components/run/shop/users/edit/' . $model->getId());
                    } else {
                        pjax('/admin/components/run/shop/users/index');
                    }
                } else {
                    showMessage(validation_errors(), '', 'r');
                }
            }
        } else {

            $this->render(
                'create',
                [
                 'model' => $model,
                 'roles' => $this->roles(),
                ]
            );
        }
    }

    /**
     * Edit order satus by id.
     *
     * @access public
     */
    public function edit($id, $offset = 0, $ordersList = null) {

        $model = SUserProfileQuery::create()
                ->filterById((int) $id)
                ->findOne();

        if ($model === null) {
            $this->error404(lang('User not found', 'admin'));
        }

        if ($this->input->post()) {
            $validation = $this->form_validation->set_rules($model->rules('edit'));

            if (strlen($this->input->post('new_pass')) !== 0) {
                $this->form_validation->set_rules('new_pass', lang('New password', 'admin'), 'trim|min_length[5]|max_length[50]|xss_clean');
                $this->form_validation->set_rules('new_pass_conf', lang('New password confirm', 'admin'), 'required|matches[new_pass]');
            }
            if ($this->input->post('new_pass_conf')) {
                $this->form_validation->set_rules('new_pass', lang('New password', 'admin'), 'required');
            }

            //$this->form_validation->set_rules('Phone', lang('Phone'), 'trim|min_length[5]|max_length[20]|xss_clean|callback_check_phone');

            $validation = $model->validateCustomData($validation);

            if ($validation->run($this) == FALSE) {
                showMessage(validation_errors(), '', 'r');
            } else {

                $postData = $this->input->post();

                if ($this->input->post('new_pass')) {
                    $noCryptPassword = $this->input->post('new_pass');
                    $postData['Password'] = crypt(CI::$APP->dx_auth->_encode($postData['new_pass']));
                }

                unset($postData['new_pass_conf']);
                unset($postData['new_pass']);

                $model->fromArray($postData);
                $model->save();

                $this->lib_admin->log(
                    lang('Shop', 'admin') . ' - ' . lang('Changes have been saved', 'admin') .
                    '<a href="' . site_url('/admin/components/run/shop/users/edit/' . $id) . '">' . $model->getFullName() . '</a>'
                );

                $replaceData = [
                                'user_name' => $this->input->post('Name'),
                                'password'  => $noCryptPassword,
                               ];

                if ($replaceData['password'] != NULL) {
                    \cmsemail\email::getInstance()->sendEmail($this->input->post('UserEmail'), 'change_password', $replaceData);
                }

                CMSFactory\Events::create()->registerEvent(['model' => $model], 'ShopAdminUsers:afterEdit');
                CMSFactory\Events::create()->runFactory();

                $this->lib_admin->log(lang('User edited', 'admin') . '. Id: ' . $id);
                showMessage(lang('Changes have been saved', 'admin'));

                $action = $this->input->post('action');

                if ($action == 'close') {
                    pjax('/admin/components/run/shop/users/edit/' . $id);
                } else {
                    pjax($this->getBackUrl());
                }
            }
        } else {
            CMSFactory\Events::create()->registerEvent(['model' => $model], 'ShopAdminUsers:beforeEdit');
            CMSFactory\Events::create()->runFactory();
            $model->reload();

            $amountPurchases = 0;
            foreach (SOrdersQuery::create()->setComment(__METHOD__)->leftJoin('SOrderProducts')->distinct()->filterByUserId($id)->find() as $order) {
                if ($order->getPaid() == TRUE) {
                    foreach ($order->getSOrderProductss() as $p) {
                        $amountPurchases += $p->getQuantity() * $p->getPrice();
                    }
                    $amountPurchases += $order->getDeliveryPrice();
                }
            }
            $wishListData = unserialize($model->getWishListData());

            if (is_array($wishListData)) {
                $newData = [];
                $newCollection = [];
                $ids = array_map('array_shift', $wishListData);

                if (count($ids) > 0) {
                    // Load products
                    $collection = SProductsQuery::create()
                            ->findPks(array_unique($ids));

                    $collectionCount = count($collection);
                    for ($i = 0; $i < $collectionCount; $i++) {
                        $newCollection[$collection[$i]->getId()] = $collection[$i];
                    }

                    foreach ($wishListData as $key => $item) {
                        if ($newCollection[$item[0]] !== null) {
                            $item['model'] = $newCollection[$item[0]];
                            $productVariant = SProductVariantsQuery::create()->setComment(__METHOD__)->filterById($item[1])->findOne();
                            $item['variantName'] = $productVariant->getName();
                            $item['price'] = money_format('%i', $productVariant->getPrice());
                            $newData[$key] = $item;
                        }
                    }
                }
            }

            $ordersModel = SOrdersQuery::create()
                    ->orderById('desc')
                    ->filterByUserId($id);

            // Count total orders
            $totalOrders = $this->_count($ordersModel);

            $ordersModel = $ordersModel
                ->distinct()
                ->limit($this->ordersPerPage)
                ->offset((int) $offset)
                ->find();

            $orderStatuses = SOrderStatusesQuery::create()->setComment(__METHOD__)->joinWithI18n(MY_Controller::defaultLocale())->orderByPosition(Criteria::ASC)->find();

            // Create pagination
            $this->load->library('pagination');
            $config['base_url'] = $this->createUrl('users/edit/' . $id . '/');
            $config['container'] = 'shopEditUserOrders';
            $config['uri_segment'] = 8;
            $config['total_rows'] = $totalOrders;
            $config['per_page'] = $this->ordersPerPage;
            //$config['page_query_string'] = TRUE;
            $this->pagination->num_links = 6;
            $config['suffix'] = 'true';
            $this->pagination->initialize($config);

            if ($ordersList) {
                $this->render(
                    'edit_orders_list',
                    [
                     'ordersModel'   => $ordersModel,
                     'orderStatuses' => $orderStatuses,
                     'pagination'    => $this->pagination->create_links_ajax(),
                    ]
                );
            } else {

                $this->render(
                    'edit',
                    [
                     'model'           => $model,
                     'amountPurchases' => $amountPurchases,
                     'newData'         => $newData,
                     'ordersModel'     => $ordersModel,
                     'orderStatuses'   => $orderStatuses,
                     'pagination'      => $this->pagination->create_links_ajax(),
                     'roles'           => $this->roles(),
                     'back_url'        => $this->getBackUrl(),
                     'current_user'    => $this->dx_auth->get_user_id(),
                    ]
                );
            }
        }
    }

    private function getBackUrl() {
        $users_back_url = $this->session->userdata('users_back_url') ? $this->session->userdata('users_back_url') : site_url('/admin/components/run/shop/users');
        return $users_back_url;
    }

    private function setBackUrl() {
        $previous = $this->input->server('REQUEST_URI');
        $users_back_url = (strstr($previous, 'shop/users') && !strstr($previous, 'shop/users/edit')) ? $previous : site_url('/admin/components/run/shop/users');
        $this->session->set_userdata('users_back_url', $users_back_url);
    }

    public function check_phone($value) {
        $value = trim($value);
        if ($value) {
            if (preg_match_all('/^[\+\-0-9\(\)\, ]{5,20}$/', $value)) {
                return $value;
            } else {
                $this->form_validation->set_message('phone_check', lang('Wrong phone format', 'saas'));
                return FALSE;
            }
        }
    }

    /**
     * Delete user by id.
     *
     * @access public
     */
    public function deleteAll() {
        if (!$this->input->post('ids')) {
            showMessage(lang('No data transmitted', 'admin'), '', 'r');
            exit;
        }
        if (count($this->input->post('ids')) > 0) {
            $model = SUserProfileQuery::create()
                    ->findPks($this->input->post('ids'));

            if (!empty($model)) {
                foreach ($model as $order) {
                    $order->delete();
                }
                $this->lib_admin->log(lang('User deleted', 'admin') . '. Ids: ' . implode(', ', $this->input->post('ids')));
                showMessage(lang('Members removed', 'admin'));
            }
        }
    }

    /**
     * Count total elements in the list
     *
     * @param $object
     * @return int
     */
    protected function _count($object) {
        $object = clone $object;
        return $object->count();
    }

    public function auto_complite($type) {

        $s_limit = $this->input->get('limit');
        $s_coef = $this->input->get('term');

        $model = SUserProfileQuery::create();

        if ($type == 'name') {
            $model = $model->where('SUserProfile.Name LIKE "%' . $s_coef . '%"');
        } else {
            $model = $model->where('SUserProfile.UserEmail LIKE "%' . $s_coef . '%"');
        }
        $model = $model
            ->limit($s_limit)
            ->find();

        foreach ($model as $product) {

            if ($type == 'name') {
                $response[] = [
                               'value' => ShopCore::encode($product->getName()),
                              ];
            } elseif ($type == 'email') {
                $response[] = [
                               'value' => ShopCore::encode($product->getUserEmail()),
                              ];
            }
        }
        echo json_encode($response);
    }

    /**
     * Set user role
     * @param $userId
     * @param $roleId
     */
    public function setRoleId() {
        $userId = $this->input->post('userId');
        $roleId = $this->input->post('roleId');

        $users = SUserProfileQuery::create()
                ->findById($userId);

        if (!$users) {
            return FALSE;
        }

        foreach ($users as $user) {
            $user->setRoleId($roleId);
        }

        $users->save();
        if ($this->input->is_ajax_request()) {
            echo json_encode(['success' => TRUE, 'message' => lang('User role changed.', 'admin')]);
        } else {
            return TRUE;
        }
    }

}