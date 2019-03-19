<?php

namespace Profile;

use Propel\Runtime\ActiveQuery\Criteria;
use ShopController;
use SUserProfileQuery;

(defined('BASEPATH')) OR exit('No direct script access allowed');

/**
 * Shop Controller
 *
 * @uses \ShopController
 * @package Shop
 * @copyright 2013 ImageCMS
 * @property model SProducts
 * @deprecated since 4.9 can be removed now
 */
class BaseProfile extends ShopController
{

    public $data = null;

    public $model;

    protected $_userId = null;

    public $templateFile = 'profile';

    public function __construct() {
        parent::__construct();

        if (!$this->dx_auth->is_logged_in()) {
            redirect('/');
        }

        $this->_userId = $this->dx_auth->get_user_id();

        $this->__CMSCore__();
        $this->index();
        exit;
    }

    /**
     * Display product info.
     *
     * @access public
     */
    public function __CMSCore__() {
        $this->load->helper('Form');

        $this->core->set_meta_tags(lang('Profile'));

        if ($this->input->post()) {
            $errors = $this->_edit();
        }

        $profile = SUserProfileQuery::create()
            ->filterById($this->_userId)
            ->findOne();

        $user = $this->db
            ->where('id', $this->_userId)
            ->get('users')
            ->row_array();

        $orders = \SOrdersQuery::create()
            ->orderByDateCreated(Criteria::DESC)
            ->joinSOrderStatuses()
            ->filterByUserId($this->_userId)
            ->find();

        $this->data = [
                       'template' => $this->templateFile,
                       'orders'   => $orders,
                       'profile'  => $profile,
                       'user'     => $user,
                       'errors'   => $errors,
                      ];

    }

}

/* End of file product.php _Admin_ ImageCms */