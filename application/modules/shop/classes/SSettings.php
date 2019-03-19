<?php

use Currency\Currency;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Exception\PropelException;

/**
 * SSettings - Manager shop settings
 * @property integer $imagesQuality
 * @property integer $imagesMainSize
 * @property string $mainImageWidth
 * @property string $mainImageHeight
 * @property string $smallImageWidth
 * @property string $smallImageHeight
 * @property string $mainModImageWidth
 * @property string $mainModImageHeight
 * @property string $smallModImageWidth
 * @property string $smallModImageHeight
 * @property string $smallAddImageWidth
 * @property string $smallAddImageHeight
 * @property string $addImageWidth
 * @property string $addImageHeight
 * @property string $watermark_active
 * @property string $watermark_watermark_image
 * @property string $watermark_watermark_interest
 * @property string $watermark_watermark_font_path
 * @property string $watermark_wm_vrt_alignment
 * @property string $watermark_wm_hor_alignment
 * @property string $watermark_watermark_padding
 * @property string $watermark_watermark_type
 * @property string $watermark_watermark_image_opacity
 * @property string $watermark_watermark_text
 * @property string $watermark_watermark_font_size
 * @property string $watermark_watermark_color
 * @property string $forgotPasswordMessageText
 * @property string $pricePrecision
 * @property integer $adminProductsPerPage
 * @property integer $frontProductsPerPage
 * @property boolean $catalogMode
 * @property integer $searchName
 * @property string systemTemplatePath
 * @property string ordersCheckStocks
 * @property string userInfoRegister
 * @property string ordersMinimumPrice
 * @property string additionalImageWidth
 * @property string additionalImageHeight
 * @property string thumbImageWidth
 * @property string ordersRecountGoods
 * @property string thumbImageHeight
 * @property string arrayFrontProductsPerPage
 * @property string imageSizesBlock
 * @property string urlProductPrefix
 * @property string urlProductParent
 * @property string urlShopCategoryPrefix
 * @property string urlShopCategoryParent
 */
class SSettings
{

    /**
     * @var null|string
     */
    public static $curentLocale = null;

    /**
     * @var array
     */
    public $settings = [];

    /**
     * @var array|null
     */
    private $defaultLanguage = null;

    public function __construct() {

        $this->defaultLanguage = getDefaultLanguage();
        // Load and parse all settings
        $this->loadSettings();
        $CI = &get_instance();
        $CI->load->database();

        CI::$APP->load->library('lib_admin');
    }

    /**
     * Load settings and store it in settings array
     *
     * @return void
     */
    public function loadSettings() {

        $model = ShopSettingsQuery::create()
            ->setComment(__METHOD__);

        if (null !== self::$curentLocale) {
            $model
                ->filterByLocale(self::$curentLocale)
                ->_or()
                ->filterByLocale('');
        } else {
            $model
                ->filterByLocale($this->defaultLanguage['identif'])
                ->_or()
                ->filterByLocale('');
        }

        $model = $model->find();

        if (count($model) > 0) {
            foreach ($model as $row) {
                $this->settings[$row->getName()] = $row;
            }
        }
    }

    /**
     * @return null|string
     */
    public static function getCurentLocale() {

        return self::$curentLocale;
    }

    /**
     * @return string
     */
    public function getImageSizesBlock() {

        return $this->imageSizesBlock;
    }

    /**
     * Save settings from array
     *
     * @param array $data
     * @param bool $create
     * @return bool
     */
    public function fromArray(array $data, $create = true) {

        if (count($data) > 0) {
            if (array_key_exists('Locale', $data)) {
                $locale = $data['Locale'];
                unset($data['Locale']);
            } else {
                $locale = null;
            }

            foreach ($data as $key => $value) {
                try {
                    $this->set($key, $value, $create, $locale);
                } catch (Exception $e) {
                    CI::$APP->lib_admin->log($e->getMessage() . ' : ' . $e->getPrevious()->getMessage());
                    return false;
                }
            }
        } else {
            return false;
        }
        return true;
    }

    /**
     * Create new param and save it.
     *
     * @param string $name
     * @param string $value
     * @param bool $create
     * @param string $locale
     * @throws PropelException
     */
    public function set($name, $value, $create = true, $locale = 'ru') {

        if ($this->isTranslatable($name)) {
            $model = ShopSettingsQuery::create()
                ->filterByName($name)
                ->filterByLocale($locale)
                ->findOne();

            if ($model === null) {
                $model = new ShopSettings();
            }

            $model->setName($name)
                ->setValue($value)
                ->setLocale($locale)
                ->save();
        } elseif (!array_key_exists($name, $this->settings) && $create === true) {

            $model = new ShopSettings();

            $model->setName($name)
                ->setValue($value)
                ->setLocale('')
                ->save();

            $this->settings[$name] = $model;
        } else {
            // Update
            $this->settings[$name]->setValue($value);
            $this->settings[$name]->save();
        }

    }

    /**
     * @param string $fieldName
     * @return bool
     */
    public function isTranslatable($fieldName) {

        $translatableFields = $this->getTranslatableFields();

        if (in_array($fieldName, $translatableFields)) {
            return TRUE;
        }

        return FALSE;
    }

    /**
     * @return array
     */
    public function getTranslatableFields() {

        return [
            /* Внешний вид */

            /* Изображения */

            /* Заказы - Уведомление покупателя о совершении заказа */
                'ordersSenderName', //Имя отправителя
                'ordersMessageTheme', //Тема сообщения
                'ordersMessageText', //Текст сообщения

            /* Заказы - Уведомление покупателя о смене статуса заказа */
                'notifyOrderStatusSenderName', //Имя отправителя
                'notifyOrderStatusMessageTheme', //Тема сообщения
                'notifyOrderStatusMessageText', //Формат сообщения

            /* WishList'ы */
                'wishListsSenderName', //Имя отправителя
                'wishListsMessageTheme', //Тема сообщения
                'wishListsMessageText', //Текст сообщения

            /* Уведомление о появлении */
                'notificationsSenderName', //Имя отправителя
                'notificationsMessageTheme', //Тема сообщения
                'notificationsMessageText', //Текст сообщения

            /* Оповещение о новом Callback'е */
                'callbacksSenderName', //Имя отправителя
                'callbacksMessageTheme', //Тема сообщения
                'callbacksMessageText', //Текст сообщения

            /* Автоматическая регистрация пользователя после заказа */
                'userInfoSenderName', //Имя отправителя
                'userInfoMessageTheme', //Тема сообщения
                'userInfoMessageText', //Текст сообщения

            /* Блок Топ-продаж */

            /* Восстановления пароля */
                'forgotPasswordMessageText', //Текст сообщения

            /*  Настройки поиска  */
                'searchName',
            /* Уведомления */
            //'adminMessageIncoming',
            //'adminMessageCallback',
            //'adminMessageOrderPage'
                'adminMessages',
               ];
    }

    /**
     * @return string
     */
    public function getAddImageHeight() {

        return $this->addImageHeight;
    }

    /**
     * @return string
     */
    public function getAddImageWidth() {

        return $this->addImageWidth;
    }

    /**
     * @return string
     */
    public function getAdditionalImageHeight() {

        return $this->additionalImageHeight;
    }

    /**
     * @return string
     */
    public function getAdditionalImageWidth() {

        return $this->additionalImageWidth;
    }

    /**
     * @return int
     */
    public function getAdminProductsPerPage() {

        return $this->adminProductsPerPage;
    }

    /**
     * @return string
     */
    public function getArrayFrontProductsPerPage() {

        return $this->arrayFrontProductsPerPage;
    }

    /**
     * @return array|null
     */
    public function getDefaultLanguage() {

        return $this->defaultLanguage;
    }

    /**
     * @return string
     */
    public function getForgotPasswordMessageText() {

        return $this->forgotPasswordMessageText;
    }

    /**
     * @return int
     */
    public function getFrontProductsPerPage() {

        return $this->frontProductsPerPage;
    }

    /**
     * @return int
     */
    public function getImagesMainSize() {

        return $this->imagesMainSize;
    }

    /**
     * @return int
     */
    public function getImagesQuality() {

        return $this->imagesQuality;
    }

    public function getIsAdult() {

        return $this->__get('isAdult');
    }

    /**
     * Get param value
     *
     * @param string $name
     * @return string or null
     */
    public function __get($name) {

        if ($name == 'pricePrecision') {
            return Currency::create()->mainPricePrecision;
        }
        if (array_key_exists($name, $this->settings)) {
            return $this->settings[$name]->getValue();
        } else {
            return null;
        }
    }

    /**
     * @return string
     */
    public function getMainImageHeight() {

        return $this->mainImageHeight;
    }

    /**
     * @return string
     */
    public function getMainImageWidth() {

        return $this->mainImageWidth;
    }

    /**
     * @return string
     */
    public function getMainModImageHeight() {

        return $this->mainModImageHeight;
    }

    /**
     * @return string
     */
    public function getMainModImageWidth() {

        return $this->mainModImageWidth;
    }

    /**
     * @return string
     */
    public function getOrdersCheckStocks() {

        return $this->ordersCheckStocks;
    }

    /**
     * @return string
     */
    public function getOrdersMinimumPrice() {

        return $this->ordersMinimumPrice;
    }

    /**
     * @return string
     */
    public function getOrdersRecountGoods() {

        return $this->ordersRecountGoods;
    }

    /**
     * @return string
     */
    public function getPricePrecision() {

        return $this->pricePrecision;
    }

    /**
     * @return int
     */
    public function getSearchName() {

        return $this->searchName;
    }

    /**
     * @return boolean
     */
    public function useCatalogMode() {

        return (boolean) $this->catalogMode;
    }

    /**
     * @return array
     */
    public function getSettings() {

        return $this->settings;
    }

    /**
     * @return string
     */
    public function getSmallAddImageHeight() {

        return $this->smallAddImageHeight;
    }

    /**
     * @return string
     */
    public function getSmallAddImageWidth() {

        return $this->smallAddImageWidth;
    }

    /**
     * @return string
     */
    public function getSmallImageHeight() {

        return $this->smallImageHeight;
    }

    /**
     * @return string
     */
    public function getSmallImageWidth() {

        return $this->smallImageWidth;
    }

    /**
     * @return string
     */
    public function getSmallModImageHeight() {

        return $this->smallModImageHeight;
    }

    /**
     * @return string
     */
    public function getSmallModImageWidth() {

        return $this->smallModImageWidth;
    }

    /**
     * @param bool|FALSE|int $order_id
     * @return array
     */
    public function getSortingFront($order_id = FALSE) {

        if (isset($this->sortingFront) && !$order_id) {
            return $this->sortingFront;
        }

        $locale = MY_Controller::getCurrentLocale();

        $sorting = SSortingQuery::create()
            ->setComment(__METHOD__)
            ->select(['name_front', 'tooltip', 'get', 'id'])
            ->withColumn('shop_sorting_i18n.name_front', 'name_front')
            ->withColumn('shop_sorting_i18n.tooltip', 'tooltip')
            ->withColumn('shop_sorting.get')
            ->withColumn('shop_sorting.id', 'id')
            ->joinWithI18n($locale, Criteria::INNER_JOIN)
            ->orderByPos()
            ->filterByActive(1)
            ->find()
            ->toArray();

        if ($order_id) {
            foreach ($sorting as $key => $sort) {
                if ($sort['id'] == $order_id) {
                    array_unshift($sorting, $sort);
                    unset($sorting[$key]);
                    break;
                }
            }
        }

        if (ShopCore::$_GET['searchSetting']) {
            array_unshift(
                $sorting,
                [
                 'name_front' => lang('- Not selected -', 'admin'),
                 'tooltip'    => '',
                 'get'        => 'none',
                 'id'         => 0,
                ]
            );

        }
        $this->sortingFront = $sorting;
        return $sorting;

    }

    /**
     * @return string
     */
    public function getSystemTemplatePath() {

        return $this->systemTemplatePath;
    }

    /**
     * @return string
     */
    public function getThumbImageHeight() {

        return $this->thumbImageHeight;
    }

    /**
     * @return string
     */
    public function getThumbImageWidth() {

        return $this->thumbImageWidth;
    }

    /**
     * @return string
     */
    public function getUserInfoRegister() {

        return $this->userInfoRegister;
    }

    /**
     * @return string
     */
    public function getWatermarkActive() {

        return $this->watermark_active;
    }

    /**
     * @return string
     */
    public function getWatermarkWatermarkColor() {

        return $this->watermark_watermark_color;
    }

    /**
     * @return string
     */
    public function getWatermarkWatermarkFontPath() {

        return $this->watermark_watermark_font_path;
    }

    /**
     * @return string
     */
    public function getWatermarkWatermarkFontSize() {

        return $this->watermark_watermark_font_size;
    }

    /**
     * @return string
     */
    public function getWatermarkWatermarkImage() {

        return $this->watermark_watermark_image;
    }

    /**
     * @return string
     */
    public function getWatermarkWatermarkImageOpacity() {

        return $this->watermark_watermark_image_opacity;
    }

    /**
     * @return string
     */
    public function getWatermarkWatermarkInterest() {

        return $this->watermark_watermark_interest;
    }

    /**
     * @return string
     */
    public function getWatermarkWatermarkPadding() {

        return $this->watermark_watermark_padding;
    }

    /**
     * @return string
     */
    public function getWatermarkWatermarkText() {

        return $this->watermark_watermark_text;
    }

    /**
     * @return string
     */
    public function getWatermarkWatermarkType() {

        return $this->watermark_watermark_type;
    }

    /**
     * @return string
     */
    public function getWatermarkWmHorAlignment() {

        return $this->watermark_wm_hor_alignment;
    }

    /**
     * @return string
     */
    public function getWatermarkWmVrtAlignment() {

        return $this->watermark_wm_vrt_alignment;
    }

    /**
     * @return mixed|array
     */
    public function getimagesizes() {

        $result['mainimage']['width'] = $this->__get('mainImageWidth');
        $result['mainimage']['height'] = $this->__get('mainImageHeight');
        $result['smallimage']['width'] = $this->__get('smallImageWidth');
        $result['smallimage']['height'] = $this->__get('smallImageHeight');
        $result['mainmodimage']['width'] = $this->__get('mainModImageWidth');
        $result['mainmodimage']['height'] = $this->__get('mainModImageHeight');
        $result['smallmodimage']['width'] = $this->__get('smallModImageWidth');
        $result['smallmodimage']['height'] = $this->__get('smallModImageHeight');
        return $result;
    }

    /**
     * @return string
     */
    public function getUrlProductPrefix() {
        return $this->urlProductPrefix;
    }

    /**
     * @return string
     */
    public function getUrlProductParent() {
        return $this->urlProductParent;
    }

    /**
     * @return string
     */
    public function getUrlShopCategoryPrefix() {
        return $this->urlShopCategoryPrefix;
    }

    /**
     * @return string
     */
    public function getUrlShopCategoryParent() {
        return $this->urlShopCategoryParent;
    }

    /**
     * @return array
     */
    public function getss_settings() {

        $arr = $this->__get('ss');
        $arr = unserialize($arr);
        return $arr;
    }

    /**
     * @param string $a
     */
    public function isselected($a) {

        /* TODO: remove if the functionality is not present */
        $i = $this->getSelectedCats();
        foreach ($i as $j) {
            if ((int) $j == (int) $a) {
                echo 'selected';
            }
        }
    }

    /**
     * @return array|string
     */
    public function getSelectedCats() {

        $arr = $this->__get('selectedProductCats');
        $arr = unserialize($arr);
        return $arr;
    }

    /**
     * @param string $ordersCheckStocks
     */
    public function setOrdersCheckStocks($ordersCheckStocks) {

        $this->ordersCheckStocks = $ordersCheckStocks;
    }

}