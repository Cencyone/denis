<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

use CMSFactory\Events;
use Search\BaseSearch;
use template_manager\classes\Params;
use template_manager\classes\Template;

/**
 * Search Controller
 *
 * @uses ShopController
 * @package Shop
 * @version 0.1
 * @copyright 2013 ImageCMS
 * @author <dev@imagecms.net>
 */
class Search extends BaseSearch
{

    const TEMPLATE_VERSION_USE_NEW_URL = 1.5;

    /**
     * @var string
     */
    private $locale;

    public function __construct() {

        parent::__construct();

        $this->setLocale(MY_Controller::getCurrentLocale());
    }

    /**
     * Display products list.
     * @access public
     * @return void
     */
    public function index() {

        /** Если не задан текст поиска идет перенаправление на главную страницу */
        if (!$this->input->get('text')) {
            redirect(site_url());
        }

        $this->core->core_data['data_type'] = 'shop_search';

        $this->load->library('Pagination');
        $this->pagination = new SPagination();

        $get_param = array_filter($this->input->get());

        $per_page  = ShopCore::app()->SSettings->getFrontProductsPerPage();
        $get_param['user_per_page'] = $get_param['user_per_page'] ?:$per_page;

        $this->setGet_Param($get_param);

        $search_str = trim($get_param['text']);

        /** Convert to string * */
        if (!is_string($search_str)) {
            $search_str = (string) $search_str;
        }

        $searchObj = $this
            ->getIndexProduct($search_str, $this->getLocale());

        ShopCore::$_GET['searchSetting'] = 1;

        if (!$this->input->get('order')) {
            ShopCore::$_GET['order'] = 'none';
        }

        $searchObj['order_method'] = ShopCore::$_GET['order'];

        /** Begin Pagination */
        $paginationConfig['base_url'] = shop_url('search/?' . http_build_query($get_param));
        $paginationConfig['total_rows'] = $searchObj['totalProducts'];
        $paginationConfig['per_page'] = $get_param['user_per_page'];
        $paginationConfig['page_query_string'] = TRUE;
        $paginationConfig['first_link'] = '1';
        $paginationConfig['num_links'] = 3;
        include_once "./templates/{$this->config->item('template')}/paginations.php";

        $this->pagination->initialize($paginationConfig);
        $searchObj['pagination'] = $this->pagination->create_links();
        /** End Pagination */

        $this->template->registerMeta('ROBOTS', 'NOINDEX, FOLLOW');
        $this->core->set_meta_tags(lang('Search'), '', '', $this->pagination->cur_page);

        /** Register event 'search:load' */
        Events::create()->registerEvent(['search_text' => $get_param['text']], 'ShopBaseSearch:preSearch');
        Events::create()->raiseEvent($searchObj, 'search:load');

        $this->render('search', $searchObj);
    }

    /**
     * @return string
     */
    private function getLocale() {

        return $this->locale;
    }

    /**
     * @param $locale
     * @return void
     */
    private function setLocale($locale) {

        $this->locale = $locale;
    }

    /**
     * Autocomplete search
     * @param null|string $locale
     * @return string
     */
    public function ac($locale = NULL) {

        $NextCS = $this->template->get_var('NextCS');
        $locale = $locale ?: $this->getLocale();
        $word = $this->input->post('queryString');

        $oldUrl = $this->isUsedOldUrl();

        if (mb_strlen($word) >= 3) {

            $resOBJ = $this->getAutoCompProduct($word, $locale);

            $x = count($resOBJ);
            $v = 0;

            while ($v < $x) {
                /** @var SProducts $var */
                $var = $resOBJ[$v];
                if ($var) {
                    $res[] = [
                              'product_id' => $var->getId(),
                              'name'       => $var->getName(),
                              'url'        => $oldUrl ? $var->getUrl() : $var->getRouteUrl(),
                              'mainImage'  => $var->getFirstVariant()->getMainPhoto(),
                              'smallImage' => $var->getFirstVariant()->getSmallPhoto(),
                              'price'      => (string) emmet_money($var->getFirstVariant()->getFinalPrice()),

                             ];

                    if ($NextCS != null) {

                        $res[$v]['nextCurrency'] = array_map('trim', emmet_money_additional($var->getFirstVariant()->getFinalPrice()));
                    }
                }
                unset($var);
                ++$v;
            }

            $res['queryString'] .= $word;

            return json_encode($res);
        } else {
            $this->core->error_404();
        }

    }

    /**
     * @return bool
     */
    private function isUsedOldUrl() {

        if ($this->getCache()->contains('usedOldSearchUrl')) {

            $usedOldUrl = $this->getCache()->fetch('usedOldSearchUrl');

        } else {

            $params = new Params($this->template->template_dir .  Template::PARAMS_FILE);

            $usedOldUrl = ((float) str_replace(',', '.', $params->getValue('version'))) < self::TEMPLATE_VERSION_USE_NEW_URL;

            $this->getCache()->save('usedOldSearchUrl', $usedOldUrl, config_item('cache_ttl'));
        }

        return $usedOldUrl;

    }

    /**
     * Indexation unique products words
     * @return void
     */
    public function updateIndexationWords() {

        $this->indexationWordProduct();

    }

}

/* End of file search.php */