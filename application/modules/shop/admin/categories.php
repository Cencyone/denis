<?php

use Category\CategoryApi;
use CMSFactory\Events;

/**
 * ShopAdminCategories
 *
 * @property Lib_admin $lib_admin
 * @property Cms_admin cms_admin
 * @uses ShopController
 * @package
 * @version $id$
 * @copyright 2010 Siteimage
 * @author <dev@imagecms.net>
 * @license
 */
class ShopAdminCategories extends ShopAdminController
{

    /**
     * @var array
     */
    public $categories2;

    private $categoryApi;

    public $defaultLanguage = null;

    /**
     * @var array
     */
    private $prod_count = [];

    /**
     * @var string
     */
    private $templatePath;

    /**
     * @var array|bool
     */
    public $tree;

    public function __construct() {

        parent::__construct();
        ShopController::checkVar();
        ShopAdminController::checkVarAdmin();
        $this->defaultLanguage = getDefaultLanguage();
        $this->templatePath = ShopCore::app()->SSettings->getSystemTemplatePath();
        $this->templatePath = str_replace('./', '', $this->templatePath) . '/';
        $this->categoryApi = CategoryApi::getInstance();
    }

    public function ajax_load_parent() {

        $id = (int) $this->input->post('id');
        $locale = MY_Controller::getCurrentLocale();

        $subCats = SCategoryQuery::create()->getTree($id, SCategoryQuery::create()->joinWithI18n($locale));

        echo $this->printCategoryTree($subCats, true);
    }

    /**
     * Transilt title to url
     */
    public function ajax_translit() {

        $this->load->helper('translit');
        $str = $this->input->post('str');
        echo translit_url($str);
    }

    public function changeActive() {

        $id = $this->input->post('id');
        $model = SCategoryQuery::create()->setComment(__METHOD__)->findPk($id);
        if (count($model) > 0) {
            $model->setActive(!$model->getActive());
            if ($model->save()) {
                $message = ($model->getActive() ? lang('Category activated.', 'admin') : lang('Category deactivated.', 'admin')) . ' '
                    . lang('Category ID:') . ' '
                    . $id;
                $this->lib_admin->log($message);
                showMessage(lang('Changes saved', 'admin'));
            }
            $this->cache->clearCacheFolder('category');
        }
    }

    /**
     * Create new category.
     *
     * @access public
     */
    public function create() {

        $model = new SCategory();
        $locale = MY_Controller::getCurrentLocale();

        Events::create()->registerEvent('', 'ShopAdminCategories:preCreate');
        Events::runFactory();

        if ($this->input->post()) {
            $this->form_validation->set_rules($model->rules());

            if ($this->form_validation->run() == FALSE) {
                showMessage(validation_errors(), '', 'r');
            } else {

                $postData = $this->input->post();
                $data = [
                         'tpl'           => $postData['tpl'],
                         'order_method'  => $postData['order_method'],
                         'showsitetitle' => $postData['showsitetitle'],
                         'parent_id'     => $postData['ParentId'],
                         'url'           => $postData['Url'],
                         'active'        => (int) $postData['Active'],
                         'show_in_menu'  => (int) $postData['show_in_menu'] ?: 0,
                         'image'         => $postData['Image'],
                         'name'          => $postData['Name'],
                         'h1'            => $postData['H1'],
                         'description'   => $postData['Description'],
                         'meta_desc'     => $postData['MetaDesc'],
                         'meta_title'    => $postData['MetaTitle'],
                         'meta_keywords' => $postData['MetaKeywords'],
                         'position'      => NULL,
                         'external_id'   => NULL,
                         'created'       => time(),
                         'updated'       => time(),
                        ];

                $model = $this->categoryApi->addCategory($data, $locale);

                if ($this->categoryApi->getError()) {
                    showMessage($this->categoryApi->getError(), '', 'r');
                    exit;
                }

                Events::create()->registerEvent(['ShopCategoryId' => $model->getId(), 'model' => $model])->runFactory();

                $last_cat_id = $this->db->order_by('id', 'desc')->get('shop_category')->row()->id;
                $this->lib_admin->log(lang('Category created', 'admin') . '. Id: ' . $last_cat_id);
                showMessage(lang('Category created', 'admin'));
                if ($postData['action'] === 'close') {
                    pjax('/admin/components/run/shop/categories/index');
                }

                if ($postData['action'] === 'edit') {
                    pjax('/admin/components/run/shop/categories/edit/' . $model->getId() . '/' . $locale);
                }
                $this->cache->clearCacheFolder('category');
            }
        } else {
            $this->render(
                'create',
                [
                 'model'      => $model,
                 'categories' => SCategoryQuery::create()->getTree(0, SCategoryQuery::create()->joinWithI18n($locale))->getCollection(), // Categories array for parent_id dropdown.
                 'tpls'       => $this->get_tpl_names($this->templatePath),
                 'sortings'   => ShopCore::app()->SSettings->getSortingFront(),
                ]
            );
        }
    }

    /**
     * @param string $directory
     * @return array
     */
    public function get_tpl_names($directory) {

        $arr = scandir($directory);
        foreach ($arr as $item) {
            if (is_file($directory . '/' . $item)) {
                $a = explode('.', $item);
                if ($a[1] == 'tpl') {
                    $result[] = str_replace('.tpl', '', $item);
                }
            }
        }
        return $result;
    }

    /**
     * fast create category
     */
    public function createCatFast() {

        Events::create()->registerEvent('', 'ShopAdminCategories:preFastCreate');
        Events::runFactory();

        if ($this->input->post()) {

            $model = new SCategory;

            $this->form_validation->set_rules($model->rules());

            if ($this->form_validation->run() === FALSE) {
                echo json_encode(
                    [
                     'error' => 1,
                     'data'  => validation_errors(),
                    ]
                );
                exit;
            } else {
                $locale = MY_Controller::getCurrentLocale();

                $postData = $this->input->post();

                $data = [
                         'parent_id'    => (int) $postData['catId'],
                         'url'          => $postData['Url'],
                         'active'       => (int) $postData['active'],
                         'show_in_menu' => (int) $postData['show_in_menu'],
                         'name'         => $postData['Name'],
                         'created'      => time(),
                         'updated'      => time(),
                        ];

                $model = $this->categoryApi->addCategory($data, $locale);

                Events::create()->registerEvent(['model' => $model, 'userId' => $this->dx_auth->get_user_id()], 'ShopAdminCategories:fastCreate');
                Events::runFactory();

                if ($this->categoryApi->getError()) {
                    echo json_encode(
                        [
                         'error' => 1,
                         'data'  => $this->categoryApi->getError(),
                        ]
                    );

                    exit;
                }
                echo json_encode(
                    [
                     'error' => 0,
                     'data'  => lang('Category created', 'admin'),
                    ]
                );
            }
        }
    }

    public function create_tpl() {

        $file = trim($this->input->post('filename'));

        $this->form_validation->set_rules('filename', lang('Template name', 'admin'), 'required|alpha_dash|min_length[1]|max_length[250]');

        if ($this->form_validation->run() == FALSE) {
            $responce = showMessage(validation_errors(), '', 'r', true);
            $result = false;
            echo json_encode(['responce' => $responce, 'result' => $result]);
            return FALSE;
        }

        $file = $this->templatePath . $file . '.tpl';
        if (!file_exists($file)) {
            $fp = fopen($file, 'w');
            if ($fp) {
                $responce = showMessage(lang('The file has been successfully created', 'admin'), '', '', true);
                $result = true;
            } else {
                $responce = showMessage(lang('Could not create file', 'admin'), '', 'r', true);
                $result = false;
            }
            fwrite($fp, '/* new ImageCMS Tpl file */');
            fclose($fp);
            echo json_encode(['responce' => $responce, 'result' => $result]);
        } else {
            $responce = showMessage(lang('File with the same name is already exist.'), '', 'r', true);
            $result = false;
            echo json_encode(['responce' => $responce, 'result' => $result]);
            return FALSE;
        }
    }

    /**
     * Delete category
     *
     * @access public
     * @return void
     */
    public function delete() {

        // Get category id
        $category_id = $this->input->post('id');

        Events::create()->registerEvent(['ShopCategoryId' => $this->input->post('id')])->runFactory();

        // Delete category
        if ($category_id) {
            $result = $this->categoryApi->deleteCategory($category_id);
            if (!$result) {
                showMessage(lang('Failed to delete the category', 'admin'), '', 'r');
                showMessage($this->categoryApi->getError(), '', 'r');
            } else {
                $this->lib_admin->log(lang('Category deleted', 'admin') . '. Ids: ' . implode(', ', $category_id));
                showMessage(lang('Category deleted successfully', 'admin'));
                $this->cache->clearCacheFolder('category');
            }
        } else {
            showMessage(lang('Failed to delete the category', 'admin'));
        }

    }

    /**
     * Edit category
     *
     * @access public
     * @param integer $id
     * @param string $locale
     */
    public function edit($id = null, $locale = null) {
        $locale = $locale == null ? $this->defaultLanguage['identif'] : $locale;
        $currentLocale = MY_Controller::getCurrentLocale();
        $model = SCategoryQuery::create()->setComment(__METHOD__)->joinWithI18n($currentLocale)->findPk((int) $id);

        Events::create()->registerEvent(['model' => $model, 'userId' => $this->dx_auth->get_user_id(), 'url' => $model->getUrl()], 'ShopAdminCategories:preEdit');
        Events::runFactory();

        if ($model === null) {
            $this->error404(lang('Category not found', 'admin'));
        }
        /**
         *  Update category data
         */
        if ($this->input->post()) {

            $validation = $this->form_validation->set_rules($model->rules());
            $validation = $model->validateCustomData($validation);

            if ($validation->run($this) == FALSE) {
                showMessage(validation_errors(), '', 'r');
            } else {

                $postData = $this->input->post();

                $data = [
                         'tpl'           => $postData['tpl'],
                         'order_method'  => $postData['order_method'],
                         'showsitetitle' => $postData['showsitetitle'],
                         'show_in_menu'  => (int) $postData['show_in_menu'] ?: 0,
                         'parent_id'     => $postData['ParentId'],
                         'url'           => $postData['Url'],
                         'active'        => (int) $postData['Active'],
                         'image'         => $postData['Image'],
                         'name'          => $postData['Name'],
                         'h1'            => $postData['H1'],
                         'description'   => $postData['Description'],
                         'meta_desc'     => $postData['MetaDesc'],
                         'meta_title'    => $postData['MetaTitle'],
                         'meta_keywords' => $postData['MetaKeywords'],
                         'updated'       => time(),
                        ];

                $model = $this->categoryApi->updateCategory($id, $data, $locale);

                if ($this->categoryApi->getError()) {
                    showMessage($this->categoryApi->getError(), '', 'r');
                    exit;
                }

                /** Init Event. Edit ShopCategory */
                Events::create()->registerEvent(['ShopCategoryId' => $model->getId(), 'url' => $model->getUrl(), 'model' => $model])->runFactory();
                /** End init Event. Edit ShopCategory */
                $this->lib_admin->log(lang('Category edited', 'admin') . '. Id: ' . $id);
                showMessage(lang('Changes saved', 'admin'));

                if ($postData['action'] == 'close') {
                    pjax('/admin/components/run/shop/categories/index');
                }

                if ($postData['action'] == 'edit') {
                    pjax('/admin/components/run/shop/categories/edit/' . $model->getId() . '/' . $locale);
                }
                $this->cache->clearCacheFolder('category');
            }
        } else {
            $model->setLocale($locale);
            $this->load->helper('cookie');
            set_cookie('category_full_path_ids', json_encode(unserialize($model->getFullPathIds())), 60 * 60 * 60);

            $this->render(
                'edit',
                [
                 'model'      => $model,
                 'modelArray' => $model->toArray(),
                 'categories' => SCategoryQuery::create()->getTree(0, SCategoryQuery::create()->joinWithI18n($locale))->getCollection(),
                 'languages'  => ShopCore::$ci->cms_admin->get_langs(true),
                 'locale'     => $locale,
                 'tpls'       => $this->get_tpl_names($this->templatePath),
                 'sortings'   => ShopCore::app()->SSettings->getSortingFront(),
                 'addField'   => ShopCore::app()->CustomFieldsHelper->getCustomFields('category', $model->getId())->asAdminHtml(),
                ]
            );
            exit;
        }
    }

    public function index() {

        $tree = SCategoryQuery::create()->getTree(
            0,
            SCategoryQuery::create()
                ->joinWithI18n(MY_Controller::defaultLocale())
        );

        $this->render(
            'list',
            [
             'tree'      => $tree->getCollection(),
             'htmlTree'  => $this->printCategoryTree($tree),
             'languages' => $this->cms_admin->get_langs(true),
            ]
        );
    }

    private function printCategoryTree($tree = false, $ajax = false) {

        $this->prod_count = $this->getProductsCount();

        $output = '';
        if (!$ajax) {
            $output .= '<div class="sortable save_positions" data-url="/admin/components/run/shop/categories/save_positions">';
        } else {
            $output .= '<div class="frame_level sortable" style="display: block" data-url="/admin/components/run/shop/categories/save_positions">';
        }

        foreach ($tree as $c) {
            $output .= $this->printCategory($c);
        }

        $output .= '</div>';

        return $output;

    }

    /**
     * Load product counts
     */
    private function getProductsCount() {

        return SProductsQuery::create()
            ->select(['category_id'])
            ->withColumn('COUNT(category_id)', 'prod_count')
            ->groupByCategoryId()
            ->find()
            ->toKeyValue('category_id', 'prod_count');
    }

    /**
     * @param SCategory $category
     * @return string
     */
    private function printCategory($category) {

        $catToDisplay = new stdClass();

        $name = $category->getName() ? $category->getName() : lang('Тo translation', 'admin') . ' (' . MY_Controller::getCurrentLocale() . ')';
        $catToDisplay->id = $category->getId();
        $catToDisplay->parent = ($category->getSCategory() != null) ? $category->getSCategory()->getName() : '-';
        $catToDisplay->name = $name;
        $catToDisplay->url = $category->getRouteUrl();
        $catToDisplay->active = $category->getActive();
        $level = count(explode('/', $catToDisplay->url));
        $catToDisplay->level = $level;

        $catToDisplay->hasChilds = (bool) $category->hasSubItems();
        $catToDisplay->myProdCnt = (int) $this->prod_count[$category->getId()];
        $catToDisplay->show_in_menu = (bool) $category->getShowInMenu();

        $output = '<div>';

        $this->template->assign('cat', $catToDisplay);
        $output .= $this->template->fetch('file:' . $this->getViewFullPath('_listItem'));

        $output .= '</div>';

        unset($catToDisplay);

        return $output;
    }

    /**
     * Save categories position.
     *
     * @access public
     * @return void
     */
    public function save_positions() {

        $result = CI::$APP->db
            ->select(['parent_id', 'position'])
            ->get_where('shop_category', ['id' => (int) $this->input->post('categoryId')])
            ->row_array();

        $this->categories2 = CI::$APP->db
            ->select(['id', 'parent_id', 'position'])
            ->order_by('position', 'asc')
            ->get('shop_category')
            ->result_array();

        $minPos = count($this->categories2);
        $neededLevelChilds = [];
        foreach ($this->input->post('positions') as $categoryId) {
            foreach ($this->categories2 as $categoryData) {
                if ($categoryData['id'] == $categoryId && $result['parent_id'] == $categoryData['parent_id']) {
                    $neededLevelChilds[$categoryData['id']] = $this->getChildsRecursive($categoryData['id']);
                    $minPos = $minPos > $categoryData['position'] ? $categoryData['position'] : $minPos;
                    break;
                }
            }
        }

        $positions = [];
        foreach ($neededLevelChilds as $categoryId => $childs) {
            $positions[] = [
                            'id'       => $categoryId,
                            'position' => $minPos++,
                           ];
            foreach (array_keys($childs) as $categoryId_) {
                $positions[] = [
                                'id'       => $categoryId_,
                                'position' => $minPos++,
                               ];
            }
        }

        CI::$APP->db->update_batch('shop_category', $positions, 'id');
        showMessage(lang('Positions saved', 'admin'));
        $this->cache->clearCacheFolder('category');
    }

    /**
     * Returns all child and their positions of category (from all sub-levels)
     * @param integer $categoryId
     * @return array
     */
    private function getChildsRecursive($categoryId) {

        $childsPositions = [];
        foreach ($this->categories2 as $categoryData) {
            if ($categoryId == $categoryData['parent_id']) {
                $childsPositions[$categoryData['id']] = $categoryData['position'];
                $subChilds = $this->getChildsRecursive($categoryData['id']);
                if (count($subChilds) > 0) {
                    foreach ($subChilds as $categoryId_ => $position_) {
                        $childsPositions[$categoryId_] = $position_;
                    }
                }
            }
        }
        return $childsPositions;
    }

    /**
     * @param  int $cat_id
     * @throws \Propel\Runtime\Exception\PropelException
     * @return void
     */
    public function ajaxChangeShowInSite($cat_id) {

        $model = SCategoryQuery::create()->setComment(__METHOD__)->findPk($cat_id);

        if (count($model) > 0) {
            $model->setShowInMenu(!$model->getShowInMenu());
            if ($model->save()) {

                $message = ($model->getShowInMenu() ? lang('Category show in menu.', 'admin') : lang("Category don't show in menu.", 'admin')) . ' '
                    . lang('Category ID:') . ' '
                    . $cat_id;
                $this->lib_admin->log($message);
                showMessage(lang('Changes saved', 'admin'));
            }
            $this->cache->clearCacheFolder('category');
        }
        Events::create()->raiseEvent(['model' => $model], 'ShopAdminCategories:ajaxChangeShowInSite');

    }

}