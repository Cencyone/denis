<?php

namespace Products;

use Map\SProductsTableMap;
use MY_Controller;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Exception\PropelException;
use SCategoryQuery;
use ShopController;
use ShopCore;
use SProducts;
use SProductsQuery;

(defined('BASEPATH')) OR exit('No direct script access allowed');

/**
 * Shop Controller
 *
 * @uses \ShopController
 * @package Shop
 * @copyright 2013 ImageCMS
 * @property SProducts $model
 */
class BaseProducts extends ShopController
{

    public $data;

    /**
     * @var SProducts
     */
    public $model;

    public $productPath;

    public $templateFile = 'product';

    /**
     * BaseProducts constructor.
     */
    public function __construct() {

        parent::__construct();

        $this->productPath = $this->uri->segment($this->uri->total_segments());
        try {

            $cache_key = md5($this->productPath . MY_Controller::getCurrentLocale());

            if ($this->getCache()->contains($cache_key)) {

                $this->model = $this->getCache()->fetch($cache_key);

            } else {

                $this->model = SProductsQuery::create()
                    ->joinWithI18n(MY_Controller::getCurrentLocale())
                    ->joinMainCategory()
                    ->where('MainCategory.Active=?', 1)
                    ->filterByUrl($this->productPath)
                    ->filterByActive(true)
                    ->findOne();
                $this->getCache()->save($cache_key, $this->model, config_item('cache_ttl'));

            }
        } catch (PropelException $exc) {
            show_error($exc->getMessage());
        }

        if (!$this->model or !$this->areAllParentCategoriesActive($this->model)) {
            $this->core->error_404();
        }

        ShopCore::$currentCategory = $this->model->getMainCategory();

        $this->__CMSCore__();
        $this->index();
        exit;

    }

    /**
     * @param SProducts $model
     * @return bool
     * @throws PropelException
     */
    private function areAllParentCategoriesActive(SProducts $model) {

        $parentIds = unserialize($model->getMainCategory()->getFullPathIds());

        return SCategoryQuery::create()
            ->filterByActive(false)
            ->filterById($parentIds, Criteria::IN)
            ->count() == 0;
    }

    /**
     * Display product info.
     *
     * @access public
     */
    public function __CMSCore__() {

        /** Start. Set public Core Data */
        $this->core->core_data['data_type'] = 'product';
        $this->core->core_data['id'] = $this->model->getId();
        /** End. Set public Core Data */
        /** Start. Prepare public Data */
        $this->data['model'] = $this->model;
        $this->data['variants'] = count($this->model->getProductVariants()) ? $this->model->getProductVariants() : show_error('Bad Product');
        $this->data['accessories'] = $this->model->getRelatedProductsModels();

        if ($this->model->getTpl()) {
            $this->templateFile = file_exists('./templates/' . $this->config->item('template') . '/shop/' . $this->model->getTpl() . '.tpl') ? $this->model->getTpl() : 'product';
        }

        $category = SCategoryQuery::create()->setComment(__METHOD__)->findById($this->model->getCategoryId());
        $this->data['category_url'] = shop_url('category/' . $category[0]->getFullPath());
        /** End. Prepare public Data */
        /** Start. Increase views value for Product */
        $this->db->query('UPDATE `' . SProductsTableMap::TABLE_NAME . '` SET views = views + 1 WHERE id = ' . $this->model->getId());
        /** End. Increase views value for Product */
    }

}

/* End of file product.php _Admin_ ImageCms */