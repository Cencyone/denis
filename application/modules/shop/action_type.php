<?php

use CMSFactory\Events;
use Search\BaseSearch;
use Search\ActionSearch;


/**
 * Class Action_type
 */
class Action_type extends BaseSearch
{

    /**
     * Action_type constructor.
     */
    public function __construct() {

        parent::__construct();
        $this->setLocate(MY_Controller::getCurrentLocale());

        $this->core->core_data['data_type'] = 'action_type';
    }

    /**
     * Показывает все товары где есть хиты, акции, и горячие предложения в записимости от переданого параметра $type
     * @param string $type
     */
    public function show($type = 'all') {
        $action = new ActionSearch($this->getLocate());

        $this->load->library('Pagination');
        $this->pagination = new SPagination();

        $get_param = array_filter($this->input->get());
        $per_page  = ShopCore::app()->SSettings->getFrontProductsPerPage();
        $get_param['user_per_page'] = $get_param['user_per_page'] ?:$per_page;

        $action->setGet_Param($get_param);

        $searchObj = $action->getActionsProducts($type);

        ShopCore::$_GET['searchSetting'] = 1;

        if (!$this->input->get('order')) {
            /** Присваиваю значение $_GET  поскольку есть нюанс в условии на шаблоне(FullMarket -> catalog_header.tpl) */
            $_GET['order'] = 'none';
            ShopCore::$_GET['order'] = 'none';
        }
        $searchObj['order_method'] = ShopCore::$_GET['order'];
        /** Begin Pagination */
        $paginationConfig['base_url'] = shop_url('action_type/show/' .$type.'?'. http_build_query($get_param));
        $paginationConfig['total_rows'] = $searchObj['totalProducts'];
        $paginationConfig['per_page'] = $get_param['user_per_page'];
        $paginationConfig['page_query_string'] = TRUE;
        $paginationConfig['first_link'] = '1';
        $paginationConfig['num_links'] = 3;
        include_once "./templates/{$this->config->item('template')}/paginations.php";

        $this->pagination->initialize($paginationConfig);
        $searchObj['pagination'] = $this->pagination->create_links();
        $searchObj['type'] = strtolower($action->getType());
        $searchObj['settings'] = 'actionsSearch';

        /** End Pagination */

        $this->template->registerMeta('ROBOTS', 'NOINDEX, FOLLOW');
        $this->core->set_meta_tags(lang('Search'), '', '', $this->pagination->cur_page);

        /** Register event 'search:load' */
        Events::create()->raiseEvent($searchObj, 'Action_Type:preSearch');
        $this->render('bestseller', $searchObj);

    }

}