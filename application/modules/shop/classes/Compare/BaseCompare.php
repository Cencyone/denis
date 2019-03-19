<?php

namespace Compare;

use Propel\Runtime\Collection\ObjectCollection;

(defined('BASEPATH')) OR exit('No direct script access allowed');

/**
 * Shop Controller
 *
 * @uses \ShopController
 * @package Shop
 * @copyright 2013 ImageCMS
 * @property model SProducts
 */
class BaseCompare extends \ShopController
{

    public $data = null;

    public $model;

    public $templateFile = 'compare';

    public function __construct() {

        parent::__construct();
        $this->__CMSCore__();
    }

    /**
     * Display product info.
     *
     * @access public
     */
    public function __CMSCore__() {

        $this->core->set_meta_tags(lang('Compare list'));
        $this->template->registerMeta('ROBOTS', 'NOINDEX, NOFOLLOW');
        $this->data = [
                       'template'   => $this->templateFile,
                       'products'   => $this->_loadProducts(),
                       'cart_data'  => \ShopCore::app()->SCart->getData(),
                       'categories' => $this->_loadCategorys(),
                      ];
    }

    /**
     * Load categories
     * @return array
     */
    protected function _loadCategorys() {

        $ids = \SProductsQuery::create()
            ->select('CategoryId')
            ->distinct()
            ->findPks($this->_getData())
            ->toArray();

        return \SCategoryQuery::create()
            ->filterById($ids)
            ->joinWithI18n(\MY_Controller::getCurrentLocale())
            ->find()
            ->toArray();
    }

    /**
     * Add product to compare
     * @param integer $productId
     */
    public function add($productId = null) {

        $model = \SProductsQuery::create()
            ->findPk($productId);

        if ($model !== null) {
            $data = $this->_getData();

            if (!is_array($data)) {
                $data = [];
            }

            if (!in_array($model->getId(), $data)) {
                $data[] = $model->getId();
                $this->session->set_userdata('shopForCompare', $data);
            }
        }

        if ($this->session->userdata('HTTP_X_REQUESTED_WITH') != 'XMLHttpRequest') {
            if ($this->input->server('HTTP_REFERER')) {
                redirect(site_url($this->input->server('HTTP_REFERER')), 'location', '301');
            } else {
                redirect(site_url(), 'location', '301');
            }
        }

    }

    /**
     * Remove product from compare
     * @param integer $productId
     */
    public function remove($productId = null) {

        $data = $this->_getData();

        if (is_array($data)) {
            $key = array_search($productId, $data);

            if ($key !== false) {
                unset($data[$key]);
            }

            $this->session->set_userdata('shopForCompare', $data);
        }
        if ($this->session->userdata('HTTP_X_REQUESTED_WITH') != 'XMLHttpRequest') {
            if ($this->input->server('HTTP_REFERER')) {
                redirect($this->input->server('HTTP_REFERER'), 'location', '301');
            } else {
                redirect(site_url(), 'location', '301');
            }
        } else {
            echo 'success';
        }
    }

    /**
     * Select products
     * @return array|mixed|\Propel\Runtime\Collection\ObjectCollection
     */
    protected function _loadProducts() {

        $products = \SProductsQuery::create()
            ->findPks($this->_getData());

        return $products;
    }

    /**
     * Get data from session
     * @return array
     */
    protected function _getData() {

        return $this->session->userdata('shopForCompare');
    }

    /**
     *
     * @return string
     */
    public function calculate() {

        $ind = $this->input->post('ind');
        $val = $this->input->post('val');
        $rows = array_count_values($ind);
        foreach ($rows as $key => $value) {
            foreach ($ind as $k => $v) {
                if ($key == $v) {
                    $result[$key][] = $val[$k];
                }
            }
        }
        foreach ($result as $key => $value) {
            if (count(array_count_values($value)) == 1) {
                $fordelete[] = $key;
            }
        }
        if (count($fordelete) > 0) {
            $string = '';
            foreach ($fordelete as $k => $v) {
                $string .= '[data-row="' . $v . '"]';
                if ($k < (count($fordelete) - 1)) {
                    $string .= ' , ';
                }
            }
            $result = true;
        } else {
            $result = false;
            $string = false;
        }
        echo json_encode(['result' => $result, 'selector' => $string]);
    }

}