<?php

namespace Category;

use Exception;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Propel;
use SCategory;
use CMSFactory\Events;
use SCategoryQuery;
use ShopController;
use SPagination;
use SProductsQuery;

(defined('BASEPATH')) OR exit('No direct script access allowed');

class BaseCategory extends ShopController
{

    const EVENT_CATEGORY_PRESELECT_PRODUCTS = 'category:preselect_products';
    const EVENT_CATEGORY_PRELOAD = 'category:preload';
    const EVENT_CATEGORY_LOAD = 'category:load';

    /**
     *
     * @var get-params
     */
    private $get;

    /**
     *
     * @var SCategory
     */
    private $categoryModel;

    public function __construct(SCategory $categoryModel, array $get = null) {
        parent::__construct();
        $this->ci = \CI::$APP;

        if (!$this->areAllParentsActive($categoryModel)) {
            throw new Exception('category model not found');
        }

        \ShopCore::$currentCategory = $this->categoryModel;

        $this->categoryModel = $categoryModel;

        $_get = $this->input->get() != null ?: [];

        $this->get = (null === $get) ? $_get : $get;
    }

    public function getCategory() {

        return $this->categoryModel;
    }

    public function installPagination(array &$data, $basePath, $perPage) {
        /** Pagination */
        $this->ci->load->library('Pagination');
        $pagination = new SPagination();

        $paginationConfig['base_url'] = $basePath;
        $paginationConfig['total_rows'] = $data['totalProducts'];
        $paginationConfig['per_page'] = $perPage;
        $paginationConfig['last_link'] = ceil($data['totalProducts'] / $perPage);

        $paginationConfig['page_query_string'] = true;
        $paginationConfig['first_link'] = '1';
        $paginationConfig['num_links'] = 3;
        include_once "./templates/{$this->config->item('template')}/paginations.php";

        $pagination->initialize($paginationConfig);

        $data['pagination'] = $pagination->create_links();
        $data['page_number'] = $pagination->cur_page;
    }

    /**
     * @param SCategory $category
     * @return bool
     */
    private function areAllParentsActive(SCategory $category) {
        $parentIds = unserialize($category->getFullPathIds());

        return SCategoryQuery::create()
            ->filterByActive(false)
            ->filterById($parentIds, Criteria::IN)
            ->count() == 0;
    }

    /**
     * @param null|int $limit
     * @param null|int $offset
     * @param null|string $order
     * @return array
     * @throws Exception
     */
    public function getProducts($limit = null, $offset = null, $order = null) {

        if (!$this->categoryModel) {
            throw new Exception('category model not found');
        }

        $order_method = $order ?: $this->getDefaultSort();

        /** @var SProductsQuery $productsQuery Prepare products model */
        $productsQuery = SProductsQuery::create()
            ->addSelectModifier('SQL_CALC_FOUND_ROWS')
            ->filterByCategory($this->categoryModel)
            ->filterByActive(true)
            ->filterByArchive(false)
            ->joinMainCategory()
            ->useMainCategoryQuery()
            ->filterByActive(true)
            ->endUse()
            ->joinWithI18n(\MY_Controller::getCurrentLocale())
            ->joinProductVariant()
            ->withColumn('IF(sum(shop_product_variants.stock) > 0, 1, 0)', 'allstock')
            ->groupById()
            ->joinBrand()
            ->distinct()
            ->orderBy('allstock', Criteria::DESC);

        /** For order method by get order */
        if ($order_method) {
            $productsQuery->globalSort($order_method);
        }

        // for hooking into query conditions
        Events::create()->raiseEvent(
            [
             'productsQuery' => $productsQuery,
             'model'         => $this->categoryModel,
            ],
            self::EVENT_CATEGORY_PRESELECT_PRODUCTS
        );

        /** Getting products model from base */
        try {

            $products = $productsQuery->offset((int) $offset)
                ->orderBy('SProducts.Id', Criteria::DESC)
                ->limit((int) $limit)
                ->find();
        } catch (PropelException $exc) {
            show_error($exc->getMessage());
        }

        /** Get total product count according to filter parameters */
        $totalProducts = $this->getTotalRow();

        /** Render category page */
        $data = [
                 'title'         => $this->categoryModel->getTitle(),
                 'category'      => $this->categoryModel,
                 'products'      => $products,
                 'model'         => $products,
                 'totalProducts' => $totalProducts,
                 'order_method'  => $order_method,
                ];

        return $data;
    }

    /**
     * Get total rows
     * @return int
     */
    private function getTotalRow() {
        $connection = Propel::getConnection('Shop');
        $statement = $connection->prepare('SELECT FOUND_ROWS() as `number`');
        $statement->execute();
        $resultset = $statement->fetchAll();
        return $resultset[0]['number'];
    }

    /**
     * Get default sort method
     * @return mixed
     */
    public function getDefaultSort() {
        if ($this->categoryModel) {
            $order_method = $this->categoryModel->getOrderMethod();
            $order_from_db = $this->db->where('id', (int) $order_method)->get('shop_sorting')->result_array();
            $order = $order_from_db[0]['get'];
        }

        return !empty($order) ? $order : getDefaultSort();
    }

}