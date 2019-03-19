<?php

use CMSFactory\Events;
use Propel\Runtime\ActiveQuery\Criteria;
use template_manager\classes\TemplateManager;

/**
 * ShopAdminBrands
 *
 * @uses ShopController
 * @package
 * @version $id$
 * @copyright 2010 Siteimage
 * @author <dev@imagecms.net>
 * @license
 * @property SBrands $model
 * @property Cms_admin cms_admin
 * @property Lib_admin lib_admin
 */
class ShopAdminBrands extends ShopAdminController
{

    /**
     * @var string
     */
    public $imagePath = './uploads/shop/brands/';

    /**
     * @var array|null
     */
    public $defaultLanguage = null;

    /**
     * @var array
     */
    protected $allowedImageExtensions = [
                                         'jpeg',
                                         'jpg',
                                         'png',
                                         'gif',
                                        ];

    /**
     * @var null
     */
    protected $current_extension = null;

    /**
     *
     * @var array
     */
    protected $imageSizes = [
                             'mainImageWidth'  => 120,
                             'mainImageHeight' => 61,
                            ];

    /**
     *
     * @var integer
     */
    protected $imageQuality = 99;

    /**
     *
     * @var integer
     */
    protected $per_page = 10;

    /**
     * ShopAdminBrands constructor.
     */
    public function __construct() {

        parent::__construct();
        \ShopController::checkVar();
        \ShopAdminController::checkVarAdmin();
        $this->defaultLanguage = getDefaultLanguage();
        $this->load->library('upload');
        $this->load->helper('translit');

        $params = TemplateManager::getInstance()->getCurentTemplate()->getParams()->getValue('brands');
        if (count($params) > 0 && isset($params['image']['width']) && isset($params['image']['width'])) {
            $this->imageSizes['mainImageWidth'] = $params['image']['width'];
            $this->imageSizes['mainImageHeight'] = $params['image']['height'];
        }

        if (!$this->input->cookie('per_page')) {
            setcookie('per_page', ShopCore::app()->SSettings->getAdminProductsPerPage(), time() + 604800, '/', $this->input->server('HTTP_HOST'));
            $this->per_page = ShopCore::app()->SSettings->getAdminProductsPerPage();
        } else {
            $this->per_page = $this->input->cookie('per_page');
        }
    }

    /**
     *
     */
    public function index() {

        $model = SBrandsQuery::create()
            ->joinWithI18n(\MY_Controller::defaultLocale(), Criteria::JOIN);

        //////**********  Pagination pages **********\\\\\\\
        if ($this->input->get('per_page')) {
            $orderSession = [
                             'brand_url' => '?per_page=' . $this->input->get('per_page'),
                            ];
            $this->session->set_userdata($orderSession);
        } else {
            $this->session->unset_userdata('brand_url');
        }
        if ($this->input->get('brand_name')) {
            $model->where('SBrandsI18n.Name LIKE ?', '%' . $this->input->get('brand_name') . '%');
        }

        if ($this->input->get('brand_id')) {
            $model->filterById($this->input->get('brand_id'));
        }

        $modelForCount = clone $model;

        $model = $model
            ->distinct()
            ->orderByPosition(Criteria::DESC)
            ->limit($this->per_page)
            ->offset($this->input->get('per_page'))
            ->find();

        $total = $modelForCount->count();

        // Create pagination
        $this->load->library('pagination');
        $config['base_url'] = '/admin/components/run/shop/brands/index/?' . http_build_query($this->input->get());
        $config['container'] = 'shopAdminPage';
        $config['page_query_string'] = true;
        $config['uri_segment'] = 8;
        $config['total_rows'] = $total;
        $config['per_page'] = $this->per_page;
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

        $this->render(
            'list',
            [
             'model'      => $model,
             'languages'  => $this->cms_admin->get_langs(true),
             'pagination' => $this->pagination->create_links_ajax(),
             'total'      => $total,
            ]
        );
    }

    /**
     * Create new brand
     *
     * @access public
     */
    public function create() {

        $locale = \MY_Controller::getCurrentLocale();

        Events::create()->registerEvent('', 'ShopAdminBrands:preCreate');
        Events::runFactory();

        $model = new SBrands;

        if ($this->input->post()) {

            $this->form_validation->set_rules($model->rules());

            if ($this->form_validation->run($this) == FALSE) {
                showMessage(validation_errors(), '', 'r');
            } else {
                /** Prepare url if it is empty * */
                $url = (string) trim($this->input->post('Url')) ?: translit_url($this->input->post('Name'));
                if (!$url) {
                    showMessage(lang('Failed generate URL', 'admin'), '', 'r');
                    return false;
                }

                /** Check if brand URL is aviable. * */
                $urlCheck = SBrandsQuery::create()
                    ->where('SBrands.Url = ?', $url)
                    ->findOne();

                if ($urlCheck !== null) {
                    exit(showMessage(lang('This URL is already in use', 'admin'), '', 'r'));
                }

                $model->fromArray($this->input->post());
                $model->setCreated(time());
                $model->setUpdated(time());
                $model->save();
                $model->setPosition($model->getId());
                $model->save();

                $this->load->library('image_lib');

                if (!empty($_FILES) && $this->_isAllowedExtension($_FILES['mainPhoto']['name']) !== true) {
                    showMessage(lang('Wrong image format'), '', 'r');
                }

                // Resize image.
                if (!empty($_FILES['mainPhoto']['tmp_name']) && $this->_isAllowedExtension($_FILES['mainPhoto']['name']) === true) {
                    $imageSizes = $this->getImageSize($_FILES['mainPhoto']['tmp_name']);
                    $imageName = $model->getUrl() . '.' . $this->current_extension;
                    if ($imageSizes['width'] >= $this->imageSizes['mainImageWidth'] OR $imageSizes['height'] >= $this->imageSizes['mainImageHeight']) {
                        $config['image_library'] = 'gd2';
                        $config['source_image'] = $_FILES['mainPhoto']['tmp_name'];
                        $config['create_thumb'] = FALSE;
                        $config['maintain_ratio'] = TRUE;
                        $config['width'] = $this->imageSizes['mainImageWidth'];
                        $config['height'] = $this->imageSizes['mainImageHeight'];
                        $config['master_dim'] = 'height';
                        $config['new_image'] = ShopCore::$imagesUploadPath . 'brands/' . $imageName;
                        $config['quality'] = $this->imageQuality;
                        $this->image_lib->initialize($config);

                        if ($this->image_lib->resize()) {
                            $mainImageResized = true;
                            $model->setImage($imageName);
                        }
                    } else {
                        move_uploaded_file($_FILES['mainPhoto']['tmp_name'], ShopCore::$imagesUploadPath . 'brands/' . $imageName);
                        $mainImageResized = true;
                        $model->setImage($imageName);
                    }

                    $model->save();
                }

                /** Init Event. Create Shop Brand */
                Events::create()->registerEvent(['model' => $model, 'userId' => $this->dx_auth->get_user_id()]);
                Events::runFactory();

                $this->lib_admin->log(lang('Brand created', 'admin') . '. Id: ' . $model->getId());
                showMessage(lang('Brand created', 'admin'));

                $action = $this->input->post('action');

                if ($action == 'exit') {
                    pjax('/admin/components/run/shop/brands/index');
                } else {
                    if ($action === 'fast_brand_create') {
                        pjax('/admin/components/run/shop/brands/index?fast_create=on');
                    } else {
                        pjax('/admin/components/run/shop/brands/edit/' . $model->getId() . '/' . $locale);
                    }
                }
            }
        } else {
            $this->render(
                'create',
                [
                 'model'  => $model,
                 'locale' => $locale,
                ]
            );
        }
    }

    /**
     * Check if file has allowed extension
     *
     * @param string $fileName
     * @access protected
     * @return bool
     */
    protected function _isAllowedExtension($fileName) {

        $parts = explode('.', $fileName);
        $ext = strtolower(end($parts));

        $this->current_extension = $ext;

        return in_array($ext, $this->allowedImageExtensions) ? true : false;
    }

    /**
     * Get image width and height.
     *
     * @param string $file_path Full path to image
     * @access protected
     * @return mixed
     */
    protected function getImageSize($file_path) {

        if (function_exists('getimagesize') && file_exists($file_path)) {
            $image = @getimagesize($file_path);

            $size = [
                     'width'  => $image[0],
                     'height' => $image[1],
                    ];

            return $size;
        }

        return false;
    }

    /**
     * @param int|null $brandId
     * @param null|string $locale
     */
    public function edit($brandId = null, $locale = null) {

        $locale = $locale == null ? \MY_Controller::getCurrentLocale() : $locale;

        $model = SBrandsQuery::create()->setComment(__METHOD__)->findPk((int) $brandId);
        if ($model === null) {
            $this->error404(lang('Brand not found', 'admin'), '', 'r');
        }

        $paginationBrand = $this->session->userdata('brand_url');
        $paginationBrand = $paginationBrand ?: null;

        /** Init Event. PreEdit Shop Brand */
        Events::create()->registerEvent(['model' => $model, 'userId' => $this->dx_auth->get_user_id(), 'url' => $model->getUrl()], 'ShopAdminBrands:preEdit');
        Events::runFactory();

        if ($this->input->post()) {

            $validation = $this->form_validation->set_rules($model->rules());
            $validation = $model->validateCustomData($validation);

            if ($validation->run($this) == FALSE) {
                showMessage(validation_errors(), '', 'r');
            } else {
                /** Prepare url if it is empty * */
                $url = (string) trim($this->input->post('Url')) ?: translit_url($this->input->post('Name'));
                if (!$url) {
                    showMessage(lang('Failed generate URL', 'admin'), '', 'r');
                    return false;
                }

                /** Check if brand URL is available. * */
                $urlCheck = SBrandsQuery::create()
                    ->where('SBrands.Url = ?', $url)
                    ->where('SBrands.Id != ?', (int) $model->getId())
                    ->findOne();

                if ($urlCheck !== null) {
                    exit(showMessage(lang('This URL is already in use', 'admin'), '', 'r'));
                }

                $postData = $this->input->post();
                $postData['Locale'] = $locale;

                $model->fromArray($postData);
                $model->setUpdated(time());
                if ($this->input->post('deleteImage') == 1) {
                    $this->deleteImage($model);
                    $model->setImage(' ');
                }
                $model->save();
                $this->load->library('image_lib');

                if (isset($_FILES['mainPhoto'])) {
                    if ($this->_isAllowedExtension($_FILES['mainPhoto']['name']) !== true) {
                        showMessage(lang('Wrong image format'), '', 'r');
                    }
                }

                // Resize image.
                if (!empty($_FILES['mainPhoto']['tmp_name']) && $this->_isAllowedExtension($_FILES['mainPhoto']['name']) === true) {
                    $imageSizes = $this->getImageSize($_FILES['mainPhoto']['tmp_name']);
                    $imageName = $model->getUrl() . '.' . $this->current_extension;
                    if ($imageSizes['width'] >= $this->imageSizes['mainImageWidth'] OR $imageSizes['height'] >= $this->imageSizes['mainImageHeight']) {
                        $config['image_library'] = 'gd2';
                        $config['source_image'] = $_FILES['mainPhoto']['tmp_name'];
                        $config['create_thumb'] = FALSE;
                        $config['maintain_ratio'] = TRUE;
                        $config['width'] = $this->imageSizes['mainImageWidth'];
                        $config['height'] = $this->imageSizes['mainImageHeight'];
                        $config['master_dim'] = 'height';
                        $config['new_image'] = ShopCore::$imagesUploadPath . 'brands/' . $imageName;
                        $config['quality'] = $this->imageQuality;
                        $this->image_lib->initialize($config);

                        if ($this->image_lib->resize()) {
                            $mainImageResized = true;
                            $model->setImage($imageName);
                        }
                    } else {
                        move_uploaded_file($_FILES['mainPhoto']['tmp_name'], ShopCore::$imagesUploadPath . 'brands/' . $imageName);
                        $mainImageResized = true;
                        $model->setImage($imageName);
                    }

                    $model->save();
                }

                if (!isset($_FILES['mainPhoto']) && $this->input->post('deleteImage')) {
                    $model->setImage(null)->save();
                }

                /** Init Event. Edit Shop Brand */
                Events::create()->registerEvent(['model' => $model, 'userId' => $this->dx_auth->get_user_id(), 'url' => $model->getUrl()]);
                Events::runFactory();

                $this->lib_admin->log(lang('Brand edited', 'admin') . '. Id: ' . $brandId);
                showMessage(lang('Changes have been saved', 'admin'));

                if ($this->input->post('action') == 'tomain') {
                    pjax('/admin/components/run/shop/brands/index' . $paginationBrand);
                }

                if ($this->input->post('action') == 'toedit') {
                    pjax('/admin/components/run/shop/brands/edit/' . $model->getId() . '/' . $locale);
                }

                if ($this->input->post('action') == 'tocreate') {
                    pjax('/admin/components/run/shop/brands/create');
                }
            }
        } else {
            $model->setLocale($locale);

            $brandName = $model->getName();
            if (empty($brandName)) {
                $brandName = \CI::$APP->db
                    ->select('name')
                    ->limit(1)
                    ->get_where('shop_brands_i18n', ['id' => $brandId, 'locale' => $locale])
                    ->row()->name;
            }

            $this->render(
                'edit',
                [
                 'addField'        => ShopCore::app()->CustomFieldsHelper->getCustomFields('brand', $model->getId())->asAdminHtml(),
                 'brandName'       => $brandName,
                 'model'           => $model,
                 'languages'       => $this->cms_admin->get_langs(true),
                 'locale'          => $locale,
                 'brandPagination' => $paginationBrand,

                ]
            );
        }
    }

    /**
     * @param null $model
     * @return bool
     */
    public function deleteImage($model = NULL) {

        if (!$model) {
            return FALSE;
        }

        if ($model instanceof SBrands) {
            $model = [$model];
        }

        foreach ($model as $brand) {
            $name = $brand->getImage();
            if ($name) {
                $image_path = $this->imagePath . $name;
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }
        }

        return TRUE;
    }

    /**
     *
     */
    public function delete() {

        $id = $this->input->post('ids');

        $model = SBrandsQuery::create()->findPks($id);
        if ($model != null) {
            $this->deleteImage($model);
            $model->delete();

            /** Init Event. Delete Shop Brand */
            Events::create()->registerEvent(['brandId' => $id, 'userId' => $this->dx_auth->get_user_id(), 'model' => $model]);
            Events::runFactory();

            $this->lib_admin->log(lang('Brand (s) has been successfully removed (s)', 'admin') . '. Ids: ' . implode(', ', $id));
            showMessage(lang('Brand (s) has been successfully removed (s)', 'admin'));
            pjax('/admin/components/run/shop/brands/index');
        }
    }

    /**
     *
     */
    public function c_list() {

        $model = SBrandsQuery::create()
            ->orderByPosition()
            ->useI18nQuery($this->defaultLanguage['identif'])
            ->endUse()
            ->find();

        $this->render(
            'list',
            [
             'model'     => $model,
             'languages' => $this->cms_admin->get_langs(true),
            ]
        );
    }

    /**
     * @param int $id
     */
    public function translate($id) {
        $model = SBrandsQuery::create()->setComment(__METHOD__)->findPk((int) $id);

        if ($model === null) {
            $this->error404(lang('Brand not found', 'admin'));
        }

        $languages = $this->cms_admin->get_langs(true);

        $translatableFieldNames = $model->getTranslatableFieldNames();

        /**
         *  Update brand translation
         */
        if ($this->input->post()) {
            //form validating
            $translatingRules = $model->translatingRules();
            foreach ($languages as $language) {
                foreach ($translatableFieldNames as $fieldName) {
                    $this->form_validation->set_rules($fieldName . '_' . $language['identif'], $model->getLabel($fieldName) . lang(' language ', 'admin') . $language['lang_name'], $translatingRules[$fieldName]);
                }
            }

            if ($this->form_validation->run() == FALSE) {
                showMessage(validation_errors());
            } else {
                foreach ($languages as $language) {
                    $model->setLocale($language['identif']);
                    foreach ($translatableFieldNames as $fieldName) {
                        $methodName = 'set' . $fieldName;
                        $model->$methodName($this->input->post($fieldName . '_' . $language['identif']));
                    }
                }

                $model->save();

                showMessage(lang('Changes have been saved', 'admin'));

                if ($this->input->post('action') == 'tomain') {
                    pjax('/admin/components/run/shop/brands/index');
                }

                if ($this->input->post('action') == 'toedit') {
                    pjax('/admin/components/run/shop/brands/edit/' . $model->getId());
                }

                if ($this->input->post('action') == 'tocreate') {
                    pjax('/admin/components/run/shop/brands/create');
                }
            }
        } else {

            $mceEditorFieldNames = ['Description'];
            $requiredFieldNames = ['Name'];

            $this->render(
                'translate',
                [
                 'model'                  => $model,
                 'languages'              => $languages,
                 'translatableFieldNames' => $translatableFieldNames,
                 'mceEditorFieldNames'    => $mceEditorFieldNames,
                 'requairedFieldNames'    => $requiredFieldNames,
                ]
            );
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
        $positions = array_reverse($positions);

        foreach ($positions as $key => $val) {
            $query = 'UPDATE `shop_brands` SET `position`=' . $key . ' WHERE `id`=' . (int) $val . '; ';
            $this->db->query($query);
        }
        showMessage(lang('Positions saved', 'admin'));
    }

}