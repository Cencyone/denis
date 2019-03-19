<?php

(defined('BASEPATH')) OR exit('No direct script access allowed');

use CMSFactory\assetManager;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Exception\PropelException;
use smart_filter\models\SFilterPattern;
use smart_filter\models\SFilterPatternQuery;
use smart_filter\src\Admin\DataProvider;
use smart_filter\src\Admin\Exception\PatternValidationException;
use smart_filter\src\Admin\PatternHandler;

/**
 * Class Admin
 * @property Cms_admin cms_admin
 */
class Admin extends BaseAdminController
{

    const NAME = 'smart_filter';

    /**
     * @var string
     */
    private $adminUrl = '/admin/components/cp/smart_filter';

    /**
     * @var DataProvider
     */
    private $provider;

    /**
     * @var PatternHandler
     */
    private $handler;

    public function __construct() {

        parent::__construct();
        (new MY_Lang())->load(self::NAME);
        $this->provider = new DataProvider();
        $this->handler = new PatternHandler($this->provider);
    }

    public function migrate() {

        (new \smart_filter\MigrateCommand($this->db, new PatternHandler(new DataProvider())))->run();

    }

    /**
     * Patterns list
     * @throws PropelException
     */
    public function index() {

        $this->load->library('pagination');
        $paginationConfig = $this->load->config('pagination');

        $locale = MY_Controller::defaultLocale();
        $page = $this->input->get('per_page') ?: 1;

        $items = $this->provider->getPatterns($this->input->get(), $locale, $page, $paginationConfig['per_page']);
        $paginationConfig['total_rows'] = $items->getNbResults();

        $categories = $this->provider->getCategoriesJoinPattern($locale);
        $categories->prepend((new SCategory())->setName(lang('All', 'admin')));

        $this->pagination->initialize($paginationConfig);

        $category_id = $this->input->get('category_id') ?: 0;

        $data = [
                 'items'       => $items,
                 'admin_url'   => $this->adminUrl,
                 'categories'  => $categories->toKeyIndex(),
                 'category_id' => $category_id,
                 'pagination'  => $this->pagination->create_links(),

                ];

        assetManager::create()
            ->setData($data)
            ->registerScript('admin')
            ->registerStyle('style')
            ->renderAdmin('index');
    }

    /**
     * Create pattern handler
     */
    public function create() {

        $locale = MY_Controller::defaultLocale();

        if ($this->input->post()) {

            $this->push(new SFilterPattern(), $locale, lang('Pattern successfully created', 'smart_filter'));

        } else {

            $categories = $this->provider->getCategoriesWithLevels($locale);
            $firstCategory = $categories->getFirst();

            $selectsData = [];
            if ($firstCategory) {
                $selectsData = $this->provider->getSelectsData($firstCategory->getId(), $locale);
            }

            assetManager::create()
                ->setData(compact('categories', 'locale'))
                ->setData($selectsData)
                ->registerScript('admin')
                ->registerStyle('style')
                ->renderAdmin('create');
        }
    }

    /**
     * Update pattern handler
     * @param $id
     * @param string|null $locale
     * @return null|string
     * @throws PropelException
     */
    public function edit($id, $locale = null) {

        $locale = $locale ?: MY_Controller::defaultLocale();
        $pattern = SFilterPatternQuery::create()->setComment(__METHOD__)->joinWithI18n($locale)->findOneById($id);

        if (!$pattern) {
            $core_error = new ShopAdminController();

            $core_error->error404(lang('Page not found', 'admin'));
        }
        $pattern->setLocale($locale);

        if ($this->input->post()) {
            $this->push($pattern, $locale, lang('Pattern successfully edited', 'smart_filter'));

        } else {

            $selectsData = $this->provider->getSelectsData($pattern->getCategoryId(), $locale, $pattern->getDataPropertyId());

            $data = [
                     'urls'       => $this->handler->getUrlsForMultiplePattern($pattern),
                     'urlLocale'  => $this->getUrlLocale($locale),
                     'pattern'    => $pattern,
                     'locale'     => $locale,
                     'categories' => $this->provider->getCategoriesWithLevels($locale),
                     'languages'  => $this->cms_admin->get_langs(true),
                    ];
            assetManager::create()
                ->setData($selectsData)
                ->setData($data)
                ->registerScript('admin')
                ->registerStyle('style')
                ->renderAdmin('edit');

        }

    }

    /**
     * Run post validation and create/update pattern
     * @param SFilterPattern $pattern
     * @param $locale
     * @param $successMessage
     * @return null|string
     * @throws PropelException
     */
    private function push(SFilterPattern $pattern, $locale, $successMessage) {

        $this->setValidationRules();
        if ($this->form_validation->run()) {

            try {
                $this->handler->fillPattern($pattern, $this->input->post(), $locale)->save();

            } catch (PatternValidationException $e) {
                return showMessage($e->getMessage(), false, 'r');
            }

            showMessage($successMessage);
            $this->lib_admin->log($successMessage . '. Id: ' . $pattern->getId());
            $this->pjaxResponse($pattern, $locale);

        } else {
            showMessage(validation_errors(), false, 'r');
        }
    }

    /**
     * Default pattern settings handler
     * @param null $locale
     */
    public function settings($locale = null) {

        $locale = $locale ?: MY_Controller::defaultLocale();

        if ($this->input->post()) {

            $post = $this->input->post();
            $post['active'] = isset($post['active']) ? 1 : 0;

            $settings = [];
            foreach ($post as $key => $val) {
                if (in_array($key, ['active', 'h1', 'meta_title', 'meta_description', 'meta_keywords', 'seo_text'])) {
                    $settings[$key] = $val;
                }
            }

            \CMSFactory\ModuleSettings::ofModule(self::NAME)
                ->set($locale, $settings);

            showMessage(lang('Settings has been saved', 'smart_filter'));
            $this->lib_admin->log(lang('Settings in the patterns has been saved', 'smart_filter'));

        } else {

            $settings = \CMSFactory\ModuleSettings::ofModule(self::NAME)
                ->get($locale);
            $data = [
                     'admin_url' => $this->adminUrl,
                     'locale'    => $locale,
                     'languages' => $this->cms_admin->get_langs(true),
                     'urlLocale' => $this->getUrlLocale($locale),
                    ];

            assetManager::create()
                ->setData($settings)
                ->setData($data)
                ->registerScript('admin')
                ->registerStyle('style')
                ->renderAdmin('settings');

        }

    }

    /**
     * Generate patterns
     */
    public function mass_generation() {

        $locale = MY_Controller::defaultLocale();

        if ($this->input->post()) {
            $this->setValidationRules();
            if ($this->form_validation->run()) {

                $numPatterns = $this->handler->generatePatterns($this->input->post(), $locale);

                $this->lib_admin->log(lang('Mass generation patterns', 'smart_filter') . '. ' . lang('Count', 'smart_filter') .': ' . $numPatterns);

                showMessage(lang('Created patters', 'smart_filter') . ': ' . $numPatterns);
            } else {
                showMessage(validation_errors(), false, 'r');
            }
        } else {
            $categories = $this->provider->getCategoriesWithLevels($locale);

            if ($categories->getFirst()) {

                $selectsData = $this->provider->getSelectsData($categories->getFirst()->getId(), $locale, null, true);
            }

            $data = [
                     'admin_url'  => $this->adminUrl,
                     'locale'     => $locale,
                     'categories' => $categories,
                    ];
            assetManager::create()
                ->setData($data)
                ->setData($selectsData)
                ->registerScript('admin')
                ->registerStyle('style')
                ->renderAdmin('mass_generation');

        }

    }

    /**
     * Ajax deletion of patterns
     */
    public function delete() {

        if ($this->ajaxRequest) {
            $ids = $this->input->post('ids');
            if (is_array($ids)) {
                SFilterPatternQuery::create()->setComment(__METHOD__)->filterById($ids, Criteria::IN)->delete();
                showMessage(lang('Items has been successfully removed', 'smart_filter'));
                $this->lib_admin->log(lang('Patterns has been successfully removed', 'smart_filter'));

            }
        } else {
            $this->core->error_404();
        }

    }

    /**
     * Ajax change active patterns
     * @throws PropelException
     */
    public function changeActive() {

        if ($this->ajaxRequest) {

            $id = $this->input->post('id');
            $pattern = SFilterPatternQuery::create()->setComment(__METHOD__)->findOneById($id);
            if ($pattern) {
                $pattern->setActive(!$pattern->isActive())->save();

                $this->lib_admin->log(lang('Patterns status was change', 'smart_filter'));
                showMessage(lang('Change saved successfully', 'admin'));
            }
        } else {

            $this->core->error_404();
        }

    }

    /**
     * Dynamically provides data for brand select
     *
     * @param $categoryId
     * @param string|null $locale
     */
    public function ajaxGetBrandsMultiple($categoryId, $locale = null) {

        if ($this->validateRequestedId($categoryId)) {

            $brands = $this->provider->getBrands($categoryId, $locale);

            $brands = $this->prependSelectOption($brands, 'all', lang('All', 'admin'));
            echo json_encode($brands);
        }
    }

    /**
     * Dynamically provides data for property select
     *
     * @param $categoryId
     * @param string|null $locale
     */
    public function ajaxGetPropertiesMultiple($categoryId, $locale = null) {

        if ($this->validateRequestedId($categoryId)) {

            $properties = $this->provider->getProperties($categoryId, $locale);
            $properties = $this->prependSelectOption($properties, 'all', lang('All', 'admin'));
            echo json_encode($properties);
        }
    }

    /**
     * Dynamically provides data for brand select
     *
     * @param $categoryId
     * @param string|null $locale
     */
    public function ajaxGetBrands($categoryId, $locale = null) {

        if ($this->validateRequestedId($categoryId)) {
            $brands = $this->provider->getBrands($categoryId, $locale);
            $brands = $this->prependSelectOption($brands, '', '-');
            echo json_encode($brands);
        }
    }

    /**
     * Dynamically provides data for property select
     *
     * @param $categoryId
     * @param string|null $locale
     */
    public function ajaxGetProperties($categoryId, $locale = null) {

        if ($this->validateRequestedId($categoryId)) {
            $properties = $this->provider->getProperties($categoryId, $locale);
            $properties = $this->prependSelectOption($properties, '', '-');
            echo json_encode($properties);
        }
    }

    /**
     * Dynamically provides data for property value select
     *
     * @param $propertyId
     * @param string|null $locale
     */
    public function ajaxGetPropertyValues($propertyId, $locale = null) {

        if ($this->validateRequestedId($propertyId)) {
            $propertyValues = $this->provider->getPropertyValues($propertyId, $locale);
            $propertyValues = $this->prependSelectOption($propertyValues, 0, count($propertyValues) ? lang('All', 'admin') : '-');
            echo json_encode($propertyValues);
        }

    }

    private function getUrlLocale($locale) {

        return $locale == MY_Controller::defaultLocale() ? '' : '/' . $locale;
    }

    private function pjaxResponse($pattern, $locale) {

        $url = $this->adminUrl;
        if ($this->input->post('action') !== 'exit') {
            $url .= '/edit/' . $pattern->getId() . $this->getUrlLocale($locale);
        }

        pjax($url);

    }

    private function validateRequestedId($id) {

        return is_numeric($id) && $id > 0;
    }

    /**
     * Prepends value to array of select options
     *
     * @param array $options
     * @param string $name
     * @param string $value
     * @return array
     * @internal param array $option
     */
    private function prependSelectOption($options, $name, $value) {

        if (count($options)) {
            array_unshift($options, ['id' => $name, 'value' => $value]);
        }

        return $options;

    }

    /**
     * Creates validation rules for create/edit post request
     */
    private function setValidationRules() {

        $this->form_validation->set_rules('category_id', lang('Category', 'admin'), 'required');

        if (0 == $this->input->post('brand_id') && 0 == $this->input->post('property_id')) {
            $this->form_validation->set_rules('brand_id', lang('Brand', 'admin'), 'required');
            $this->form_validation->set_rules('property_id', lang('Property', 'admin'), 'required');
        }

    }

}