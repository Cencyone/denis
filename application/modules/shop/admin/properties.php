<?php

use CMSFactory\Events;
use Propel\Runtime\Propel;

/**
 * ShopAdminProperties
 *
 * @property  Lib_admin lib_admin
 * @uses ShopController
 * @package Shop
 * @version $id$
 * @copyright 2010 Siteimage
 * @author <dev@imagecms.net>
 * @license
 *
 */
class ShopAdminProperties extends ShopAdminController
{

    /**
     *
     * @var array
     */
    public $defaultLanguage = [];

    /**
     *
     * @var integer
     */
    public $perPage = 5;

    /**
     * ShopAdminProperties constructor.
     */
    public function __construct() {
        parent::__construct();

        ShopController::checkVar();
        ShopAdminController::checkVarAdmin();

        $this->load->helper('translit');

        $this->defaultLanguage = getDefaultLanguage();

        session_start();

        $this->load->helper('cookie');

        if (!get_cookie('per_page')) {
            setcookie('per_page', ShopCore::app()->SSettings->getAdminProductsPerPage(), time() + 604800, '/', $this->input->server('HTTP_HOST'));
            $this->perPage = ShopCore::app()->SSettings->getAdminProductsPerPage();
        } else {
            $this->perPage = get_cookie('per_page');
        }
    }

    /**
     * Display search form
     *
     * @return void
     */
    public function per_page_cookie() {
        setcookie('per_page', (int) $this->input->get('count_items'), time() + 604800, '/', $this->input->server('HTTP_HOST'));
    }

    /**
     * Display list of properties
     *
     * @access public
     * @param null|int $categoryId
     */
    public function index($categoryId = null) {

        /** Properties Pagination */
        if ($this->input->get('per_page')) {
            $propertiesSession = [
                                  'properties_url' => '?per_page=' . $this->input->get('per_page'),
                                 ];
            $this->session->set_userdata($propertiesSession);
        } else {
            $this->session->unset_userdata('properties_url');
        }

        $this->session->set_userdata('cat_id', $categoryId);
        if ($categoryId === null || $categoryId == 0) {
            $model = SPropertiesQuery::create()->setComment(__METHOD__)->joinWithI18n(MY_Controller::defaultLocale());
            $category = null;
        } else {
            $category = SCategoryQuery::create()->setComment(__METHOD__)->joinWithI18n(MY_Controller::defaultLocale())->findPk((int) $categoryId);
            if ($category !== null) {
                $model = SPropertiesQuery::create()->setComment(__METHOD__)->joinWithI18n(MY_Controller::defaultLocale())
                    ->filterByPropertyCategory($category);
            }
        }
        if ($this->input->get('filterID') && $this->input->get('filterID') > 0) {
            $model = $model->filterById((int) $this->input->get('filterID'));
        }

        if ($this->input->get('Property') && $this->input->get('Property')) {
            $model = $model->useSPropertiesI18nQuery()->filterByLocale(MY_Controller::defaultLocale())->where('SPropertiesI18n.Name LIKE ?', '%' . $this->input->get('Property') . '%')->endUse();
        }

        if ($this->input->get('CSVName') && $this->input->get('CSVName')) {
            $model = $model->where('SProperties.CsvName LIKE ?', '%' . $this->input->get('CSVName') . '%');
        }

        if ($this->input->get('Active') == 'true') {
            $model = $model->filterByActive(true);
        } elseif ($this->input->get('Active') == 'false') {
            $model = $model->filterByActive(false);
        }

        if ($this->input->get('orderMethod') && $this->input->get('orderMethod') != '') {
            $order_methods = [
                              'Id',
                              'Property',
                              'CSVName',
                              'Status',
                             ];
            if (in_array($this->input->get('orderMethod'), $order_methods)) {
                switch ($this->input->get('orderMethod')) {
                    case 'Id':
                        $model = $model->orderById($this->input->get('order'));
                        break;
                    case 'Property':
                        $model = $model->useSPropertiesI18nQuery()->filterByLocale(MY_Controller::defaultLocale())->orderByName($this->input->get('order'))->endUse();
                        break;
                    case 'CSVName':
                        $model = $model->orderByCsvName($this->input->get('order'));
                        break;
                    case 'Status':
                        $model = $model->orderByActive($this->input->get('order'));
                        break;
                    default :
                        $model = $model->orderByPosition();
                        break;
                }
            }
        }

        $model2 = clone $model;
        $model2 = $model2
            ->distinct()
            ->find();

        $totalProperties = $this->getTotalRow();

        $model = $model->offset((int) $this->input->get('per_page'))
            ->limit($this->perPage)
            ->distinct()
            ->_if(!$this->input->get('orderMethod'))
            ->orderByPosition()
            ->_endIf()
            ->find();

        // Create pagination
        $this->load->library('pagination');
        $categoryId = $categoryId == null ? $categoryId : $categoryId . '/';

        $config['base_url'] = '/admin/components/run/shop/properties/index/' . $categoryId . '?' . http_build_query($this->input->get());
        //$config['base_url'] = site_url('admin/components/run/shop/search/index/?'.http_build_query($this->input->get());
        $config['container'] = 'shopAdminPage';
        $config['page_query_string'] = true;
        $config['uri_segment'] = 8;
        $config['total_rows'] = $totalProperties;
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

        $propertiesCategories = $this->db
            ->select('property_id')
            ->select('GROUP_CONCAT(category_id) as categories')
            ->group_by('property_id')
            ->get('shop_product_properties_categories')
            ->result_array();

        $categories = SCategoryQuery::create()->getTree(0, SCategoryQuery::create()->joinWithI18n(MY_Controller::defaultLocale()))->getCollection();

        $idsPropArr = [];
        foreach ($categories as $oneCategory) {
            foreach ($propertiesCategories as $propertyCategories) {
                if (in_array($oneCategory->getId(), explode(',', $propertyCategories['categories']))) {
                    $idsPropArr[$propertyCategories['property_id']][] = $oneCategory->getName();
                }
            }
        }

        $this->render(
            'list',
            [
             'model'          => $model,
             'categories'     => $categories,
             'filterCategory' => $category,
             'pagination'     => $this->pagination->create_links_ajax(),
             'locale'         => $this->defaultLanguage['identif'],
             'p_cat'          => $idsPropArr,
            ]
        );
    }

    /**
     * Create new product property
     *
     * @access public
     */
    public function create() {
        $postData = $this->input->post();

        $model = new SProperties;

        Events::create()->registerEvent('', 'ShopAdminProperties:preCreate');
        Events::runFactory();

        if ($this->input->post()) {
            $this->form_validation->set_rules($model->rules());

            $this->form_validation->set_rules('CsvName', lang('CSV column name', 'admin'), 'required|alpha_dash');

            if ($this->form_validation->run($this) == FALSE) {
                showMessage(validation_errors(), '', 'r');
            } else {

                if (!$this->input->post('CsvName')) {
                    $postData['CsvName'] = translit($this->input->post('Name'));
                }

                /** Check csv name * */
                if ($this->checkCsvName($this->input->post('CsvName'))) {
                    showMessage(lang('Csv name is already used', 'admin'), '', 'r');
                    return;
                }

                $model->fromArray($postData);
                if (in_array('all', $this->input->post('UseInCategories'))) {
                    $categoriesModel = SCategoryQuery::create()->setComment(__METHOD__)->find();
                    foreach ($categoriesModel as $category) {
                        $model->addPropertyCategory($category);
                    }
                } else {
                    // Assign property categories
                    if (count($this->input->post('UseInCategories')) > 0 && is_array($this->input->post('UseInCategories'))) {
                        $ids = $this->input->post('UseInCategories');
                        $categoriesModel = SCategoryQuery::create()->setComment(__METHOD__)->filterById($ids)->find();
                        foreach ($categoriesModel as $category) {
                            $model->addPropertyCategory($category);
                        }
                    }
                }

                $model->save();
                $model->setPosition(-$model->getId());
                $model->save();

                $this->lib_admin->log(lang('Property created', 'admin') . '. Id: ' . $model->getId());
                showMessage(lang('Property created', 'admin'));

                Events::create()->registerEvent(['model' => $model, 'userId' => $this->dx_auth->get_user_id()], 'ShopAdminProperties:create');
                Events::runFactory();

                if ($this->input->post('action') == 'tomain') {
                    pjax('/admin/components/run/shop/properties/index');
                } else {
                    pjax('/admin/components/run/shop/properties/edit/' . $model->getId());
                }
            }
        } else {
            $this->render(
                'create',
                [
                 'model'      => $model,
                 'categories' => SCategoryQuery::create()->getTree(0, SCategoryQuery::create()->joinWithI18n(MY_Controller::defaultLocale()))->getCollection(),
                 'locale'     => $this->defaultLanguage['identif'],
                 'filter'     => $this->session->userdata('cat_id'),
                ]
            );
        }
    }

    /**
     * fast create property
     */
    public function createPropFast() {

        Events::create()->registerEvent('', 'ShopAdminProperties:preFastCreate');
        Events::runFactory();

        if ($this->input->post()) {

            $postData = $this->input->post();
            $model = new SProperties;

            $this->form_validation->set_rules($model->rules());

            $this->form_validation->set_rules('CsvName', lang('CSV column name', 'admin'), 'required|alpha_dash');

            if ($this->form_validation->run() === FALSE) {
                echo json_encode(
                    [
                     'error' => 1,
                     'data'  => validation_errors(),
                    ]
                );
                exit;
            } else {
                if (!$this->input->post('CsvName')) {
                    $postData['CsvName'] = translit($this->input->post('Name'));
                }

                /** Check csv name * */
                if ($this->checkCsvName($this->input->post('CsvName'))) {
                    echo json_encode(
                        [
                         'error' => 1,
                         'data'  => lang('Csv name is already used', 'admin'),
                        ]
                    );
                    exit;
                }

                $postData['Active'] = (int) $this->input->post('active');
                $postData['ShowOnSite'] = 1;

                $model->fromArray($postData);
                // Assign property categories

                $allCategory = false;
                foreach ($this->input->post('inCat') as $inCat) {
                    if ($inCat == '0') {
                        $allCategory = true;
                    }
                }
                if ($allCategory) {
                    $categoriesModel = SCategoryQuery::create()->setComment(__METHOD__)->find();
                    foreach ($categoriesModel as $category) {
                        $model->addPropertyCategory($category);
                    }
                } else {
                    $categoriesModel = SCategoryQuery::create()->setComment(__METHOD__)->findById($this->input->post('inCat'));
                    foreach ($categoriesModel as $category) {
                        $model->addPropertyCategory($category);
                    }
                }

                $model->save();
                $model->setPosition(-$model->getId());
                $model->save();

                $categories = $this->db->where('locale', MY_Controller::defaultLocale())
                    ->get('shop_category_i18n')
                    ->result_array();
                $catName = [];
                foreach ($categories as $c) {
                    $catName[$c['id']]['name'] = $c['name'];
                }
                $prop_cat = $this->db
                    ->join('shop_category', 'shop_product_properties_categories.category_id = shop_category.id')
                    ->where('shop_product_properties_categories.property_id', $model->getId())
                    ->order_by('position')
                    ->get('shop_product_properties_categories')
                    ->result_array();

                $idsPropArr = [];
                foreach ($prop_cat as $p_c) {
                    $idsPropArr[$p_c['property_id']][] = $catName[$p_c['category_id']]['name'];
                }

                Events::create()->registerEvent(['model' => $model, 'userId' => $this->dx_auth->get_user_id()], 'ShopAdminProperties:fastCreate');
                Events::runFactory();

                $fastPropertyCreateView = $this->render('fastPropertyCreate', ['open_fast_create' => TRUE], TRUE);
                $onePropertyListView = $this->render('onePropertyListView', ['p' => $model, 'p_cat' => $idsPropArr], TRUE);
                echo json_encode(
                    [
                     'error'                  => 0,
                     'data'                   => lang('Property created', 'admin'),
                     'fastPropertyCreateView' => $fastPropertyCreateView,
                     'onePropertyListView'    => $onePropertyListView,
                    ]
                );
            }
        }
    }

    /**
     * Edit property
     *
     * @param integer $propertyId
     * @access public
     */
    public function edit($propertyId = null, $locale = null) {
        $locale = $locale == null ? $this->defaultLanguage['identif'] : $locale;

        $model = SPropertiesQuery::create()
            ->findPk((int) $propertyId);

        if ($model === null) {
            $this->error404(lang('The property is not found', 'admin'));
        }

        $propertiesSession = $this->session->userdata('properties_url');
        $propertiesSession = $propertiesSession ? $propertiesSession : null;

        $postData = $this->input->post();
        if ($postData) {
            $this->form_validation->set_rules($model->rules());
            /** Check csv name * */
            if ($this->checkCsvName($this->input->post('CsvName'), $propertyId)) {
                showMessage(lang('Csv name is already used', 'admin'), '', 'r');
                return;
            }

            $this->form_validation->set_rules('CsvName', lang('CSV column name', 'admin'), 'required|alpha_dash');

            if ($this->form_validation->run($this) == FALSE) {
                showMessage(validation_errors(), '', 'r');
            } else {

                $checkboxes = [
                               'Active',
                               'ShowInCompare',
                               'ShowInFilter',
                               'ShowOnSite',
                               'Multiple',
                               'MainProperty',
                               'ShowFaq',
                              ];
                $postData = $this->input->post();
                foreach ($checkboxes as $name) {
                    $postData[$name] = $postData[$name] ? 1 : 0;
                }

                $model->fromArray($postData);

                /**
                 * Clear product properties data if category will not use
                 */

                if ($locale == MY_Controller::defaultLocale()) {

                    $this->deleteProductPropertiesData($model, $postData['UseInCategories']);

                    ShopProductPropertiesCategoriesQuery::create()
                        ->filterByPropertyId($model->getId())
                        ->delete();
                }

                if (in_array('all', $postData['UseInCategories'])) {
                    $categoriesModel = SCategoryQuery::create()->setComment(__METHOD__)->find();
                    foreach ($categoriesModel as $category) {
                        $model->addPropertyCategory($category);
                    }
                } else {

                    // Assign property categories
                    if (count($postData['UseInCategories']) > 0 && is_array($postData['UseInCategories'])) {
                        $ids = $this->input->post('UseInCategories');
                        $categoriesModel = SCategoryQuery::create()->setComment(__METHOD__)->filterById($ids)->find();

                        foreach ($categoriesModel as $category) {
                            $model->addPropertyCategory($category);
                        }
                    }
                }

                $model->save();

                $this->lib_admin->log(lang('Property edited', 'admin') . '. Id: ' . $propertyId);

                Events::create()->registerEvent(['model' => $model, 'userId' => $this->dx_auth->get_user_id()], 'ShopAdminProperties:edit');
                Events::runFactory();

                showMessage(lang('Changes saved', 'admin'));
                if ($postData['action'] == 'tomain') {
                    pjax('/admin/components/run/shop/properties/index' . $propertiesSession);
                }
                if ($postData['action'] == 'tocreate') {
                    pjax('/admin/components/run/shop/properties/create');
                }
                if ($postData['action'] == 'toedit') {
                    pjax('/admin/components/run/shop/properties/edit/' . $model->getId() . '/' . $locale);
                }
            }
        } else {
            $model->setLocale($locale);

            $propertyCategories = [];
            foreach ($model->getPropertyCategories() as $propertyCategory) {
                array_push($propertyCategories, $propertyCategory->getId());
            }

            $this->render(
                'edit',
                [
                 'model'                => $model,
                 'categories'           => SCategoryQuery::create()->getTree(0, SCategoryQuery::create()->joinWithI18n($locale))->getCollection(),
                 'propertyCategories'   => $propertyCategories,
                 'languages'            => ShopCore::$ci->cms_admin->get_langs(true),
                 'locale'               => $locale,
                 'filter'               => $this->session->userdata('cat_id'),
                 'propertiesPagination' => $propertiesSession,
                 'defaultLocale'        => MY_Controller::defaultLocale(),
                ]
            );
        }
    }

    /**
     *
     */
    public function addPropertyValue() {

        if (!$this->input->is_ajax_request()) {
            $this->error404(lang('Page not found'));
        }
        $propertyId = $this->input->post('property_id');
        $value = $this->input->post('value');
        $locale = $this->input->post('locale') ?: MY_Controller::defaultLocale();

        $property = SPropertiesQuery::create()->setComment(__METHOD__)->findOneById($propertyId);

        $propertyValue = SPropertyValueQuery::create()
            ->joinWithI18n($locale)
            ->useI18nQuery($locale)
            ->filterByValue($value)
            ->endUse()
            ->findOneByPropertyId($propertyId);

        $id = false;
        if (!$property) {
            $success = false;
            $message = lang('No such property');

        } elseif ($propertyValue !== null) {
            $success = false;
            $message = langf('Value |value| already exists', 'main', ['value' => $value]);
            $id = $propertyValue->getId();
        } else {
            $success = true;
            $message = lang('New value created');

            $property_values = SPropertyValueQuery::create()
                ->filterByPropertyId($propertyId)
                ->orderByPosition()
                ->find();

            /** Change new position properties */
            foreach ($property_values as $property_value) {
                $property_value->setPosition($property_value->getPosition() + 1);
            }
            $property_values->save();

            $propertyValue = new SPropertyValue();
            $propertyValue->setLocale($locale)
                ->setPropertyId($propertyId)
                ->setPosition(1)
                ->setValue($value)
                ->save();
            $id = $propertyValue->getId();
        }

        echo json_encode(compact('id', 'message', 'success'));
    }

    /**
     * Delete product properties data
     * @param SProperties $model - properties model
     * @param array $currentUseInCategories - array with categories ids in use
     */
    private function deleteProductPropertiesData($model, $currentUseInCategories) {
        $modelPropertyCategories = ShopProductPropertiesCategoriesQuery::create()
            ->filterByPropertyId($model->getId())
            ->find()
            ->toArray();

        $categoriesInUse = [];
        if ($modelPropertyCategories) {

            foreach ($modelPropertyCategories as $propertyCategory) {
                $categoriesInUse[] = (string) $propertyCategory['CategoryId'];
            }

            $categoriesIdsDeleted = array_diff($categoriesInUse, $currentUseInCategories);

            if ($categoriesIdsDeleted) {
                $products = SProductsQuery::create()
                    ->orderById()
                    ->filterByCategoryId($categoriesIdsDeleted)
                    ->find();

                $productsIds = [];
                foreach ($products as $product) {
                    $productsIds[] = $product->getId();
                }

                if ($productsIds) {
                    SProductPropertiesDataQuery::create()
                        ->filterByProductId($productsIds)
                        ->filterByPropertyId($model->getId())
                        ->delete();
                }
            }
        }
    }

    /**
     * Check exists csv name. Uses for validation.
     * @param string $csvName
     * @return boolean
     */
    public function checkCsvName($csvName = '', $propertyId = null) {
        $this->db->where('csv_name', $csvName);
        if ($propertyId != null) {
            $this->db->where('id <>', $propertyId);
        }

        $result = $this->db->get('shop_product_properties')->row_array();

        if ($result) {
            $this->form_validation->set_message('checkCsvName', lang('Csv name is already used', 'admin'));
            return TRUE;
        }

        return FALSE;
    }

    /**
     * Render properties form for create/edit product.
     *
     * @param integer $categoryId
     * @param integer $productId
     * @return void
     */
    public function renderForm($categoryId, $productId = null) {
        $result = ShopCore::app()->SPropertiesRenderer->renderAdmin($categoryId, SProductsQuery::create()->setComment(__METHOD__)->findPk((int) $productId));

        if ($result == false) {
            echo '<div id="notice" style="width: 500px;">' . lang('The list of properties is empty', 'admin') . '
						<a href="#" onclick="ajaxShop(\'properties/create\'); return false;">' . lang('Create', 'admin') . '.</a>
					</div>';
        } else {
            echo $result;
        }
    }

    /**
     * @return bool
     */
    public function save_positions() {
        $positions = $this->input->post('positions');
        if (count($positions) == 0) {
            return false;
        }
        $arr = '(';
        $i = 0;
        foreach (array_values($positions) as $item) {
            $arr .= "'" . $item . "'";
            if ($i < count($positions) - 1) {
                $arr .= ', ';
            } else {
                $arr .= ')';
            }
            $i++;
        }

        foreach ($positions as $key => $val) {
            $query = 'UPDATE `shop_product_properties` SET `position`=' . $key . ' WHERE `id`=' . (int) $val . '; ';
            $this->db->query($query);
        }
        showMessage(lang('Positions saved', 'admin'));
    }

    /**
     * Delete property
     */
    public function delete() {
        $id = $this->input->post('ids');

        $model = SPropertiesQuery::create()
            ->findPks($id);

        if ($model === null) {
            return false;
        }

        foreach ($model as $item) {
            Events::create()->raiseEvent(['model' => $item], 'ShopAdminProperties::delete');
            $item->delete();
        }

        $this->lib_admin->log(lang('The property(ies) deleted', 'admin') . '. Ids: ' . implode(', ', $id));
        showMessage(lang('The property(ies) deleted', 'admin'), lang('Message', 'admin'));
        pjax('/admin/components/run/shop/properties/index');
    }

    /**
     *
     */
    public function changeActive() {
        $id = $this->input->post('id');

        $prop = $this->db->where('id', $id)->get('shop_product_properties')->row();
        $active = $prop->active;
        if ($active == 1) {
            $active = 0;
        } else {
            $active = 1;
        }

        if ($this->db->where('id', $id)->update('shop_product_properties', ['active' => $active])) {
            showMessage(lang('Change saved successfully', 'admin'));
        }

        /* $model = SPropertiesQuery::create()->setComment(__METHOD__)->findPk($id);
          if (count($model) > 0) {
          $model->setActive(!$model->getActive());
          if ($model->save()) {
          showMessage('Измения успешно сохранены');
          }
          } */
    }

    /**
     * @return mixed
     */
    private function getTotalRow() {
        $connection = Propel::getConnection('Shop');
        $statement = $connection->prepare('SELECT FOUND_ROWS() as `number`');
        $statement->execute();
        $resultset = $statement->fetchAll();
        return $resultset[0]['number'];
    }

}