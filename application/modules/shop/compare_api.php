<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Compare Controller
 *
 * @uses ShopController
 * @package Shop
 * @version 0.1
 * @copyright 2010 Siteimage
 * @author <dev@imagecms.net>
 */
class Compare_api extends ShopController
{

    public $forCompareIds = [];

    public function __construct() {

        parent::__construct();
    }

    /**
     * Load categories
     * @return array
     */
    protected function _loadCategorys() {

        $ids = SProductsQuery::create()
            ->select('CategoryId')
            ->distinct()
            ->findPks($this->_getData())
            ->toArray();

        return SCategoryQuery::create()
            ->filterById($ids)
            ->find()
            ->toArray();
    }

    /**
     * Add product to compare
     * @param integer $productId
     */
    public function add($productId = null) {

        if ($this->ajaxRequest) {

            $response = [

                         'success' => true,
                         'errors'  => false,
                        ];

            $model = SProductsQuery::create()
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
            } else {
                $response = [

                             'success' => FALSE,
                             'errors'  => 'not_valid_product',
                            ];

            }

            echo json_encode(array_merge($response, ['count' => count($data)]));
        } else {
            $this->core->error_404();
        }
    }

    /**
     * Remove product from compare
     * @param integer $productId
     */
    public function remove($productId = null) {
        if ($this->ajaxRequest) {

            $data = $this->_getData();
            $response = [
                         'success' => true,
                         'errors'  => false,
                        ];

            if (is_array($data)) {
                $key = array_search($productId, $data);

                if ($key !== false) {
                    unset($data[$key]);
                }

                $this->session->set_userdata('shopForCompare', $data);
            } else {
                $response = [
                             'success' => FALSE,
                             'errors'  => 'not_valid_product',
                            ];

            }

            echo json_encode(array_merge($response, ['count' => count($data)]));
        } else {
            $this->core->error_404();
        }
    }

    /**
     * Select products
     * @return array|mixed|\Propel\Runtime\Collection\ObjectCollection
     */
    protected function _loadProducts() {

        return SProductsQuery::create()
            ->findPks($this->_getData());
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

        if ($this->ajaxRequest) {
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
                //echo json_encode(array("dr" => $fordelete));

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
        } else {
            $this->core->error_404();
        }
    }

    public function sync() {
        if ($this->ajaxRequest) {
            echo json_encode($this->_getData());
        } else {
            $this->core->error_404();
        }
    }

}

/* End of file compare.php */