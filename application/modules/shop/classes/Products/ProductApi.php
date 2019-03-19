<?php

namespace Products;

use Currency\Currency;
use CustomFieldsDataQuery;
use Exception;
use FilesystemIterator;
use MediaManager\Image;
use MY_Controller;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Exception\PropelException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SCategoryQuery;
use SCurrenciesQuery;
use ShopController;
use ShopKitQuery;
use SNotificationsQuery;
use SOrdersQuery;
use SProductPropertiesDataQuery;
use SProducts;
use SProductsI18n;
use SProductsI18nQuery;
use SProductsQuery;
use SProductVariants;
use SProductVariantsI18n;
use SProductVariantsI18nQuery;
use SProductVariantsQuery;
use SPropertiesQuery;

(defined('BASEPATH')) OR exit('No direct script access allowed');

/**
 * Shop Controller
 *
 * @uses \ShopController
 * @package Shop
 * @copyright 2014 ImageCMS
 * @author Dev ImageCMS <dev@imagecms.net>
 * @access public
 * @link URL
 * @version 1.0
 */
class ProductApi extends ShopController
{

    const IMAGES_UPLOADS_PATH = './uploads/shop/products/';

    /**
     *
     * @var ProductApi
     */
    protected static $_instance;

    /**
     * Error message.
     * @var string
     */
    protected $error = '';

    private function __clone() {

    }

    /**
     * Create product and variant.
     *
     * @param array $data array of product data for insert
     * <br> string $data['url'] (optional) product url
     * <br> int $data['active'] (optional) is product will be show in store - 1 or 0
     * <br> int $data['brand_id'] (optional) brand id
     * <br> int $data['category_id'] (required) category id
     * <br> array $data['additional_categories_ids'] (optional) product additional categories
     * <br> string $data['related_products'] (optional) related products for current product
     * <br> int $data['created'] (optional) unix timestamp
     * <br> int $data['updated'] (optional) unix timestamp
     * <br> float $data['old_price'] (optional) old price of product
     * <br> int $data['views'] (optional) count of views
     * <br> int $data['hot'] (optional) is product type is "hot" - 1 or 0
     * <br> int $data['action'] (optional) is product type is "action" - 1 or 0
     * <br> int $data['added_to_cart_count'] (optional) count of adding to cart
     * <br> int $data['enable_comments'] (optional) allow leave comments for product - 1 or 0
     * <br> string $data['external_id'] (optional) product external id
     * <br> string $data['tpl'] (optional) set non-standard template file for product
     * <br> int $data['user_id'] (optional) user id who create product
     * <br> string $data['product_name'] (required) product name
     * <br> string $data['short_description'] (optional) short description
     * <br> string $data['full_description'] (optional) full description
     * <br> string $data['meta_title'] (optional) meta title
     * <br> string $data['meta_description'] (optional) meta description
     * <br> string $data['meta_keywords'] (optional) meta keywords
     * <br> string $data['number'] (optional) product SKU
     * <br> int $data['stock'] (optional) count of products in warehouse
     * <br> int $data['position'] (optional) variant position
     * <br> string $data['mainImage'] (optional) product image
     * <br> string $data['var_external_id'] (optional) variant external id
     * <br> int $data['currency'] (required) currency id
     * <br> float $data['price_in_main'] (required) price in main currency
     * <br> string $data['variant_name'] (optional) product variant name
     * <br> string $data['enable_comments'] (optional) enable comments
     * @param string $locale locale
     * @return SProducts|false
     */
    public function addProduct($data = [], $locale = 'ru') {

        try {
            $this->error = '';

            if ($data === NULL) {
                throw new Exception(lang('You did not specified data array'));
            }

            if (!is_array($data)) {
                throw new Exception(lang('Second parameter $data must be array'));
            }

            $data = $this->_validateProductData($data);

            $model = new SProducts;
            $model = $this->_setProductData($model, $data, 'create');

            $this->addProductI18N($model->getId(), $data, $locale);
            $this->addVariant($model->getId(), $data, $locale);
            $this->setProductAdditionalCategories($model, $data);
            Currency::create()->checkPrices();

            return $model;
        } catch (Exception $exc) {
            $this->setError($exc->getMessage());
            return FALSE;
        }
    }

    /**
     * Validations of product data for insert or update
     *
     * @param array $data
     * @param string $type
     * @return array
     * @throws Exception
     */
    private function _validateProductData(array $data, $type = 'create') {

        if ($data['url']) {
            if (strpos($data['url'], '_') === 0) {
                throw new Exception(lang('URL field can not contain first symbol: _'));
            }

            if (!preg_match("/^[\w\d-._~:\[\]@!$&'()*+;=]*$/", $data['url'])) {
                throw new Exception(lang('URL field can only contain alphanumeric characters and symbols: - , _'));
            }

            preg_match('/[а-яА-Я \/#?]/i', $data['url'], $url);
            if (!empty($url)) {
                throw new Exception(lang('URL field can only contain alphanumeric characters and symbols: - , _'));
            }

            // Check if Url is aviable.
            $this->db->where('url', substr($data['url'], 0, 255));

            if ($type == 'update') {
                $this->db->where('entity_id !=', $data['product_id']);
            }
            $urlCheck = $this->db->get('route');

            if ($urlCheck->num_rows() > 0) {
                throw new Exception(lang('This URL is already in use!'));
            }
        } else {
            $this->load->helper('translit');

            $this->db->where('url', substr(translit_url($data['product_name']), 0, 255));

            if ($type == 'update') {
                $this->db->where('entity_id !=', $data['product_id']);
            }

            $urlCheck = $this->db->get('route');

            if ($urlCheck->num_rows() > 0) {
                throw new Exception(lang('This URL is already in use!'));
            }

            $data['url'] = translit_url($data['product_name']);
        }

        if ($data['active']) {
            if (!in_array($data['active'], [1, 0])) {
                throw new Exception(lang('active not 1 or 0'));
            }
        } else {
            $data['active'] = 0;
        }

        if ((int) $data['enable_comments']) {
            if (!in_array((int) $data['enable_comments'], [1, 0])) {
                throw new Exception(lang('hit not 1 or 0'));
            }
        } else {
            $data['enable_comments'] = 0;
        }

        if ($data['stock']) {
            if (!is_int((int) $data['stock'])) {
                throw new Exception(lang('Invalid stock'));
            }
        } else {
            $data['stock'] = 0;
        }

        if ($data['brand_id']) {
            if (!filter_var($data['brand_id'], FILTER_VALIDATE_INT)) {
                throw new Exception(lang('Invalid brand_id'));
            }
        }
        if (!$data['product_name']) {
            throw new Exception(lang('not specified product_name'));
        }

        if (!$data['currency']) {
            throw new Exception(lang('currency not specified'));
        }

        if (count(SCurrenciesQuery::create()->setComment(__METHOD__)->findById($data['currency'])) == 0) {
            throw new Exception(lang('currency not exist'));
        }

        if (!isset($data['price_in_main'])) {
            throw new Exception(lang('price_in_main not specified'));
        }

        if (!is_numeric(str_replace(',', '.', $data['price_in_main']))) {
            throw new Exception(lang('The field Price must be numeric'));
        }

        if ($data['category_id']) {
            if (!filter_var($data['category_id'], FILTER_VALIDATE_INT)) {
                throw new Exception(lang('Invalid category_id'));
            }
        } else {
            throw new Exception(lang('category_id not specified'));
        }

        if ($data['additional_categories_ids']) {
            if (!is_array($data['additional_categories_ids'])) {
                $data['additional_categories_ids'] = [];
            }
        } else {
            $data['additional_categories_ids'] = [];
        }

        if (!$data['created']) {
            $data['created'] = time();
        }

        if (!$data['updated']) {
            $data['updated'] = time();
        }
        if ((float) $data['old_price'] && is_numeric($data['old_price'])) {
            if (!filter_var($data['old_price'], FILTER_VALIDATE_FLOAT)) {
                throw new Exception(lang('Invalid old_price'));
            }
        } else {
            $data['old_price'] = 0;
        }

        if ($data['views']) {
            if (!filter_var($data['views'], FILTER_VALIDATE_INT)) {
                throw new Exception(lang('Invalid views'));
            }
        }

        if ($data['added_to_cart_count']) {
            if (!filter_var($data['added_to_cart_count'], FILTER_VALIDATE_INT)) {
                throw new Exception(lang('Invalid added_to_cart_count'));
            }
        } else {
            $data['added_to_cart_count'] = 0;
        }

        if ($data['hot']) {
            if (!in_array($data['hot'], [1, 0])) {
                throw new Exception(lang('hot not 1 or 0'));
            }
        }

        if ($data['hit']) {
            if (!in_array($data['hit'], [1, 0])) {
                throw new Exception(lang('hit not 1 or 0'));
            }
        }

        if ($data['action']) {
            if (!in_array($data['action'], [1, 0])) {
                throw new Exception(lang('action not 1 or 0'));
            }
        }

        if ($data['tpl']) {
            if (!preg_match('/^([-a-z\d_\-\.])+$/i', $data['tpl'])) {
                throw new Exception(lang('The Main tpl field can only contain Latin alpha-numeric characters'));
            }

            if (mb_strlen($data['tpl']) > 250) {
                throw new Exception(lang('The main template field can not contain more than 250 characters'));
            }
        }

        return $data;
    }

    /**
     * Set product data
     * @param SProducts $model
     * @param array $data
     * <br> string $data['url'] (optional) product url
     * <br> int $data['active'] (optional) is product will be show in store - 1 or 0
     * <br> int $data['archive'] (optional) is product archived - 1 or 0
     * <br> int $data['brand_id'] (optional) brand id
     * <br> int $data['category_id'] (required) category id
     * <br> string $data['related_products'] (optional) related products for current product
     * <br> int $data['created'] (optional) unix timestamp
     * <br> int $data['updated'] (optional) unix timestamp
     * <br> float $data['old_price'] (optional) old price of product
     * <br> int $data['views'] (optional) count of views
     * <br> int $data['hot'] (optional) is product type is "hot" - 1 or 0
     * <br> int $data['action'] (optional) is product type is "action" - 1 or 0
     * <br> int $data['added_to_cart_count'] (optional) count of adding to cart
     * <br> int $data['enable_comments'] (optional) allow leave comments for product - 1 or 0
     * <br> string $data['external_id'] (optional) product external id
     * <br> string $data['tpl'] (optional) set non-standard template file for product
     * <br> int $data['user_id'] (optional) user id who create product
     * @param string $type 'update' or 'create'
     * @return SProducts
     * @throws PropelException
     */
    private function _setProductData($model, array $data, $type = 'update') {

        /* @var $model SProducts */
        $model->setUrl($data['url']);
        $model->setActive($data['active']);
        $model->setArchive($data['archive'] ?: $model->getArchive());
        $model->setBrandId($data['brand_id']);
        $model->setCategoryId($data['category_id']);
        $model->setRelatedProducts($data['related_products']);
        if ($type == 'create') {
            $model->setCreated($data['created'] ?: time());
        } else {
            $model->setCreated($data['created'] ?: $model->getCreated());
        }
        $model->setUpdated($data['updated'] ?: time());
        $model->setOldPrice($data['old_price']);
        $model->setViews($data['views'] ?: $model->getViews());

        if ($data['hot'] !== NULL) {
            $model->setHot($data['hot']);
        }

        if ($data['action'] !== NULL) {
            $model->setAction($data['action']);
        }

        if ($data['hit'] !== NULL) {
            $model->setHit($data['hit']);
        }

        $model->setAddedToCartCount($data['added_to_cart_count']);
        $model->setEnableComments($data['enable_comments']);

        if ($data['external_id']) {
            $model->setExternalId($data['external_id']);
        }

        $model->setTpl($data['tpl']);
        $model->setUserId($data['user_id']);

        $model->save();

        return $model;
    }

    /**
     * Create translation for existing product.
     *
     * @param integer $productId
     * @param array $data
     * <br> string $data['product_name'] (required) product name
     * <br> string $data['short_description'] (optional) short description
     * <br> string $data['full_description'] (optional) full description
     * <br> string $data['meta_title'] (optional) meta title
     * <br> string $data['meta_description'] (optional) meta description
     * <br> string $data['meta_keywords'] (optional) meta keywords
     * @param string $locale
     * @return FALSE|SProductsI18n
     */
    public function addProductI18N($productId, $data = [], $locale = 'ru') {

        try {
            $model = new SProductsI18n();
            $this->_setProductI18NData($productId, $model, $data, $locale);
            return $model;
        } catch (Exception $exc) {
            $this->setError($exc->getMessage());
            return FALSE;
        }
    }

    /**
     * Set productI18N data
     * @param integer $productId
     * @param SProductsI18n $model
     * @param array $data
     * <br> string $data['product_name'] (required) product name
     * <br> string $data['short_description'] (optional) short description
     * <br> string $data['full_description'] (optional) full description
     * <br> string $data['meta_title'] (optional) meta title
     * <br> string $data['meta_description'] (optional) meta description
     * <br> string $data['meta_keywords'] (optional) meta keywords
     * @param string $locale
     * @return SProductsI18n
     * @throws PropelException
     */
    private function _setProductI18NData($productId, $model, array $data, $locale = 'ru') {

        $model->setId($productId);
        $model->setMetaKeywords($data['meta_keywords']);
        $model->setMetaDescription($data['meta_description']);
        $model->setMetaTitle($data['meta_title']);
        $model->setFullDescription($data['full_description']);
        $model->setShortDescription($data['short_description']);
        $model->setName($data['product_name']);
        $model->setLocale($locale);
        $model->save();

        return $model;
    }

    /**
     * Set error message.
     *
     * @param string $msg
     */
    private function setError($msg) {

        $this->error = $msg;
    }

    /**
     * Create variant for product.
     *
     * @param integer $productId
     * @param array $data
     * <br> string $data['number'] (optional) product SKU
     * <br> int $data['stock'] (optional) count of products in warehouse
     * <br> int $data['position'] (optional) variant position
     * <br> string $data['mainImage'] (optional) product image
     * <br> string $data['var_external_id'] (optional) variant external id
     * <br> int $data['currency'] (required) currency id
     * <br> float $data['price_in_main'] (optional) price in main currency
     * <br> string $data['variant_name'] (optional) product variant name
     * @param string $locale
     * @return bool|SProductVariants
     */
    public function addVariant($productId, $data = [], $locale = 'ru') {

        try {
            $model = new SProductVariants();
            $model = $this->_setVariantData($productId, $model, $data);

            $this->addVariantI18N($model->getId(), $data, $locale);

            foreach (MY_Controller::getAllLocales() as $oneLocale) {
                if ($locale !== $oneLocale) {
                    $this->addVariantI18N($model->getId(), [], $oneLocale);
                }
            }

            Currency::create()->checkPrices();
            return $model;
        } catch (Exception $exc) {
            $this->setError($exc->getMessage());
            return FALSE;
        }
    }

    /**
     * Set product variant data
     * @param integer $productId
     * @param SProductVariants $model
     * @param array $data
     * @return SProductVariants
     * @throws PropelException
     */
    private function _setVariantData($productId, $model, array $data) {

        /* @var $model SProductVariants */
        $model->setProductId($productId);
        $model->setNumber($data['number']);
        $model->setStock($data['stock']);
        $model->setPosition($data['position']);

        if ($data['mainImage'] !== NULL) {
            $model->setMainimage($data['mainImage']);
        }

        if ($data['var_external_id']) {
            $model->setExternalId($data['var_external_id']);
        }
        $model->setCurrency($data['currency']);

        $model->setPriceInMain($this->formPrice($data['price_in_main']));

        try {
            $model->save();
        } catch (Exception $exc) {
            $this->setError($exc->getMessage());
            return FALSE;
        }

        if (!$data['number']) {
            $this->setArticle($model);
        }

        Currency::create()->checkPrices();

        return $model;
    }

    /**
     * Form valid price value
     * @param $price
     * @return string
     */
    private function formPrice($price) {

        if (substr($price, -1) == '.') {
            $price = substr($price, 0, -1);
        }

        $price = str_replace(',', '.', $price);
        return $price;
    }

    /**
     * @param SProductVariants $model
     * @throws PropelException
     */
    protected function setArticle(SProductVariants $model) {

        $product = $model->getSProducts();
        if ($product && $category = $product->getMainCategory()) {
            $article = substr($category->getUrl(), 0, 2) . '-' . $model->getId();
            $model->setNumber($article);
            $model->save();

        }

    }

    /**
     * Add product variant translation by variant ID
     * @param integer $variantId
     * @param array $data
     * <br> string $data['variant_name'] (optional) product variant name
     * @param string $locale
     * @return bool|SProductVariantsI18n
     */
    public function addVariantI18N($variantId, $data = [], $locale = 'ru') {

        try {
            $model = new SProductVariantsI18n();
            $model->setId($variantId);
            $model->setLocale($locale);
            $model->setName($data['variant_name']);
            $model->save();
            return $model;
        } catch (Exception $exc) {
            $this->setError($exc->getMessage());
            return FALSE;
        }
    }

    /**
     * Set product additional categories
     * @param SProducts $model - product model
     * <br> array $data['additional_categories_ids'] - product additional categories ids
     * @param array $data
     * @return bool
     */
    public function setProductAdditionalCategories($model, $data) {

        if ($data['additional_categories_ids'] !== NULL) {
            if ($model && $model instanceof SProducts) {
                if (is_array($data['additional_categories_ids'])) {

                    $this->db->where('product_id', $model->getId())->delete('shop_product_categories');

                    $insert_data = [];
                    foreach ($data['additional_categories_ids'] as $category_id) {
                        if ($category_id != $model->getCategoryId()) {
                            $insert_data[] = [
                                              'product_id'  => $model->getId(),
                                              'category_id' => $category_id,
                                             ];
                        }
                    }

                    $insert_data[] = [
                                      'product_id'  => $model->getId(),
                                      'category_id' => $model->getCategoryId(),
                                     ];

                    if (count($insert_data) > 0) {
                        $this->db->insert_batch('shop_product_categories', $insert_data);
                    }
                } else {
                    $this->setError(lang('Additional categories ids must be array'));
                    return FALSE;
                }
                return TRUE;
            } else {
                $this->setError(lang('You did not specified product model'));
                return FALSE;
            }
        } else {
            $this->setError(lang('You did not specified categories array'));
            return FALSE;
        }
    }

    /**
     * Copy product images from all uploads product folders
     * @param string $imageName - image file name to copy
     * @return false|string
     */
    public function copyProductImage($imageName) {
        if (!$imageName) {
            return FALSE;
        }

        if (!is_dir(self::IMAGES_UPLOADS_PATH)) {
            return FALSE;
        }

        $imageExtension = array_pop(explode('.', $imageName));
        $newImageName = md5($imageName . time()) . ".$imageExtension";
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator(self::IMAGES_UPLOADS_PATH, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST) as $file) {
            if ($file->isFile() && $file->getFilename() === $imageName) {
                chmod($file->getPathname(), 0777);
                $newImagePath = dirname($file->getPathname()) . "/$newImageName";
                copy($file->getPathname(), $newImagePath);
                chmod($newImagePath, 0777);
            }
        }

        return $newImageName;
    }

    /**
     * Remove unused product properties values
     * @param integer $productId
     * @param integer $oldCategoryId
     * @param integer $newCategoryId
     * @return bool
     * @throws PropelException
     */
    public function deleteOldProductPropertiesData($productId, $oldCategoryId, $newCategoryId) {

        if ($oldCategoryId == $newCategoryId) {
            return FALSE;
        }

        $category_properties = $this->db
            ->select('property_id,category_id')
            ->where('category_id', $oldCategoryId)
            ->where('category_id', $newCategoryId)
            ->get('shop_product_properties_categories');

        if ($category_properties) {
            $category_properties = $category_properties->result_array();

            $newCategoryProperties = $this->db
                ->select('property_id')
                ->where('category_id', $newCategoryId)
                ->get('shop_product_properties_categories');

            if ($newCategoryProperties) {
                $newCategoryProperties = $newCategoryProperties->result_array();
                /**
                 * @todo rewrite to array_column
                 */
                $newCategoryPropertiesArray = [];
                foreach ($newCategoryProperties as $newCatProp) {
                    $newCategoryPropertiesArray[] = $newCatProp['property_id'];
                }

                $delete_category_properties = [];
                foreach ($category_properties as $property) {
                    if (!(in_array($property['property_id'], $newCategoryPropertiesArray))) {
                        $delete_category_properties[] = $property['property_id'];
                    }
                }

                SProductPropertiesDataQuery::create()
                    ->filterByProductId($productId)
                    ->filterByPropertyId($delete_category_properties)
                    ->delete();
            }
        }
        return true;
    }

    /**
     * Delete product by ID
     * @param integer $id
     * @return bool
     */
    public function deleteProduct($id) {

        try {

            if (!$id) {
                throw new Exception(lang('You did not specified product id'));
            }

            /** Delete images */
            $this->deleteProductImages($id);
            /** Delete product kits */
            $this->deleteProductKits($id);

            /** Notifications delete */
            $this->deleteProductNotifications($id);

            /** Delete product */
            $model = SProductsQuery::create()->setComment(__METHOD__)->findOneById($id);
            if ($model) {
                $model->delete();
            }
            /** End Delete product */
            /** Delete product from users carts */
            $this->deleteProductFromCart($id);

            /* Delete product custom fields data */
            $this->deleteProductCustomFieldsData($id);

            return TRUE;
        } catch (Exception $exc) {
            $this->setError($exc->getMessage());
            return FALSE;
        }
    }

    /**
     * Delete product images by product ID
     * @param integer $product_id
     * @return boolean
     */
    private function deleteProductImages($product_id) {

        if (!$product_id) {
            $this->setError(lang('You did not specified product id'));
            return FALSE;
        }

        Image::create()->deleteImagebyProductId($product_id)->deleteAdditionalImagebyProductId([$product_id]);

        return TRUE;
    }

    /**
     * Delete product kits by product ID
     * @param integer $product_id
     * @return boolean
     */
    public function deleteProductKits($product_id) {

        if (!$product_id) {
            $this->setError(lang('You did not specified product id'));
            return FALSE;
        }

        $modelKit = ShopKitQuery::create()->setComment(__METHOD__)->findByProductId($product_id);

        if ($modelKit) {
            $modelKit->delete();
        }

        return TRUE;
    }

    /**
     * Delete product notifications by product ID
     * @param integer $product_id
     * @return boolean
     */
    public function deleteProductNotifications($product_id) {

        if (!$product_id) {
            $this->setError(lang('You did not specified product id'));
            return FALSE;
        }

        $notifModel = SNotificationsQuery::create()->setComment(__METHOD__)->findByProductId($product_id);

        if ($notifModel) {
            $notifModel->delete();
        }
        return TRUE;
    }

    /**
     * Delete product from users carts by product ID
     * @param integer $product_id - product ID
     * @return boolean
     */
    public function deleteProductFromCart($product_id) {

        if (!$product_id) {
            $this->setError(lang('You did not specified product id'));
            return FALSE;
        }

        /** Get users from DB */
        $users = $this->db->get('users');
        $users = $users ? $users->result_array() : [];

        /** User data to update */
        $usersUpdateData = [];

        /** Prepare user data to update */
        foreach ($users as $user) {
            $cart_data = $user['cart_data'] ? unserialize($user['cart_data']) : [];

            foreach ($cart_data as $key => $cart_item) {
                /** Remove product from cart data */
                if ($cart_item['instance'] == 'SProducts' && $cart_item['productId'] == $product_id) {
                    unset($cart_data[$key]);
                    $usersUpdateData[] = [
                                          'id'        => $user['id'],
                                          'cart_data' => serialize($cart_data),
                                         ];
                    break;
                }
            }
        }

        /** Update users cart data  */
        if (count($usersUpdateData) > 0) {
            $this->db->update_batch('users', $usersUpdateData, 'id');
        }

        return TRUE;
    }

    /**
     * Delete product custom fields data
     * @param integer $product_id - product id
     * @return int
     * @throws Exception
     */
    public function deleteProductCustomFieldsData($product_id) {

        if (!$product_id) {
            throw new Exception(lang('You did not specified product id'));
        }

        return CustomFieldsDataQuery::create()->setComment(__METHOD__)->filterByentityId($product_id)->delete();
    }

    /**
     * Delete product orders by product ID
     * @param integer $product_id
     * @return boolean
     */
    public function deleteProductOrdes($product_id) {

        if (!$product_id) {
            $this->setError(lang('You did not specified product id'));
            return FALSE;
        }

        $orders = $this->db->where('product_id', $product_id)->get('shop_orders_products');

        if ($orders) {
            $orders = $orders->result();

            foreach ($orders as $key => $order) {
                $orderId[$key] = $order->order_id;
            }

            $modelOrders = SOrdersQuery::create()->setComment(__METHOD__)->findPks($orderId);

            if ($modelOrders) {
                $modelOrders->delete();
            }
        }

        return TRUE;
    }

    /**
     * Return error message.
     *
     * @return string
     */
    public function getError() {

        return $this->error;
    }

    /**
     *
     * @return ProductApi
     */
    public static function getInstance() {

        if (null === self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     *
     * todo: fix value
     * Get product properties by ID
     * @param integer $product_id
     * @param string $locale
     * @return array|bool
     * @throws Exception
     */
    public function getProductProperties($product_id, $locale = 'ru') {

        try {
            if (!$product_id) {
                throw new Exception(lang('You did not specified product id'));
            }

            $model = SProductsQuery::create()->setComment(__METHOD__)->findOneById($product_id);
            if ($model) {
                $categoryId = $model->getCategoryId();

                if ($categoryId) {
                    $categoryModel = SCategoryQuery::create()->setComment(__METHOD__)->findOneById($categoryId);

                    if ($categoryModel) {

                        $properties = SPropertiesQuery::create()->setComment(__METHOD__)->joinWithI18n($locale, Criteria::LEFT_JOIN)->filterByActive(true)->filterByShowInCompare(true)->filterByPropertyCategory($categoryModel)->orderByPosition()->find()->toArray();
                    } else {
                        throw new Exception(lang('Product category does not exists'));
                    }

                    if ($properties) {
                        return $properties;
                    } else {
                        throw new Exception(lang('Product has no properties'));
                    }
                } else {
                    throw new Exception(lang('Product has no category'));
                }
            } else {
                throw new Exception(lang('Product that you specified does not exist'));
            }
        } catch (Exception $exc) {
            $this->setError($exc->getMessage());
            return FALSE;
        }
    }

    /**
     * Get products by ids
     *
     * @param array|int $ids
     * @return SProducts
     */
    public function getProducts($ids) {

        if (is_array($ids)) {
            $model = SProductsQuery::create()->setComment(__METHOD__)->findById($ids);
        } else {
            $model = SProductsQuery::create()->setComment(__METHOD__)->findOneById($ids);
        }
        return $model;
    }

    /**
     * Save product additional image
     * @param integer $productId - product ID
     * @param string $imageName - image name
     * @param integer $position - additional image position
     * @return bool
     */
    public function saveProductAdditionalImage($productId, $imageName, $position) {

        if (!$productId) {
            $this->setError(lang('You did not specified product id'));
            return FALSE;
        }

        if (!$imageName) {
            $this->setError(lang('You did not specified image name'));
            return FALSE;
        }

        if (!is_numeric($position)) {
            $this->setError(lang('You did not specified image position'));
            return FALSE;
        }

        $images = $this->db->where('product_id', $productId)->get('shop_product_images')->result_array();
        $same_pos = $this->db->where('product_id', $productId)->where('position', $position)->get('shop_product_images')->row_array();

        if ($same_pos != NULL) {

            $this->db->where('product_id', $productId)->where('position', $position)->update('shop_product_images', ['image_name' => $imageName]);
        } else {
            if (!$images) {
                $position = 0;
            }

            $data = [
                     'product_id' => $productId,
                     'image_name' => $imageName,
                     'position'   => $position,
                    ];

            $this->db->insert('shop_product_images', $data);
        }

        return TRUE;
    }

    /**
     * Set product property value
     * @param integer $product_id - product ID
     * @param integer $property_id - product property ID
     * @param string $property_value_id - product property value (can be array if property is multiple)
     * @return boolean
     */
    public function setProductPropertyValue($product_id, $property_id, $property_value_id) {

        if (!$product_id || !$property_id) {
            $this->setError(lang('Not valid arguments passed to the method'));
            return FALSE;
        }

        $property = SPropertiesQuery::create()->setComment(__METHOD__)->filterById($property_id)->findOne();

        if (!$property) {
            $this->setError(lang('Property that you specified does not exist'));
            return FALSE;
        }

        /** Check if property is multiple selection */
        if (is_array($property_value_id) && !$property->getMultiple()) {
            $this->setError(lang('Not multiple property cant set few values'));
            return FALSE;
        }

        if (!is_array($property_value_id)) {
            $property_value_id = [$property_value_id];
        }

        $this->deleteProductPropertyValue($product_id, $property_id);

        foreach ($property_value_id as $item) {
            $value = new \SProductPropertiesData();
            $value->setPropertyId($property_id)
                ->setProductId($product_id)
                ->setValueId($item)
                ->save();
        }

        //        todo: create value if not exists

        //        if (is_array($property_value)) {

        /** Check if property is multiple selection */
        //

        //            /** Prepare array to insert */
        //            foreach ($property_value as $value) {
        //                $data[] = [
        //                    'product_id'  => $product_id,
        //                    'property_id' => $property_id,
        //                    'locale'      => $locale,
        //                    'value'       => htmlspecialchars($value),
        //                ];
        //            }
        //            return $this->db->insert_batch('shop_product_properties_data', $data);
        //
        //        } else {
        //            if (!in_array($property_value, $property_data) && !in_array(htmlspecialchars($property_value), $property_data)) {
        //                array_push($property_data, $property_value);
        //                $property_values_to_create = implode(" \n ", $property_data);
        //
        //                $property->setData($property_values_to_create);
        //                $property->save();
        //            }
        //
        //            /** Delete all product properties values */
        //
        //            return $this->db->set('product_id', $product_id)->set('property_id', $property_id)->set('locale', $locale)->set('value', $property_value)->insert('shop_product_properties_data');
        //        }

    }

    /**
     * Delete product property value
     * @param integer $product_id product ID
     * @param integer $property_id product property ID
     * @return boolean
     */
    public function deleteProductPropertyValue($product_id, $property_id) {
        \SProductPropertiesDataQuery::create()
            ->filterByPropertyId($property_id)
            ->findByProductId($product_id)->delete();
    }

    /**
     * Update product by ID
     * @param int $productId - product id
     * @param array $data array of product data for update
     * <br> string $data['url'] (optional) product url
     * <br> int $data['active'] (optional) is product will be show in store - 1 or 0
     * <br> int $data['brand_id'] (optional) brand id
     * <br> int $data['category_id'] (required) category id
     * <br> array $data['additional_categories_ids'] (optional) product additional categories
     * <br> string $data['related_products'] (optional) related products for current product
     * <br> int $data['updated'] (optional) unix timestamp
     * <br> float $data['old_price'] (optional) old price of product
     * <br> int $data['views'] (optional) count of views
     * <br> int $data['hot'] (optional) is product type is "hot" - 1 or 0
     * <br> int $data['action'] (optional) is product type is "action" - 1 or 0
     * <br> int $data['added_to_cart_count'] (optional) count of adding to cart
     * <br> int $data['enable_comments'] (optional) allow leave comments for product - 1 or 0
     * <br> string $data['external_id'] (optional) product external id
     * <br> string $data['tpl'] (optional) set non-standard template file for product
     * <br> int $data['user_id'] (optional) user id who create product
     * <br> string $data['product_name'] (required) product name
     * <br> string $data['short_description'] (optional) short description
     * <br> string $data['full_description'] (optional) full description
     * <br> string $data['meta_title'] (optional) meta title
     * <br> string $data['meta_description'] (optional) meta description
     * <br> string $data['meta_keywords'] (optional) meta keywords
     * <br> float $data['price'] (required) product price
     * <br> string $data['number'] (optional) product SKU
     * <br> int $data['stock'] (optional) count of products in warehouse
     * <br> int $data['position'] (optional) variant position
     * <br> string $data['mainImage'] (optional) product image
     * <br> string $data['var_external_id'] (optional) variant external id
     * <br> int $data['currency'] (required) currency id
     * <br> float $data['price_in_main'] (optional) price in main currency
     * <br> string $data['variant_name'] (optional) product variant name
     * <br> string $data['enable_comments'] (optional) enable comments
     * @param string $locale
     * @param int|null $variant_id
     * @return SProducts
     */
    public function updateProduct($productId, $data = [], $locale = 'ru', $variant_id = NULL) {

        try {
            if (!$productId) {
                throw new Exception(lang('You did not specified product id'));
            }

            if ($data === NULL) {
                throw new Exception(lang('You did not specified data array'));
            }

            if (!is_array($data)) {
                throw new Exception(lang('Second parameter $data must be array'));
            }

            $data['product_id'] = $productId;
            $data = $this->_validateProductData($data, 'update');
            $model = SProductsQuery::create()->setComment(__METHOD__)->findPk($productId);

            if ($model) {
                $model = $this->_setProductData($model, $data);

                $this->updateProductI18N($model->getId(), $data, $locale);

                $this->updateVariant($productId, $data, $locale, $variant_id);

                $this->setProductAdditionalCategories($model, $data);
            } else {
                throw new Exception(lang('Product with such ID not exist'));
            }
            return $model;
        } catch (Exception $exc) {
            $this->setError($exc->getMessage());
            return FALSE;
        }
    }

    /**
     * Update translation for product
     *
     * @param integer $productId
     * @param array $data
     * <br> string $data['product_name'] (required) product name
     * <br> string $data['short_description'] (optional) short description
     * <br> string $data['full_description'] (optional) full description
     * <br> string $data['meta_title'] (optional) meta title
     * <br> string $data['meta_description'] (optional) meta description
     * <br> string $data['meta_keywords'] (optional) meta keywords
     * @param string $locale
     * @return boolean
     * @throws Exception
     */
    public function updateProductI18N($productId, array $data, $locale = 'ru') {

        try {

            if (!$productId) {
                throw new Exception(lang('You did not specified product id'));
            }

            if ($data === NULL) {
                throw new Exception(lang('You did not specified data array'));
            }

            if (!is_array($data)) {
                throw new Exception(lang('Second parameter $data must be array'));
            }

            $model = SProductsI18nQuery::create()->setComment(__METHOD__)->filterByLocale($locale)->findOneById($productId);
            if (!$model) {
                $model = $this->addProductI18N($productId, $data, $locale);
            }
            $this->_setProductI18NData($productId, $model, $data, $locale);
            return $model;
        } catch (Exception $exc) {
            $this->setError($exc->getMessage());
            return FALSE;
        }
    }

    /**
     * Update variant by product ID
     *
     * @param integer $productId product ID
     * @param array $data data array to update
     * <br> float $data['price'] (required) product price
     * <br> string $data['number'] (optional) product SKU
     * <br> int $data['stock'] (optional) count of products in warehouse
     * <br> int $data['position'] (optional) variant position
     * <br> string $data['mainImage'] (optional) product image
     * <br> string $data['var_external_id'] (optional) variant external id
     * <br> int $data['currency'] (required) currency id
     * <br> float $data['price_in_main'] (optional) price in main currency
     * <br> string $data['variant_name'] (optional) product variant name
     * @param string $locale product variant locale
     * @param integer $variantId variant id
     * @return bool|SProductVariants
     */
    public function updateVariant($productId, array $data, $locale = 'ru', $variantId = NULL) {

        try {

            if (!$productId) {
                throw new Exception(lang('You did not specified product id'));
            }

            if ($data === NULL) {
                throw new Exception(lang('You did not specified data array'));
            }

            if (!is_array($data)) {
                throw new Exception(lang('Second parameter $data must be array'));
            }

            if ($variantId) {
                $model = SProductVariantsQuery::create()->setComment(__METHOD__)->filterById($variantId)->findOne();
            } else {
                $model = SProductVariantsQuery::create()->setComment(__METHOD__)->findOneByProductId($productId);
            }

            if (!$model) {
                return $this->addVariant($productId, $data, $locale);
            }
            $model = $this->_setVariantData($productId, $model, $data);

            if (!$this->updateVariantI18N($model->getId(), $data, $locale)) {
                $this->addVariantI18N($model->getId(), $data, $locale);
            }

            return $model;
        } catch (Exception $exc) {
            $this->setError($exc->getMessage());
            return FALSE;
        }
    }

    /**
     * Update translation of product variant by variant ID
     * @param integer $variantId product variant ID
     * @param array $data array to update
     * <br> string $data['variant_name'] (optional) product variant name
     * @param string $locale product variant locale
     * @return bool|SProductVariantsI18n
     * @throws Exception
     */
    public function updateVariantI18N($variantId, array $data, $locale = 'ru') {

        try {

            if (!$variantId) {
                throw new Exception(lang('You did not specified product variant id'));
            }

            if ($data === NULL) {
                throw new Exception(lang('You did not specified data array'));
            }

            if (!is_array($data)) {
                throw new Exception(lang('Second parameter $data must be array'));
            }

            $model = SProductVariantsI18nQuery::create()->setComment(__METHOD__)->filterByLocale($locale)->findOneById($variantId);
            if (!$model) {
                return FALSE;
            }
            $model->setLocale($locale);
            $model->setName($data['variant_name']);
            $model->save();
            return $model;
        } catch (Exception $exc) {
            $this->setError($exc->getMessage());
            return FALSE;
        }
    }

}