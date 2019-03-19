<?php

(defined('BASEPATH')) OR exit('No direct script access allowed');

use CMSFactory\Events;
use core\src\Exception\PageNotFoundException;
use Map\SProductsTableMap;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Exception\PropelException;

/**
 * Shop Product Controller
 * @package Shop
 * @copyright 2013 ImageCMS
 * @author <dev@imagecms.net>
 * @property SProducts $model
 * @property Mod_discount $mod_discount
 */
class Product extends ShopController
{

    public $data;

    /**
     * @var SProducts
     */
    public $model;

    public $productPath;

    public $templateFile = 'product';

    /**
     * Display product
     *
     * @param $id
     * @throws PageNotFoundException
     */
    public function index($id) {

        $product = $this->getProduct($id);

        if (!$product or !$this->areAllParentCategoriesActive($product)) {
            throw new PageNotFoundException();
        }

        $this->setData($product);

        $this->setMetaTags($product);

        Events::create()->registerEvent($this->data, 'product:load');
        Events::runFactory();

        \CMSFactory\assetManager::create()->setData($this->data)->render($this->templateFile);

    }

    private function setMetaTags($product) {

        if ($product->getMetaDescription()) {
            $description = $product->getMetaDescription();
        } else {
            $description = $product->getId() . ' - ' . $product->getName() . mb_substr(strip_tags($product->getFullDescription()), 0, 100, 'utf-8');
        }

        if ($product->getMetaKeywords()) {
            $keywords = $product->getMetaKeywords();
        } else {
            $keywords = strip_tags($product->getMainCategory()->getName());
        }

        if ($product->getMetaTitle()) {
            $this->core->set_meta_tags($product->getMetaTitle(), $keywords, $description, '', 0);
        } else {
            $this->core->set_meta_tags($product->getName(), $keywords, $description, '', 0, $product->getMainCategory()->getName());
        }
    }

    /**
     * @param $id
     * @return false|mixed|SProducts|
     */
    private function getProduct($id) {
        try {

            $this->productPath = $this->uri->segment($this->uri->total_segments());
            $cache_key = md5($this->productPath . MY_Controller::getCurrentLocale());

            if ($this->getCache()->contains($cache_key)) {
                $model = $this->getCache()->fetch($cache_key);
            } else {

                $model = SProductsQuery::create()
                    ->joinWithI18n(MY_Controller::getCurrentLocale())
                    ->joinMainCategory()
                    ->where('MainCategory.Active=?', 1)
                    ->filterById($id)
                    ->filterByActive(true)
                    ->findOne();
                $this->getCache()->save($cache_key, $model, config_item('cache_ttl'));

            }
            return $model;
        } catch (PropelException $exc) {
            show_error($exc->getMessage());
        }

    }

    /**
     * @param SProducts $product
     */
    private function setData($product) {

        $this->data['model'] = $product;
        $this->data['variants'] = count($product->getProductVariants()) ? $product->getProductVariants() : show_error('Bad Product');
        $this->data['accessories'] = $product->getRelatedProductsModels();

        if ($product->getTpl()) {
            $this->templateFile = file_exists('./templates/' . $this->config->item('template') . '/shop/' . $product->getTpl() . '.tpl') ? $product->getTpl() : 'product';
        }

        $this->db->query('UPDATE `' . SProductsTableMap::TABLE_NAME . '` SET views = views + 1 WHERE id = ' . $product->getId());

        $this->data['delivery_methods'] = SDeliveryMethodsQuery::create()
            ->joinWithI18n(MY_Controller::getCurrentLocale())
            ->filterByEnabled(1)
            ->orderByPosition()
            ->find();

        $this->data['payments_methods'] = SPaymentMethodsQuery::create()
            ->joinWithI18n(MY_Controller::getCurrentLocale())
            ->filterByActive(1)
            ->orderByPosition()
            ->find();

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

}