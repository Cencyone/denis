<?php
use MediaManager\Image;

/**
 * ShopAdminSettings class
 *
 * Saves shop settings
 * @property Lib_admin lib_admin
 */
class ShopAdminSettings extends ShopAdminController
{

    private $defaultLanguage = null;

    public function __construct() {

        parent::__construct();

        \ShopController::checkVar();
        \ShopAdminController::checkVarAdmin();

        $this->defaultLanguage = getDefaultLanguage();
    }

    /**
     * Display settings table
     *
     * @param null|string $locale
     * @return string
     */
    public function index($locale = null) {

        $locale = $locale == null ? $this->defaultLanguage['identif'] : $locale;

        SSettings::$curentLocale = $locale;

        $orders = SOrderStatusesI18nQuery::create()->setComment(__METHOD__)->filterByLocale($locale)->find();

        $notif = $this->db->where('locale', $locale)->get('answer_notifications')->result_array();
        $notif_with_key = [];
        foreach ($notif as $n) {
            $notif_with_key[$n['name']] = $n['message'];
        }

        $this->render(
            'settings',
            [
             'templates'          => $this->_getTemplatesList(),
             'locale'             => $locale,
             'orders'             => $orders,
             'ctemplates'         => $this->_get_templates(),
             'changefreq_options' => [
                                      'always'  => 'always',
                                      'hourly'  => 'hourly',
                                      'daily'   => 'daily',
                                      'weekly'  => 'weekly',
                                      'monthly' => 'monthly',
                                      'yearly'  => 'yearly',
                                      'never'   => 'never',
                                     ],
             'search_setting'     => ShopCore::app()->SSettings->getSearchName(),
             'catalogMode'        => ShopCore::app()->SSettings->useCatalogMode(),
             'sorting'            => $this->getSorting(),
             'isAdult'            => ShopCore::app()->SSettings->getIsAdult(),
             'notif'              => $notif_with_key,
            ]
        );
    }

    /**
     * Update settings
     * @param null|string $locale
     * @return bool
     */
    public function update($locale = null) {

        if ($locale === null) {
            $locale = chose_language();
        }

        $XMLDataMap = [
                       'main_page_priority'    => $this->input->post('main_page_priority'),
                       'cats_priority'         => $this->input->post('cats_priority'),
                       'pages_priority'        => $this->input->post('pages_priority'),
                       'main_page_changefreq'  => $this->input->post('main_page_changefreq'),
                       'categories_changefreq' => $this->input->post('categories_changefreq'),
                       'pages_changefreq'      => $this->input->post('pages_changefreq'),
                      ];

        ShopCore::app()->SSettings->set('xmlSiteMap', serialize($XMLDataMap));

        $this->db->where('name', 'sitemap');
        $this->db->update('components', ['enabled' => '1', 'settings' => serialize($XMLDataMap)]);

        $data = [];
        $this->load->library('form_validation');

        //Products front validation rules
        $this->form_validation->set_rules('frontProductsPerPage', lang('Number of products on site', 'admin'), 'integer|required');
        $this->form_validation->set_rules('arrayFrontProductsPerPage', lang('Array of values for the number of products on site', 'admin'), 'required');

        /*----------- Validation of product per page -----------*/
        $FrontProductPerPage = explode(',', $this->input->post('arrayFrontProductsPerPage'));
        if (!in_array($this->input->post('frontProductsPerPage'), $FrontProductPerPage)) {
            showMessage(lang('First element array of values for the number of products on site must be mush number of products on site', 'admin'), '', 'r');
            return false;
        }

        if (trim($this->input->post('orders')['minimumPrice'])) {
            $this->form_validation->set_rules('orders[minimumPrice]', lang('Order minimum price', 'admin'), 'integer');
        }

        if ($this->input->post('url_product_prefix')) {
            $this->form_validation->set_rules('url_product_prefix', lang('Product url prefix', 'admin'), 'alpha_dash_slash');
        }

        if ($this->input->post('url_shop_category_prefix')) {
            $this->form_validation->set_rules('url_shop_category_prefix', lang('Category url prefix', 'admin'), 'alpha_dash_slash');
        }

        if ($this->form_validation->run() == false) {
            showMessage(validation_errors(), '', 'r');
            return false;
        } else {

            $postData = $this->input->post();
            $data['pricePrecision'] = 4;
            //          Отключено из-за ненадобности в новых валютах  $data['pricePrecision'] = $this->input-post('pricePrecision');
            $data['order_method'] = $this->input->post('order_method');

            // Forgot Password Message text
            $data['forgotPasswordMessageText'] = $postData['forgotPassword']['MessageText'];

            $postData['watermark']['active'] = (boolean) $postData['watermark']['active'];
            // Save watermark settings
            if ($postData['watermark']) {
                foreach ($postData['watermark'] as $key => $value) {
                    $data['watermark_' . $key] = $value;
                }
            }
            // Validate watermark image
            if ($data['watermark_active'] && 'overlay' == $data['watermark_watermark_type'] && '' != ($watermarkImagePath = $data['watermark_watermark_image'])) {
                if (file_exists('.' . $watermarkImagePath)) {
                    $data['watermark_watermark_image'] = $watermarkImagePath;
                } elseif (file_exists('./uploads/' . $watermarkImagePath)) {
                    $data['watermark_watermark_image'] = '/uploads/' . $watermarkImagePath;
                } else {
                    showMessage(lang('Specify the correct path to watermark image', 'gallery'), false, 'r');
                    exit;
                }
            }

            // saving font file, if specified
            if (isset($_FILES['watermark_font_path'])) {
                $uploadPath = './uploads/';
                // TODO: there are no mime-types for fonts in application/config/mimes.php
                $allowedTypes = [
                                 'ttf',
                                 'fnt',
                                 'fon',
                                 'otf',
                                ];
                $ext = pathinfo($_FILES['watermark_font_path']['name'], PATHINFO_EXTENSION);
                if (in_array($ext, $allowedTypes)) {

                    $this->load->library(
                        'upload',
                        [
                         'upload_path'   => $uploadPath,
                         'max_size'      => 1024 * 1024 * 2, //2 Mb
                            //'allowed_types' => 'ttf|fnt|fon|otf'
                         'allowed_types' => '*',
                        ]
                    );
                    if (!$this->upload->do_upload('watermark_font_path')) {
                        $this->upload->display_errors('', '');
                    } else {
                        $udata = $this->upload->data();
                        // changing value in the DB
                        $this->changeComponentsSettings('gallery', 'watermark_font_path', $uploadPath . $_FILES['watermark_font_path']['name']);
                        $data['watermark_watermark_font_path'] = $uploadPath . $udata['file_name'];
                    }
                }
            }

            if ($postData['watermark']['delete_watermark_font_path'] == 1) {
                unlink(ShopCore::app()->SSettings->getWatermarkWatermarkFontPath());
                $data['watermark_watermark_font_path'] = '';
            }

            //Start. Save image sizes Blocks
            if ($imageSizesBlock = serialize($this->input->post('imageSizesBlock'))) {
                ShopCore::app()->SSettings->set('imageSizesBlock', $imageSizesBlock);
            }
            //End. Save image sizes Blocks

            /** Delete not used folders */
            $postList = [];
            foreach ($this->input->post('imageSizesBlock') as $key => $value) {
                $postList[] .= $value['name'];
            }

            array_push($postList, 'additional', 'origin', 'watermarks');
            $list = $this->getFoldersList(ShopCore::$imagesUploadPath . 'products/');
            $foldersForDeleting = array_diff($list, $postList);

            foreach ($foldersForDeleting as $value) {
                $this->removeDirectory(ShopCore::$imagesUploadPath . 'products/' . $value);
            }
            /** End. Delete not used folders */
            $data['imagesQuality'] = $postData['images']['quality'];
            $data['imagesMainSize'] = $postData['images']['imagesMainSize'];

            $data['additionalImageWidth'] = $this->input->post('additionalImageWidth');
            $data['additionalImageHeight'] = $this->input->post('additionalImageHeight');

            $data['thumbImageWidth'] = $this->input->post('thumbImageWidth');
            $data['thumbImageHeight'] = $this->input->post('thumbImageHeight');

            $data['frontProductsPerPage'] = $this->input->post('frontProductsPerPage');
            if ($this->input->post('arrayFrontProductsPerPage') != null) {
                $values = explode(',', trim($this->input->post('arrayFrontProductsPerPage'), ','));
                $data['arrayFrontProductsPerPage'] = serialize($values);
            }
            $data['systemTemplatePath'] = $this->input->post('systemTemplatePath');
            $data['mobileTemplatePath'] = $this->input->post('mobileTemplatePath');

            // Orders
            $data['ordersMessageFormat'] = $postData['orders']['messageFormat'];
            $data['ordersMessageText'] = $postData['orders']['userMessageText'];
            $data['ordersSendMessage'] = $postData['orders']['sendUserEmail'];
            $data['ordersSenderEmail'] = $postData['orders']['senderEmail'];
            $data['ordersSenderName'] = $postData['orders']['senderName'];
            $data['ordersMessageTheme'] = $postData['orders']['theme'];
            $data['ordersManagerEmail'] = $postData['orders']['managerEmail'];
            $data['ordersSendManagerMessage'] = $postData['orders']['sendManagerEmail'];

            $data['ordersuserInfoRegister'] = $postData['orders']['userInfo[Register]'];

            $data['ordersRecountGoods'] = (bool) $postData['orders']['recountGoods'];
            $data['ordersCheckStocks'] = (bool) $postData['orders']['checkStocks'];
            $data['ordersMinimumPrice'] = $postData['orders']['minimumPrice'] ? (int) $postData['orders']['minimumPrice'] : '';

            // Order statuses changing
            $data['notifyOrderStatusStatusEmail'] = $postData['notifyOrderStatus']['statusEmail'];
            $data['notifyOrderStatusMessageFormat'] = $postData['notifyOrderStatus']['messageFormat'];
            $data['notifyOrderStatusMessageText'] = $postData['notifyOrderStatus']['userMessageText'];
            $data['notifyOrderStatusSenderEmail'] = $postData['notifyOrderStatus']['senderEmail'];
            $data['notifyOrderStatusSenderName'] = $postData['notifyOrderStatus']['senderName'];
            $data['notifyOrderStatusMessageTheme'] = $postData['notifyOrderStatus']['theme'];

            // Wish lists
            $data['wishListsMessageFormat'] = $postData['wishLists']['messageFormat'];
            $data['wishListsMessageText'] = $postData['wishLists']['userMessageText'];
            $data['wishListsSenderEmail'] = $postData['wishLists']['senderEmail'];
            $data['wishListsSenderName'] = $postData['wishLists']['senderName'];
            $data['wishListsMessageTheme'] = $postData['wishLists']['theme'];

            // Notifications
            $data['notificationsMessageFormat'] = $postData['notifications']['messageFormat'];
            $data['notificationsMessageText'] = $postData['notifications']['userMessageText'];
            $data['notificationsSenderEmail'] = $postData['notifications']['senderEmail'];
            $data['notificationsSenderName'] = $postData['notifications']['senderName'];
            $data['notificationsMessageTheme'] = $postData['notifications']['theme'];

            // Callbacks
            $data['callbacksSendNotification'] = ($postData['callbacks']['sendNotification'] == 1) ? 1 : 0;
            $data['callbacksMessageFormat'] = $postData['callbacks']['messageFormat'];
            $data['callbacksMessageText'] = $postData['callbacks']['userMessageText'];
            $data['callbacksSendEmailTo'] = $postData['callbacks']['sendEmailTo'];
            $data['callbacksSenderEmail'] = $postData['callbacks']['senderEmail'];
            $data['callbacksSenderName'] = $postData['callbacks']['senderName'];
            $data['callbacksMessageTheme'] = $postData['callbacks']['theme'];

            // UserInfo for new user after ordering
            $data['userInfoRegister'] = ($postData['userInfo']['Register'] == 1) ? 1 : 0;
            $data['userInfoMessageFormat'] = $postData['userInfo']['messageFormat'];
            $data['userInfoMessageText'] = $postData['userInfo']['userMessageText'];
            $data['userInfoSenderEmail'] = $postData['userInfo']['senderEmail'];
            $data['userInfoSenderName'] = $postData['userInfo']['senderName'];
            $data['userInfoMessageTheme'] = $postData['userInfo']['theme'];

            // UserInfo for new user after ordering
            $data['topSalesBlockFormulaCoef'] = $postData['topSalesBlock']['formulaCoef'];
            $data['Locale'] = $this->input->post('Locale');

            //Yandex market settings
            $data['selectedProductCats'] = serialize($this->input->post('displayedCats'));
            $data['isAdult'] = $postData['yandex']['isAdult'];

            //1C catalogue settings
            $postData['1CSettings']['filesize'] = 'file_limit=' . $postData['1CSettings']['filesize'];
            $data['1CCatSettings'] = serialize($this->input->post('1CSettings'));
            $data['1CSettingsOS'] = serialize($this->input->post('1CSettingsOS'));

            //Memcached settings
            $postData['MemCachedSettings']['MEMCACHE_ON'] = (bool) $postData['MemCachedSettings']['MEMCACHE_ON'];
            $data['MemcachedSettings'] = serialize($this->input->post('MemCachedSettings'));

            //Mobile version settings
            $postData['MobileVersionSettings']['MobileVersionON'] = (bool) $postData['MobileVersionSettings']['MobileVersionON'];
            $data['MobileVersionSettings'] = serialize($this->input->post('MobileVersionSettings'));

            //Set search settings
            $data['searchName'] = $this->input->post('searchName');

            //Set use Catalog mode
            $data['catalogMode'] = $this->input->post('catalogMode') ? 1 : 0;

            $data['urlProductPrefix'] = trim($this->input->post('url_product_prefix'), '/ ');
            $data['urlProductParent'] = $this->input->post('url_product_parent') ? 1 : 0;
            $data['urlShopCategoryPrefix'] = trim($this->input->post('url_shop_category_prefix'), '/ ');
            $data['urlShopCategoryParent'] = $this->input->post('url_shop_category_parent') ? 1 : 0;

            SSettings::$curentLocale = $data['Locale'];

            if (!ShopCore::app()->SSettings->fromArray($data)) {
                showMessage(lang('Error'), '', 'r');
                return false;
            }

            $adminMessageIncoming = $postData['messages']['incoming'];
            $adminMessageCallback = $postData['messages']['callback'];
            $adminMessageOrderPage = $postData['messages']['order'];

            if ($this->db->where('name', 'incoming')->where('locale', $locale)->get('answer_notifications')->num_rows()) {
                $this->db->where('name', 'incoming')->where('locale', $locale)->update('answer_notifications', ['message' => $adminMessageIncoming]);
            } else {
                $this->db->insert('answer_notifications', ['locale' => $locale, 'name' => 'incoming', 'message' => $adminMessageIncoming]);
            }

            if ($this->db->where('name', 'callback')->where('locale', $locale)->get('answer_notifications')->num_rows()) {
                $this->db->where('name', 'callback')->where('locale', $locale)->update('answer_notifications', ['message' => $adminMessageCallback]);
            } else {
                $this->db->insert('answer_notifications', ['locale' => $locale, 'name' => 'callback', 'message' => $adminMessageCallback]);
            }

            if ($this->db->where('name', 'order')->where('locale', $locale)->get('answer_notifications')->num_rows()) {
                $this->db->where('name', 'order')->where('locale', $locale)->update('answer_notifications', ['message' => $adminMessageOrderPage]);
            } else {
                $this->db->insert('answer_notifications', ['locale' => $locale, 'name' => 'order', 'message' => $adminMessageOrderPage]);
            }

            if ($postData['searchName']) {
                $this->db->update('settings', ['search_setting' => $postData['searchName']]);

            }

            $this->lib_admin->log(lang('Settings was updated', 'admin'));
            showMessage(lang('Changes have been saved', 'admin'));

            $this->cache->delete_all();

            $action = $this->input->post('action');
            if ($action == 'close') {
                pjax('/admin/components/run/shop/dashboard');
            }
        }
    }

    /**
     * Changes the array values in table `components`, field `settings`
     * @param string $moduleName - `name` field of table (module name)
     * @param string $key
     * @param mixed $newValue
     */
    protected function changeComponentsSettings($moduleName, $key, $newValue) {

        // getting settings from table
        $result = $this->db
            ->select('settings')
            ->from('components')
            ->where('name', $moduleName)
            ->get();
        $settingsData = $result->result_array();
        $settings = unserialize($settingsData[0]['settings']);
        // set new value
        $settings[$key] = $newValue;
        // save data into table
        $this->db->where('name', $moduleName)
            ->update(
                'components',
                [
                 'settings' => serialize($settings),
                ]
            );
    }

    /**
     * @return array|bool
     */
    protected function _getTemplatesList() {

        $paths = [];
        $this->load->helper('directory');

        $dirs = [
                 './application/' . getModContDirName('shop') . '/shop/templates/*',
                 './templates/*/shop/',
                ];

        foreach ($dirs as $dir) {
            $result = glob($dir, GLOB_ONLYDIR);
            if (is_array($result)) {

                // Remove mobile version template from select
                foreach ($result as $pathItemIndex => $pathItem) {
                    if (stristr($pathItem, '_mobile')) {
                        unset($result[$pathItemIndex]);
                    }
                }

                $paths = array_merge($paths, $result);
            }
        }

        if (count($paths) > 0) {
            return $paths;
        } else {
            return false;
        }
    }

    /**
     * @return array|bool
     */
    public function _get_templates() {

        $new_arr = [];
        if ($handle = opendir(TEMPLATES_PATH)) {
            while (false !== ($file = readdir($handle))) {
                if ($file != '.' && $file != '..' && $file != 'administrator' && $file != 'modules') {
                    if (!is_file(TEMPLATES_PATH . $file)) {
                        $new_arr[$file] = $file;
                    }
                }
            }
            closedir($handle);
        } else {
            return FALSE;
        }
        return $new_arr;
    }

    public function runResizeAll() {

        Image::create()->resizeAll();
        showMessage(lang('Pictures Updated', 'admin'));
    }

    public function runResizeAllJsone() {

        $array = json_decode($this->input->post('array'));
        Image::create()->resizeById($array);
        //showMessage("Картинки обновлены");
    }

    public function runResizeAllAdditionalJsone() {

        $array = json_decode($this->input->post('array'));
        Image::create()->resizeByIdAdditional($array);
        //showMessage("Картинки обновлены");
    }

    public function runResizeById($id) {

        Image::create()
            ->resizeById($id)
            ->resizeByIdAdditional($id, TRUE);
        showMessage(lang('Pictures Updated', 'admin'));
    }

    public function changeSortActive() {

        $status = $this->input->post('status') == 'true' ? 0 : 1;
        $sorting = SSortingQuery::create()->setComment(__METHOD__)->findOneById($this->input->post('sortId'));
        $sorting->setActive($status);
        $sorting->save();

        showMessage(lang('Status saved', 'admin'));

        $this->lib_admin->log(lang('Change sorting status', 'admin') . ' ' . $sorting->getId());
    }

    public function saveSortPositions() {

        $positions = $this->input->post('positions');
        if (count($positions) == 0) {
            return false;
        }
        foreach ($positions as $key => $val) {
            $query = 'UPDATE `shop_sorting` SET `pos`=' . $key . ' WHERE `id`=' . (int) $val . '; ';
            $this->db->query($query);
        }
        showMessage(lang('Positions saved', 'admin'));
    }

    public function ajaxUpdateFieldName() {

        $data = [$this->input->post('name') => $this->input->post('text')];
        try {
            $this->db->where('id', $this->input->post('id'))->update('shop_sorting', $data);
        } catch (Exception $e) {
            $this->lib_admin->log($e->getMessage());
        }
    }

    /**
     * Check is gd lib installed
     * @return boolean
     */
    public function checkGDLib() {

        $res['status'] = true;
        if (!extension_loaded('gd') && !function_exists('gd_info')) {
            //            showMessage(lang('Error', 'admin'), lang('PHP GD library is not installed on your web server', 'admin'), 'r');
            $res['status'] = false;
        }
        echo json_encode($res);
    }

    public function getAllProductsVariantsIds() {

        $ids = null;
        $array = $this->db
            ->select('id,mainImage')
            ->group_by('mainImage')
            ->get('shop_product_variants')
            ->result_array();

        foreach ($array as $value) {
            if ($value['mainImage']) {
                $ids[] .= $value['id'];
            }
        }
        echo json_encode($ids);
    }

    public function getAllProductsIds() {

        $ids = null;
        $array = $this->db->distinct()->select('product_id')->get('shop_product_images')->result_array();
        foreach ($array as $value) {
            $ids[] .= $value['product_id'];
        }
        if ($ids != null) {
            echo json_encode($ids);
        } else {
            echo 'false';
        }
    }

    public function getFoldersList($path) {

        $list = scandir($path);
        unset($list[0], $list[1]);
        return $list;
    }

    public function removeDirectory($dir) {

        if ($objs = glob($dir . '/*')) {
            foreach ($objs as $obj) {
                is_dir($obj) ? $this->removeDirectory($obj) : unlink($obj);
            }
        }
        rmdir($dir);
    }

    public function getSorting() {

        $locale = MY_Controller::getCurrentLocale();
        return $this->db->order_by('shop_sorting.pos')
            ->select('*, shop_sorting.id as id')
            ->join('shop_sorting_i18n', "shop_sorting_i18n.id=shop_sorting.id and shop_sorting_i18n.locale = '$locale'", 'left')->get('shop_sorting')->result_array();
    }

    public function setSorting() {

        $name = $this->input->post('name');
        $name_front = $this->input->post('name_front');

        $tooltip = $this->input->post('tooltip');
        $locale = $this->input->post('locale');
        $id = $this->input->post('id');

        if ($this->db->where('locale', $locale)->where('id', $id)->get('shop_sorting_i18n')->num_rows()) {
            $this->db->where('locale', $locale)->where('id', $id)
                ->update('shop_sorting_i18n', ['name' => $name, 'name_front' => $name_front, 'tooltip' => $tooltip]);
        } else {
            $this->db
                ->insert('shop_sorting_i18n', ['locale' => $locale, 'id' => $id, 'name' => $name, 'name_front' => $name_front, 'tooltip' => $tooltip]);
        }
    }

}