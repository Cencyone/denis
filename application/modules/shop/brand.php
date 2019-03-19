<?php

use Brands\BaseBrands;
use CMSFactory\Events;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Shop Brands Controller
 *
 * @uses ShopController
 * @package Shop
 * @version 0.1
 * @copyright 2013 ImageCMS
 * @author <dev@imagecms.net>
 * @property SBrands $model
 */
class Brand extends BaseBrands
{

    public function __construct() {
        parent::__construct();
    }

    /**
     * Display list of brand products.
     */
    public function index() {
        if ($this->brandPath == 'brand') {
            $this->core->set_meta_tags(lang('Бренды'));
            Events::create()->registerEvent(null, 'brands:load');
            Events::runFactory();
            $this->renderImageList();
            $this->renderNamesList();
        }

        // Show 404 page if it needed
        $this->show404();

        // Remove brand description if do pagination
        if ($this->input->get('per_page')) {
            $this->model->setDescription(NULL);
        }

        $this->redirectToCorrectUrl();

        // Begin pagination
        $this->load->library('Pagination');
        $this->pagination = new SPagination();
        $paginationConfig['base_url'] = str_replace('/?', '?', shop_url('brand/' . $this->model->getUrl() . '/' . $this->category . SProductsQuery::getFilterQueryString()));
        $paginationConfig['total_rows'] = $this->data['totalProducts'];
        $paginationConfig['per_page'] = $this->perPage;
        $paginationConfig['last_link'] = ceil($paginationConfig['total_rows'] / $paginationConfig['per_page']);

        $paginationConfig['page_query_string'] = true;
        $paginationConfig['first_link'] = '1';
        $paginationConfig['num_links'] = 3;
        include_once "./templates/{$this->config->item('template')}/paginations.php";

        $this->pagination->initialize($paginationConfig);
        $this->data['pagination'] = $this->pagination->create_links();
        $this->data['page_number'] = $this->pagination->cur_page;
        // End pagination

        /* Seo block (canonical) */
        $onlyPagination = array_key_exists('per_page', ShopCore::$ORIGIN_GET) && count(ShopCore::$ORIGIN_GET) == 1;

        if (!$onlyPagination && strstr($this->input->server('REQUEST_URI'), '?')) {
            $this->template->registerCanonical(media_url($this->uri->uri_string()));
            $this->template->registerMeta('robots', 'noindex, follow');
        }

        $title = $this->model->getMetaTitle() == '' ? $this->model->getName() : $this->model->getMetaTitle();

        if ($this->model->getMetaDescription()) {
            $description = $this->model->getMetaDescription();
        } else {
            $description = $this->model->getName();
        }
        $this->core->set_meta_tags($title, $this->model->getMetaKeywords(), $description, $this->pagination->cur_page, 0);

        Events::create()->registerEvent($this->data, 'brand:load');
        Events::runFactory();

        $this->render($this->data['template'], $this->data);
    }

    /**
     * 301 redirect to correct brand URL
     */
    private function redirectToCorrectUrl() {
        $initialBrandUrl = site_url("shop/brand/{$this->model->getUrl()}");
        $fullBrandUrl = $this->category ? "$initialBrandUrl/{$this->category}" : $initialBrandUrl;
        $currentUrl = media_url($this->uri->uri_string());

        if ($currentUrl !== $fullBrandUrl) {
            redirect($initialBrandUrl, 'location', '301');
        }
    }

    /**
     * Render 404 error page if it is not brand page or not correct brand category
     */
    private function show404() {
        if ($this->model == NULL) {
            $this->core->error_404();
        }

        $fullBrandUrl = site_url("shop/brand/{$this->model->getUrl()}");
        $currentUrl = media_url($this->uri->uri_string());
        if ($currentUrl !== $fullBrandUrl && !count($this->data['products'])) {
            $this->core->error_404();
        }
    }

}

/* End of file brand.php */