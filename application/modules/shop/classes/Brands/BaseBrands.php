<?php

namespace Brands;

use MY_Controller;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Connection\Exception\ConnectionException;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Propel;
use SBrands;
use SBrandsQuery;
use SCategoryQuery;
use ShopController;
use ShopCore;
use SProductsQuery;

(defined('BASEPATH')) OR exit('No direct script access allowed');

/**
 * Shop Controller
 *
 * @uses \ShopController
 * @package Shop
 * @copyright 2013 ImageCMS
 */
class BaseBrands extends ShopController
{

    /**
     * @var array
     */
    public $data;

    /**
     * @var string
     */
    protected $locale;

    /**
     * @var SBrands
     */
    public $model;

    /**
     * @var string
     */
    public $brandPath;

    /**
     * @var int
     */
    protected $perPage;

    /**
     * @var string
     */
    public $templateFile = 'brand';

    /**
     * @var string
     */
    public $category = '';

    public function __construct() {
        parent::__construct();
        // Load per page param
        $this->locale = MY_Controller::getCurrentLocale();
        $i = MY_Controller::getCurrentLocale() == MY_Controller::defaultLocale() ? 4 : 5;

        if ($this->uri->total_segments() == $i) {
            $this->brandPath = $this->uri->segment($this->uri->total_segments() - 1);
        } else {
            $this->brandPath = $this->uri->segment($this->uri->total_segments());
        }

        $this->category = (int) $this->uri->segment($i) ?: '';

        // Load per page param
        $this->perPage = $this->input->get('user_per_page') ?: ShopCore::app()->SSettings->getFrontProductsPerPage();

        $this->model = $this->_loadBrand($this->brandPath);

        $_GET['category'] = $this->category;

        if ($this->category) {
            $this->REQUEST_URI = $this->input->server('REQUEST_URI') . '/' . $this->category;
        } else {
            $this->REQUEST_URI = $this->input->server('REQUEST_URI');
        }

        if ($this->model != null) {
            $this->__CMSCore__();
        }

        $this->core->core_data['data_type'] = 'brand';
        $this->index();

        exit;
    }

    /**
     * Display product info.
     *
     * @access public
     */
    public function __CMSCore__() {
        $this->core->core_data['id'] = $this->model->getId();
        $this->perPage = ((int) $this->input->get('user_per_page')) ?: ShopCore::app()->SSettings->getFrontProductsPerPage();

        $products = SProductsQuery::create()
            ->addSelectModifier('SQL_CALC_FOUND_ROWS')
            ->filterByActive(true)
            ->filterByArchive(false)
            ->filterByBrandId($this->model->getId())
            ->joinWithI18n($this->locale)
            ->joinProductVariant()
            ->joinMainCategory()
            ->where('MainCategory.Active = ?', 1)
            ->withColumn('IF(sum(shop_product_variants.stock) > 0, 1, 0)', 'allstock')
            ->groupById()
            ->distinct()
            ->orderBy('allstock', Criteria::DESC);

        //for found in categories
        $incategories = clone $products;
        $incategories = $incategories
            ->select(['CategoryId', 'Id'])
            ->useMainCategoryQuery()
            ->orderByPosition()
            ->endUse()
            ->distinct()
            ->find()
            ->toArray();

        if (count($incategories) > 0) {
            foreach ($incategories as $key => $value) {
                unset($incategories[$key]['Id']);
                $incategories[$key] = $value['CategoryId'];
            }
            $incategories = array_count_values($incategories);
        }

        if ($this->category) {
            $this->template->registerCanonical(site_url('shop/brand') . '/' . $this->model->getUrl());
            $products->filterByCategoryId($this->category);
        }
        //choose order method (default or get)
        if (!$this->input->get('order')) {
            $order_method = getDefaultSort();
        } elseif ($this->input->get('order')) {
            $order_method = $this->input->get('order');
        }

        //for order method by get order
        if ($order_method) {
            $products = $products->globalSort($order_method);
        }

        try {
            $products = $products->offset((int) $this->input->get('per_page'))
                ->limit((int) $this->perPage)
                ->find();
        } catch (PropelException $exc) {
            show_error($exc->getMessage());
        }

        /**
         * Prepare category tree of Main catagory and sub-categories
         */
        $count_cats = $incategories;
        $totalProducts = $this->getTotalRow();

        $categoryTree = SCategoryQuery::create()
            ->getTree(
                0,
                SCategoryQuery::create()
                    ->joinWithI18n(MY_Controller::getCurrentLocale())
            );

        $categories = $this->getListNew($count_cats, $categoryTree);
        $this->data = [
                       'categories'    => $categories,
                       'template'      => $this->templateFile,
                       'model'         => $this->model,
                       'products'      => $products,
                       'totalProducts' => $totalProducts,
                       'incats'        => $incategories,
                       'order_method'  => $order_method,
                      ];
    }

    private function getListNew($count_cats, $categoryTree) {

        $categories = [];

        foreach ($categoryTree as $parent) {

            $data = $parent->getSubItems()->getData();

            if (array_key_exists($parent->getId(), $count_cats)) {
                $categories[$parent->getId()][$parent->getName()][] = [
                                                                       'id'    => $parent->getId(),
                                                                       'name'  => $parent->getName(),
                                                                       'count' => $count_cats[$parent->getId()],
                                                                      ];
            }

            while (!empty($data)) {
                /** @var $model ModelWrapper */
                $model = array_shift($data);

                if ($model->hasSubItems()) {
                    $subItems = [];

                    foreach ($model->getSubItems() as $subcategory) {
                        array_push($subItems, $subcategory);
                    }
                    $data = array_merge($subItems, $data);

                }
                if (array_key_exists($model->getId(), $count_cats)) {
                    $categories[$parent->getId()][$parent->getName()][] = [
                                                                           'id'    => $model->getId(),
                                                                           'name'  => $model->getName(),
                                                                           'count' => $count_cats[$model->getId()],
                                                                          ];
                }

            }

        }

        return $categories;

    }

    /**
     * @param string $url
     * @return SBrands
     */
    protected function _loadBrand($url) {
        $brands = SBrandsQuery::create()
            ->joinWithI18n(MY_Controller::getCurrentLocale())
            ->filterByUrl($url)
            ->findOne();
        return $brands;
    }

    /**
     * @return mixed
     * @throws ConnectionException
     */
    private function getTotalRow() {
        $connection = Propel::getConnection('Shop');
        $statement = $connection->prepare('SELECT FOUND_ROWS() as `number`');
        $statement->query();
        $resultset = $statement->fetchAll();
        return $resultset[0]['number'];
    }

    public function renderImageList() {
        $model = $this->db
            ->join('shop_brands_i18n', 'shop_brands_i18n.id=shop_brands.id')
            ->order_by('position', 'desc')
            ->where('locale', $this->locale)
            ->where('image <>', '')
            ->get('shop_brands')
            ->result_array();

        $this->render(
            'brands_images',
            ['model' => $model]
        );
        exit;
    }

    public function renderNamesList() {
        $alphabet = [
                     'А',
                     'Б',
                     'В',
                     'Г',
                     'Д',
                     'Е',
                     'Ё',
                     'Ж',
                     'З',
                     'И',
                     'Й',
                     'К',
                     'Л',
                     'М',
                     'Н',
                     'О',
                     'П',
                     'Р',
                     'С',
                     'Т',
                     'У',
                     'Ф',
                     'Х',
                     'Ц',
                     'Ч',
                     'Ш',
                     'Щ',
                     'Э',
                     'Ю',
                     'Я',
                     'A',
                     'B',
                     'C',
                     'D',
                     'E',
                     'F',
                     'G',
                     'H',
                     'I',
                     'J',
                     'K',
                     'L',
                     'M',
                     'N',
                     'O',
                     'P',
                     'Q',
                     'R',
                     'S',
                     'T',
                     'U',
                     'V',
                     'W',
                     'X',
                     'Y',
                     'Z',
                    ];

        $array = $this->db
            ->join('shop_brands_i18n', 'shop_brands_i18n.id=shop_brands.id')
            ->order_by('position', 'desc')
            ->where('locale', $this->locale)
            ->get('shop_brands')
            ->result_array();
        $this->db->cache_off();

        $model = [];
        foreach ($array as $key => $m) {

            $model[mb_substr($m['name'], 0, 1, 'UTF-8')][$key] = $m;
            //режит русские буквы
            //$model[strtoupper($m['name'][0])][$key] = $m;
        }

        $all_count = 0;
        foreach ($alphabet as $key => $char) {
            if ($model[$char] != null) {
                $all_count++;
                $all_count += count($model[$char]);
            }
        }

        $iteration = floor($all_count / 5);

        $this->render(
            'brands_list',
            [
             'model'     => $model,
             'alphabet'  => $alphabet,
             'all_count' => $all_count,
             'iteration' => $iteration,
            ]
        );
        exit;
    }

}

/* End of file BaseBrands.php */