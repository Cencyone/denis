<?php

use CMSFactory\assetManager;
use Propel\Runtime\ActiveQuery\Criteria;

class Shop_widgets extends ShopController
{

    private $productsDefaults = [
                                 'subpath'       => 'widgets',
                                 'productsType'  => 'popular',
                                 'productsCount' => 10,
                                 'title'         => 'Popular products',
                                ];

    private $brandsDefaults = [
                               'subpath'     => 'widgets',
                               'brandsCount' => 15,
                               'withImages'  => true,
                              ];

    private $similarDefaults = [
                                'title'         => 'Similar products',
                                'productsCount' => 5,
                                'subpath'       => 'widgets',
                               ];

    private $viewProductDefaults = ['subpath' => 'widgets'];

    public function __construct() {

        parent::__construct();
    }

    public function view_product($widget) {
        $settings = $widget['settings'];
        $core_data = $this->core->core_data;

        $data = [
                 'products'   => $this->showLastProducts($settings['productsCount']),
                 'title'      => $settings['title'],
                 'widget_key' => $widget['name'],
                ];

        if ($core_data['data_type'] == 'product') {
            $this->addProduct($core_data['id'], $settings['productsCount']);
        }

        return $this->template->fetch('widgets/' . $widget['name'], $data);
    }

    public function addProduct($param = NULL, $limit = 4) {
        /**
         * Добавление первого продукта в массив.
         */
        if ($param != NULL) {
            if ($this->session->userdata('page') == false) {
                $pageId = [$param];
                $this->session->set_userdata('page', $pageId);
            } else {
                /**
                 * Если не существует такого продукта записываем новый, и удаляем последний.
                 */
                $pageId = array_unique($this->session->userdata('page'));
                if (false !== ($key = array_search($param, $pageId))) {
                    unset($pageId[$key]);
                }
                if (count($pageId) >= $limit) {
                    array_shift($pageId);
                }

                array_push($pageId, $param);
                $this->session->set_userdata('page', $pageId);
            }
        } else {
            log_message('error', 'Widget function "addProduct" product ID is not passed.');
            return false;
        }
    }

    /**
     * Вывод продукта.
     * @param int $limit
     * @return SProducts|null
     */
    public function showLastProducts($limit = 20) {
        /**
         * Вызываем сессию и извлекаем из нее все идентификаторы, и записываем в строку через запятую.
         */
        $pageId = $this->session->userdata('page');

        if (count($pageId) >= 1) {
            /**
             * Вытаскиваем все продукты из базы данных.
             */
            $model = SProductsQuery::create()
                ->setComment(__METHOD__)
                ->joinWithI18n(MY_Controller::getCurrentLocale())
                ->filterById($pageId)
                ->limit($limit)
                ->filterByActive(true)
                ->useProductVariantQuery()
                ->filterByStock(['min' => 1])
                ->groupByProductId()
                ->endUse()
                ->find();

            $models = $model->getArrayCopy('id');
            $existingIds = array_intersect($pageId, array_keys($models));

            /**
             * Sort collection by reversed user view sequence
             */
            $model->setData(array_replace(array_flip(array_reverse($existingIds)), $models));

            /**
             * Возвращаем все продукты в виде массива.
             */
            return $model;
        }
        return null;
    }

    /**
     * @param string $mode
     * @param array $data
     * @return bool
     */
    public function view_product_configure($mode = 'install_defaults', $data = []) {
        if ($this->dx_auth->is_admin() == FALSE) {
            exit;
        }

        switch ($mode) {
            case 'install_defaults':
                $this->load->module('admin/widgets_manager')->update_config($data['id'], $this->viewProductDefaults);
                break;

            case 'show_settings':
                $this->render('view_product_form', ['widget' => $data]);
                break;

            case 'update_settings':

                $this->load->library('form_validation');
                $this->form_validation->set_rules('title', lang('Widget title', 'main'), 'trim|xss_clean');

                if ($this->form_validation->run()) {

                    $settings = [
                                 'productsType'  => $this->input->post('productsType'),
                                 'title'         => $this->input->post('title'),
                                 'productsCount' => $this->input->post('productsCount'),
                                 'subpath'       => 'widgets',
                                ];

                    $this->load->module('admin/widgets_manager')->update_config($data['id'], $settings);
                    showMessage(lang('Successfully saved'));

                    if ($this->input->post('action') == 'tomain') {
                        pjax('/admin/widgets_manager');
                    } else {
                        pjax('');
                    }
                } else {
                    showMessage($this->form_validation->error_string(), '', 'r');
                }

                break;

            default :
                return false;
        }
    }

    /**
     * @param array $widget
     * @return false|string
     */
    public function products($widget) {

        $settings = $widget['settings'];

        //        $cache_keys = $this->generateKeyCache($widget['name'], $settings);
        //
        //        if ($this->getCache()->contains($cache_keys)) {
        //
        //            $data = $this->getCache()->fetch($cache_keys);
        //
        //        } else {

            $data = [
                     'products'   => $this->getPromoBlock($settings['productsType'], $settings['productsCount']),
                     'title'      => $settings['title'],
                     'widget_key' => $widget['name'],
                    ];
            //
            //            $this->getCache()->save($cache_keys, $data, config_item('cache_ttl'));
            //        }

            $widget_to_view = $this->template->fetch('widgets/' . $widget['name'], $data);

            return $widget_to_view;
    }

    /**
     *
     * @param string $mode
     * @param array $data
     * @return boolean
     */
    public function products_configure($mode = 'install_defaults', $data = []) {
        if ($this->dx_auth->is_admin() == FALSE) {
            exit;
        }

        switch ($mode) {
            case 'install_defaults':
                $this->load->module('admin/widgets_manager')->update_config($data['id'], $this->productsDefaults);
                break;

            case 'show_settings':
                $this->render('products_form', ['widget' => $data]);
                break;

            case 'update_settings':

                $this->load->library('form_validation');
                $this->form_validation->set_rules('title', lang('Widget title'), 'trim|xss_clean');
                $this->form_validation->set_rules('productsCount', lang('Number of items to display'), 'numeric|required');

                if ($this->form_validation->run()) {

                    $productType = implode(',', $this->input->post('productsType'));

                    $settings = [
                                 'productsType'  => $productType,
                                 'title'         => $this->input->post('title'),
                                 'productsCount' => $this->input->post('productsCount'),
                                 'subpath'       => 'widgets',
                                ];

                    $this->load->module('admin/widgets_manager')->update_config($data['id'], $settings);
                    showMessage(lang('Successfully saved'));

                    if ($this->input->post('action') == 'tomain') {
                        pjax('/admin/widgets_manager');
                    } else {
                        pjax('');
                    }
                } else {
                    showMessage($this->form_validation->error_string(), '', 'r');
                }

                break;

            default :
                return false;
                break;
        }
    }

    /**
     *
     * @param array $widget
     * @return string
     */
    public function brands($widget) {

        $settings = $widget['settings'];

        $cache_key = $this->generateKeyCache($widget['name'], $settings);

        if ($this->getCache()->contains($cache_key)) {

            $data = $this->getCache()->fetch($cache_key);

        } else {

            $data = [
                     'settings' => $settings,
                     'title'    => $settings['title'],
                     'brands'   => ShopCore::app()->SBrandsHelper->mostProductBrands($settings['brandsCount'], $settings['withImages']),
                    ];

            $this->getCache()->save($cache_key, $data, config_item('cache_ttl'));
        }

        $widget_to_view = $this->template->fetch('widgets/' . $widget['name'], $data);

        return $widget_to_view;
    }

    /**
     * @param string $mode
     * @param array $data
     * @return bool
     */
    public function brands_configure($mode = 'install_defaults', $data = []) {
        if ($this->dx_auth->is_admin() == FALSE) {
            exit;
        }

        switch ($mode) {
            case 'install_defaults':
                $this->load->module('admin/widgets_manager')->update_config($data['id'], $this->brandsDefaults);
                break;

            case 'show_settings':
                $this->render('brands_form', ['widget' => $data]);
                break;

            case 'update_settings':

                $this->load->library('form_validation');
                $this->form_validation->set_rules('brandsCount', lang('Number of items to display'), 'numeric|required');

                if ($this->form_validation->run()) {
                    $settings = [
                                 'withImages'  => (bool) $this->input->post('withImages'),
                                 'brandsCount' => $this->input->post('brandsCount'),
                                 'subpath'     => 'widgets',
                                 'title'       => $this->input->post('title'),
                                ];

                    $this->load->module('admin/widgets_manager')->update_config($data['id'], $settings);
                    showMessage(lang('Successfully saved'));

                    if ($this->input->post('action') == 'tomain') {
                        pjax('/admin/widgets_manager');
                    } else {
                        pjax('');
                    }
                } else {
                    showMessage($this->form_validation->error_string(), '', 'r');
                }

                break;

            default :
                return false;
        }
    }

    /**
     *
     * @param array $widget
     * @return string
     */
    public function similar_products($widget) {

        $settings = $widget['settings'];

        /**
         * @var $model SProducts
         */
        $model = assetManager::create()->getData('model');

        $model->setVirtualColumn('SettingProdCount', $settings['productsCount']);
        $cache_key = $this->generateKeyCache($widget['name'], $model);

        if ($this->getCache()->contains($cache_key)) {

            $data = $this->getCache()->fetch($cache_key);

        } else {

            $data = [
                     'settings'        => $settings,
                     'title'           => $settings['title'],
                     'widget_key'      => $widget['name'],
                     'similarProducts' => ($model instanceof SProducts) ? $model->getSimilarPriceProductsModels($settings['productsCount']) : null,
                    ];

            $this->getCache()->save($cache_key, $data, config_item('cache_ttl'));
        }

        return $this->template->fetch('widgets/' . $widget['name'], $data);
    }

    /**
     *
     * @param string $mode
     * @param array $data
     * @return boolean
     */
    public function similar_products_configure($mode = 'install_defaults', $data = []) {
        if ($this->dx_auth->is_admin() == FALSE) {
            exit;
        }

        switch ($mode) {
            case 'install_defaults':
                $this->load->module('admin/widgets_manager')->update_config($data['id'], $this->similarDefaults);
                break;

            case 'show_settings':
                $this->render('similar_products_form', ['widget' => $data]);
                break;

            case 'update_settings':

                $this->load->library('form_validation');
                $this->form_validation->set_rules('productsCount', lang('Number of items to display'), 'numeric|required');

                if ($this->form_validation->run()) {
                    $settings = [
                                 'title'         => $this->input->post('title'),
                                 'productsCount' => $this->input->post('productsCount'),
                                 'subpath'       => 'widgets',
                                ];

                    $this->load->module('admin/widgets_manager')->update_config($data['id'], $settings);
                    showMessage(lang('Successfully saved'));

                    if ($this->input->post('action') == 'tomain') {
                        pjax('/admin/widgets_manager');
                    } else {
                        pjax('');
                    }
                } else {
                    showMessage($this->form_validation->error_string(), '', 'r');
                }

                break;

            default :
                return false;
        }
    }

    /**
     *
     * @param string $viewName
     * @param array $data
     * @param boolean $return
     * @return string
     */
    public function render($viewName, $data = [], $return = false) {

        if (!empty($data)) {
            $this->template->add_array($data);
        }

        if ($return === false) {
            if ($this->input->is_ajax_request()) {
                $this->template->display('file:' . getModulePath('shop') . 'widgets/templates/' . $viewName);
            } else {
                $this->template->show('file:' . getModulePath('shop') . 'widgets/templates/' . $viewName);
            }
        } else {
            return $this->template->fetch('file:' . getModulePath('shop') . 'widgets/templates/' . $viewName);
        }

        exit;
    }

    /**
     *
     * @param string $type
     * @param integer $limit
     * @param integer $idCategory
     * @param integer $idBrand
     * @return SProducts
     */
    public function getPromoBlock($type = 'action', $limit = 10, $idCategory = NULL, $idBrand = NULL) {
        $model = SProductsQuery::create()
            ->setComment(__METHOD__)
            ->joinWithI18n(ShopController::getCurrentLocale())
            ->joinWithRoute(Criteria::INNER_JOIN)
            ->orderByCreated('DESC');

        if ($idCategory) {
            $model = $model->filterByCategoryId($idCategory);
        }
        if ($idBrand) {
            $model = $model->filterByBrandId($idBrand);
        }
        if (strpos($type, 'hit')) {
            $model = $model->_or()->filterByHit(true);
        }
        if (strpos($type, 'hot')) {
            $model = $model->_or()->filterByHot(true);
        }
        if (strpos($type, 'action')) {
            $model = $model->_or()->filterByAction(true);
        }
        if (strpos($type, 'oldPrice')) {
            $model = $model->filterByOldPrice(['min' => true]);
        }
        if (strpos($type, 'category') AND ($categoryId = filterCategoryId()) !== false) {
            $model = $model->useShopProductCategoriesQuery()->filterByCategoryId($categoryId)->endUse();
        }

        $model = $model->useMainCategoryQuery()->filterByActive(1)->endUse();

        if (strpos($type, 'date')) {
            $model = $model->orderByUpdated(Criteria::DESC);
        }
        $model = $model->filterByArchive(0)->filterByActive(true)->limit($limit)->find();

        return $model;
    }

    /**
     * @param string $widget_name
     * @param SProducts|array $param
     * @return string
     */
    private function generateKeyCache($widget_name, $param) {

        $data = [
                 $widget_name,
                 MY_Controller::getCurrentLocale(),
                ];

        switch ($widget_name) {
            case 'products_similar_sidebar' :

                $data[] = $param->getSettingProdCount();
                $data[] = $param->getUrl();

                break;
            case 'brands' :
                $data[] = $param['with_image'] ? 1 : 0;
                $data[] = $param['brandsCount'];

                break;
            case 'products_hits':
                $data[] = $param['productsType'];
                $data[] = $param['productsCount'];

                break;
            case 'special_products' :

                $data[] = $param['productsType'];
                $data[] = $param['productsCount'];

                break;
        }

        $res = md5(implode('_', $data));
        return $res;

    }

}