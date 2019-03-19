<?php

use CMSFactory\Events;
use Propel\Runtime\ActiveQuery\Criteria;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * User Profile Controller
 *
 * @uses ShopController
 * @package Shop
 * @version 0.1
 * @copyright 2010 Siteimage
 * @author <dev@imagecms.net>
 */
class Profile extends ShopController
{

    public $data = null;

    public $model;

    protected $_userId = null;

    public $templateFile = 'profile';

    /**
     * Profile constructor.
     */
    public function __construct() {
        parent::__construct();
        $this->core->core_data['data_type'] = 'profile';
        if (!$this->dx_auth->is_logged_in()) {
            redirect('/');
        }

        $this->_userId = $this->dx_auth->get_user_id();

        $this->init();
    }

    /**
     * Display list of user order
     *
     * @access public
     */
    public function index() {
        if ($this->input->post()) {
            $profile = SUserProfileQuery::create()->setComment(__METHOD__)->filterById($this->_userId)->findOne();
            $this->setValidationRules();

            if ($this->input->is_ajax_request()) {
                return $this->ajaxChangeInfo($profile);
            }

            $this->postChangeInfo($profile);
        }
        $this->render($this->data['template'], $this->data);
    }

    public function profile_change_pass() {
        $this->render_min('profile_change_pass', $this->data);

    }

    public function profile_data() {
        $this->render_min('profile_data', $this->data);

    }

    public function profile_history() {
        $this->render_min('profile_history', $this->data);
    }

    public function postChangeInfo(SUserProfile $profile) {
        $profile->validateCustomData($this->form_validation);
        if ($this->form_validation->run($this)) {
            $this->fillProfile($profile);
            $this->session->set_flashdata(['success' => lang('User profile successfully changed')]);
        }
        redirect(site_url('shop/profile'));

    }

    /**
     * @param $profile
     */
    protected function ajaxChangeInfo($profile) {
        $profile->validateCustomData($this->form_validation);
        if (!$this->form_validation->run($this)) {
            echo json_encode(
                [
                 'msg'         => validation_errors(),
                 'validations' => $this->form_validation->getErrorsArray(),
                 'status'      => false,
                 'refresh'     => $this->input->post('refresh') ?: FALSE,
                 'redirect'    => $this->input->post('redirect') ?: FALSE,
                ]
            );
        } else {

            $this->fillProfile($profile);

            echo json_encode(
                [
                 'msg'      => lang('User profile successfully changed'),
                 'status'   => true,
                 'refresh'  => $this->input->post('refresh') ?: FALSE,
                 'redirect' => $this->input->post('redirect') ?: FALSE,
                ]
            );
        }
    }

    protected function fillProfile(SUserProfile $profile) {
        $profile->setName($this->input->post('name'));
        $profile->setUserEmail($this->input->post('email'));
        $profile->setAddress($this->input->post('address'));
        $profile->setPhone($this->input->post('phone'));
        $profile->save();
        Events::create()->registerEvent(['model' => $profile], 'ProfileApi:changeInfo');
        Events::create()->runFactory();
    }

    /**
     *
     */
    protected function setValidationRules() {
        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters(FALSE, FALSE);
        $this->form_validation->set_message('phone', lang('numeric'));
        $this->form_validation->set_rules('name', '<b>' . Fields::Name() . '</b>', 'trim|required|xss_clean|min_length[4]');
        $this->form_validation->set_rules('email', '<b>' . Fields::Email() . '</b>', 'trim|required|xss_clean|valid_email');
        $this->form_validation->set_rules('address', '<b>' . Fields::ShippingAddress() . '</b>', 'trim|xss_clean');
        $this->form_validation->set_rules('phone', '<b>' . Fields::Phone() . '</b>', 'trim|required|xss_clean|phone');
    }

    /**
     * Display product info.
     *
     * @access public
     */
    protected function init() {
        $this->load->helper('Form');

        $this->core->set_meta_tags(lang('Profile'));

        $profile = SUserProfileQuery::create()
            ->setComment(__METHOD__)
            ->filterById($this->_userId)
            ->findOne();

        $user = $this->db
            ->where('id', $this->_userId)
            ->get('users')
            ->row_array();

        $orders = \SOrdersQuery::create()
            ->setComment(__METHOD__)
            ->orderByDateCreated(Criteria::DESC)
            ->joinSOrderStatuses()
            ->filterByUserId($this->_userId)
            ->find();

        $this->data = [
                       'template' => $this->templateFile,
                       'orders'   => $orders,
                       'profile'  => $profile,
                       'user'     => $user,
                      ];

    }

}
/* End of file profile.php */