<?php

use Category\CategoryApi;
use Map\CustomFieldsTableMap;
use Map\SCategoryI18nTableMap;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Propel;

/**
 * ShopAdminSearch search products
 */
class ShopAdminSearch extends ShopAdminController
{

    /**
     * @var array
     */
    public $defaultLanguage;

    /**
     * @var int
     */
    public $perPage = 5;

    public function __construct() {
        parent::__construct();

        ShopController::checkVar();
        ShopAdminController::checkVarAdmin();

        if (!$_COOKIE['per_page']) {
            setcookie('per_page', ShopCore::app()->SSettings->getAdminProductsPerPage(), time() + 604800, '/', $this->input->server('HTTP_HOST'));
            $this->perPage = ShopCore::app()->SSettings->getAdminProductsPerPage();
        } else {
            $this->perPage = $_COOKIE['per_page'];
        }

        $this->defaultLanguage = getDefaultLanguage();
    }

    /**
     * Display search form
     *
     * @return void
     */
    public function per_page_cookie() {
        setcookie('per_page', (int) $this->input->get('count_items'), time() + 604800, '/', $this->input->server('HTTP_HOST'));
    }

    public function index() {
        $model = SProductsQuery::create()
            ->setComment(__METHOD__)
            ->joinWithI18n(MY_Controller::defaultLocale(), Criteria::INNER_JOIN)
            ->leftJoinBrand()
            ->addSelectModifier('SQL_CALC_FOUND_ROWS')
            ->leftJoinProductVariant();

        if (isset(ShopCore::$_GET['WithoutImages']) && ((int) ShopCore::$_GET['WithoutImages'] == 1)) {
            $model->where('(shop_product_variants.mainImage="" or shop_product_variants.mainImage IS NULL)');
        }

        if (isset(ShopCore::$_GET['CategoryId']) && ShopCore::$_GET['CategoryId'] > 0) {
            $category = SCategoryQuery::create()
                ->filterById((int) ShopCore::$_GET['CategoryId'])
                ->findOneOrCreate();

            $model->filterByCategory($category);

        }

        $model
            ->_if(array_key_exists('filterID', ShopCore::$_GET) && $this->input->get('filterID') !== '')
            ->filterById((int) ShopCore::$_GET['filterID'])
            ->_endif();

        $model
            ->_if(array_key_exists('sku', ShopCore::$_GET) && $this->input->get('sku') !== '')
            ->useProductVariantQuery(null, Criteria::LEFT_JOIN)
            ->filterByNumber('%' . ShopCore::$_GET['sku'] . '%', Criteria::LIKE)
            ->endUse()
            ->_endif();

        if (!empty(ShopCore::$_GET['text'])) {
            $text = ShopCore::$_GET['text'];
            if (!strpos($text, '%')) {
                $text = '%' . $text . '%';
            }

            $model->condition('name', 'SProductsI18n.Name LIKE ?', $text);

            $model->where(['name'], Criteria::LOGICAL_OR);
        }

        $model
            ->_if(array_key_exists('min_price', ShopCore::$_GET) && ShopCore::$_GET['min_price'] > 0)
            ->useProductVariantQuery()
            ->filterByPrice(ShopCore::$_GET['min_price'], Criteria::GREATER_EQUAL)
            ->endUse()
            ->_endif();

        $model
            ->_if(array_key_exists('max_price', ShopCore::$_GET) && ShopCore::$_GET['max_price'] > 0)
            ->useProductVariantQuery()
            ->filterByPrice(ShopCore::$_GET['max_price'], Criteria::LESS_EQUAL)
            ->endUse()
            ->_endif();

        $model
            ->_if(array_key_exists('Active', ShopCore::$_GET) && $this->input->get('Active') !== '')
            ->filterByActive($this->input->get('Active'))
            ->_endif();

        if (isset(ShopCore::$_GET['s'])) {
            if (strpos(ShopCore::$_GET['s'], 'CustomField_') !== false) {
                $name = str_replace('CustomField_', '', ShopCore::$_GET['s']);
                $fields = CustomFieldsDataQuery::create()
                    ->withColumn(CustomFieldsTableMap::COL_FIELD_NAME, 'name')
                    ->filterBydata(1)
                    ->useCustomFieldsQuery(null, Criteria::LEFT_JOIN)
                    ->filterByEntity('product')
                    ->endUse()
                    ->useCustomFieldsI18nQuery()
                    ->filterByFieldLabel($name)
                    ->endUse()
                    ->find()
                    ->toArray('entityId');
                $ids = array_column($fields, 'entityId');

                $model->filterById($ids);
            }

            $model->_if(ShopCore::$_GET['s'] === 'Hit')->filterByHit(true)->_endif();
            $model->_if(ShopCore::$_GET['s'] === 'Hot')->filterByHot(true)->_endif();
            $model->_if(ShopCore::$_GET['s'] === 'Action')->filterByAction(true)->_endif();
            $model->_if(ShopCore::$_GET['s'] === 'Archive')->filterByArchive(true)->_endif();
        }

        if (isset(ShopCore::$_GET['orderMethod']) && ShopCore::$_GET['orderMethod'] != '') {
            $order_methods = [
                              'Id',
                              'Name',
                              'CategoryName',
                              'Price',
                              'Active',
                              'Reference',
                             ];
            if (in_array(ShopCore::$_GET['orderMethod'], $order_methods, true)) {
                switch (ShopCore::$_GET['orderMethod']) {
                    case 'Name':
                        $model->useSProductsI18nQuery()->orderByName(ShopCore::$_GET['order'])->endUse();
                        break;

                    case 'Price':
                        $model->useProductVariantQuery()->orderByPrice(ShopCore::$_GET['order'])->endUse();
                        break;

                    case 'Reference':
                        $model->useProductVariantQuery()->orderByNumber(ShopCore::$_GET['order'])->endUse();
                        break;

                    case 'CategoryName':
                        $model->useMainCategoryQuery()
                            ->joinWithI18n(MY_Controller::defaultLocale(), Criteria::JOIN)
                            ->orderBy(SCategoryI18nTableMap::COL_NAME, ShopCore::$_GET['order'])
                            ->endUse();
                        break;

                    default :
                        $model->orderBy(ShopCore::$_GET['orderMethod'], ShopCore::$_GET['order']);
                        break;
                }
            }
        } else {
            $model->orderById(Criteria::DESC);
        }

        if (count(ShopCore::$_GET['productProperties']) > 0) {
            $combine = $this->_buildCombinatorArray(ShopCore::$_GET['productProperties']);
            if ($combine !== false) {
                $model->combinator($combine);
            }
        }

        $model = $model
            ->offset((int) ShopCore::$_GET['per_page'])
            ->limit($this->perPage)
            ->groupById()
            ->find();

        $totalProducts = $this->getTotalRow();

        $model->populateRelation('ProductVariant');
        $model->populateRelation('MainCategory');

        //to save filter query

        if (!empty(ShopCore::$_GET)) {
            session_start();
            $_SESSION['ref_url'] = '?' . http_build_query(ShopCore::$_GET);
        } else {
            unset($_SESSION['ref_url']);
        }

        // Create pagination
        $this->load->library('pagination');
        $config['base_url'] = '/admin/components/run/shop/search/index/?' . http_build_query(ShopCore::$_GET);
        $config['container'] = 'shopAdminPage';
        $config['page_query_string'] = true;
        $config['uri_segment'] = 8;
        $config['total_rows'] = $totalProducts;
        $config['per_page'] = $this->perPage;

        $config['separate_controls'] = true;
        $config['full_tag_open'] = '<div class="pagination pull-left"><ul>';
        $config['full_tag_close'] = '</ul></div>';
        $config['controls_tag_open'] = '<div class="pagination pull-right"><ul>';
        $config['controls_tag_close'] = '</ul></div>';
        $config['next_link'] = lang('Next', 'admin') . '&nbsp;&gt;';
        $config['prev_link'] = '&lt;&nbsp;' . lang('Prev', 'admin');
        $config['cur_tag_open'] = '<li class="btn-primary active"><span>';
        $config['cur_tag_close'] = '</span></li>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['num_tag_close'] = '</li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';

        $this->pagination->num_links = 6;
        $this->pagination->initialize($config);

        $custom_fields = CustomFieldsQuery::create()
            ->filterByEntity('product')
            ->filterByIsActive(1)
            ->filterBytypeId(4)
            ->find();

        $catTree = SCategoryQuery::create()->getTree(0, SCategoryQuery::create()->joinWithI18n(MY_Controller::defaultLocale()))->getCollection();

        $this->render(
            'list',
            [
             'products'      => $model,
             'categories'    => $catTree,
             'totalProducts' => $totalProducts,
             'pagination'    => $this->pagination->create_links_ajax(),
             'filter_url'    => http_build_query(ShopCore::$_GET),
             'cur_uri_str'   => base64_encode($this->uri->uri_string() . '?' . http_build_query(ShopCore::$_GET)),
             'custom_fields' => $custom_fields,
            ]
        );
    }

    public function save_positions_variant() {
        $positions = $this->input->post('positions');

        if (!$positions && count($positions) == 0) {
            return false;
        }

        foreach ((array) $positions as $key => $val) {
            try {
                SProductVariantsQuery::create()
                    ->filterById((int) $val)
                    ->findOne()
                    ->setPosition($key)
                    ->save();
            } catch (Exception $exc) {
                showMessage($exc->getMessage(), '', 'r');
            }

        }
        showMessage(lang('Positions saved', 'admin'));
    }

    /**
     * @param array $data
     * @return array|bool
     */
    protected function _buildCombinatorArray(array $data) {
        $resultData = []; // Array containing data for combinator
        foreach ($data as $fieldId => $fieldValue) {
            // Load field
            $field = SPropertiesQuery::create()
                ->filterByActive(true)
                ->findPk($fieldId);

            if ($field !== null && !empty($fieldValue)) {
                if (is_array($fieldValue)) {
                    $resultData[$fieldId] = $fieldValue;
                } else {
                    $resultData[$fieldId][] = $fieldValue;
                }
            }
        }

        return !empty($resultData) ? $resultData : false;
    }

    private function getTotalRow() {
        $connection = Propel::getConnection('Shop');
        $statement = $connection->prepare('SELECT FOUND_ROWS() as `number`');
        $statement->execute();
        $resultset = $statement->fetchAll();
        return $resultset[0]['number'];
    }

}