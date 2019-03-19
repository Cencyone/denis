<?php

(defined('BASEPATH')) OR exit('No direct script access allowed');

use CMSFactory\assetManager;
use CMSFactory\Events;
use Category\BaseCategory;
use core\src\CoreFactory;
use core\src\Exception\PageNotFoundException;
use Propel\Runtime\ActiveQuery\Criteria;

/**
 * @property SCategory $categoryModel
 */
class Category extends ShopController
{

    /**
     * @var BaseCategory
     */
    private $categoryClass;

    /**
     * @var string
     */
    private $templateFile;

    /**
     * @var array
     */
    public $data;

    /**
     * @var int
     */
    private $perPage;

    /**
     * @var string
     */
    public $categoryPath;

    public function index($id) {

        Events::create()->raiseEvent(['categoryObj' => $this], BaseCategory::EVENT_CATEGORY_PRELOAD);
        $category = $this->getCategory($id);
        if (!$category) {
            throw new PageNotFoundException();
        }

        try {

            $this->categoryClass = new BaseCategory($category);

            $this->setData();
            $this->registerSeoIndex();

            /* Set meta tags */
            $this->core->set_meta_tags(
                $category->makePageTitle(),
                $category->makePageKeywords(),
                $category->makePageDesc(),
                $this->data['page_number'],
                $category->getShowsitetitle()
            );

            /** Register event 'category:load' */
            Events::create()->raiseEvent($this, BaseCategory::EVENT_CATEGORY_LOAD);

            /** Render template */
            $this->render($this->templateFile, $this->data);
        } catch (Exception $ex) {
            log_message('error', $ex->getMessage());
            if (ENVIRONMENT === 'production') {
                $this->core->error_404();
            } else {
                echo $ex->getMessage();
            }
        }
    }

    private function setData() {
        $this->perPage = (int) ShopCore::$_GET['user_per_page'] ?: ShopCore::app()->SSettings->getFrontProductsPerPage();

        if ($this->categoryClass->getCategory()->getTpl() === '') {
            $this->templateFile = 'category';
        } else {
            if (file_exists('./templates/' . $this->config->item('template') . '/shop/' . $this->categoryClass->getCategory()->getTpl() . '.tpl')) {
                $this->templateFile = $this->categoryClass->getCategory()->getTpl();
            } else {
                $this->templateFile = 'category';
            }
        }
        $this->data = $this->categoryClass->getProducts($this->perPage, (int) ShopCore::$_GET['per_page'], ShopCore::$_GET['order']);

        $basePath = rtrim(media_url(), '/') . '/' . CI::$APP->uri->uri_string() . SProductsQuery::getFilterQueryString();
        $this->categoryClass->installPagination($this->data, $basePath, $this->perPage);
    }

    /**
     * if only filter static page skip
     * if filter gets register
     * if count products per page register
     * if ordering register
     */
    private function registerSeoIndex() {

        $count = function ($get_p) {
            $count = 0;
            foreach ($get_p as $values) {
                $count += count($values);
            }
            return $count;
        };

        $countBrands = array_key_exists('brand', ShopCore::$ORIGIN_GET) ? count(ShopCore::$ORIGIN_GET['brand']) : 0;
        $countProperties = array_key_exists('p', ShopCore::$ORIGIN_GET) ? $count(ShopCore::$ORIGIN_GET['p']) : 0;

        if ($countBrands + $countProperties > 1) {
            $register = true;
        }

        $defaults = [];
        $defaults['order'] = $this->categoryClass->getDefaultSort();
        $defaults['lp'] = assetManager::create()->getData('minPrice');
        $defaults['rp'] = assetManager::create()->getData('maxPrice');
        $defaults['user_per_page'] = ShopCore::app()->SSettings->getFrontProductsPerPage();

        foreach ($defaults as $name => $value) {
            if (array_key_exists($name, ShopCore::$ORIGIN_GET) && ShopCore::$ORIGIN_GET[$name] != $value) {
                $register = true;
            }

        }

        if ($register) {
            $this->template->registerCanonical(site_url(CoreFactory::getUrlParser()->getUrl()));
            $this->template->registerMeta('robots', 'noindex, follow');
        }

    }

    /**
     *
     * @param $id
     * @return null|SCategory
     */
    private function getCategory($id) {

        $category = SCategoryQuery::create()
            ->joinWithI18n(self::getCurrentLocale(), Criteria::INNER_JOIN)
            ->withColumn('IF(H1 IS NOT NULL AND H1 NOT LIKE "", H1, Name)', 'title')
            ->filterById($id)
            ->filterByActive(true)
            ->findOne();

        return $category;

    }

}