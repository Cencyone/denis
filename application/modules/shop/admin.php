<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Admin
 * @uses Controller
 * @package Shop
 * @version $id$
 * @copyright 2015 ImageCMS
 * @author <dev@imagecms.net>
 * @license
 */
class Admin extends ShopController
{

    public function __construct() {
        parent::__construct();
        //cp_check_perm('module_admin');
        // Load all categories in admin panel.
        ShopCore::app()->SCategoryTree->loadUnactive = true;

        $adminController = $this->uri->segment(5);
        $adminClassName = 'ShopAdmin' . ucfirst($adminController);
        $adminMethod = $this->uri->segment(6);
        $adminClassFile = SHOP_DIR . 'admin' . DS . $adminController . '.php';

        if (file_exists($adminClassFile)) {
            if (!$adminMethod) {
                $adminMethod = 'index';
            }

            include $adminClassFile;

            $controller = new $adminClassName;

            $this->load->module('core');
            $args = $this->core->grab_variables(7);

            // Redirect all requests to the appropriate controller,
            // othewise we'll get 404.
            if (method_exists($controller, $adminMethod)) {
                call_user_func_array([$controller, $adminMethod], $args);
                exit;
            }
        }
    }

    /**
     * Create and display ul list of shop categories.
     *
     * @access public
     */
    public function ajaxCategoriesTree() {
        ShopCore::app()->SAdminSidebarRenderer->render();
    }

}

/* End of file admin.php */