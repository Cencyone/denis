<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * User Profile Controller
 *
 * @uses ShopController
 * @package Shop
 * @version 0.1
 * @copyright 2013 Siteimage
 * @author <dev@imagecms.net>
 * @deprecated since 4.9 use shop/profile
 */
class Profileapi extends ShopController
{

    protected $_userId = null;

    public function __construct() {
        parent::__construct();

        if (!$this->dx_auth->is_logged_in()) {
            redirect('/');
        }
        $this->_userId = $this->dx_auth->get_user_id();
    }

    /**
     * @deprecated since 4.9 use shop/profile::index
     * @todo change links in all templates shop/profileapi/changeInfo -> shop/profile
     * @return string|json
     */
    public function changeInfo() {
        return $this->load->module('shop/profile')->index();
    }

}

/* End of file profile.php */