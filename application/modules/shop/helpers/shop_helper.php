<?php

use Cart\BaseCart;
use Currency\Currency;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveRecord\ActiveRecordInterface;
use Propel\Runtime\Collection\ObjectCollection;
use Symfony\Component\VarDumper\VarDumper;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

if (!function_exists('promoLabel')) {

    /**
     * @param       $action
     * @param bool      $hot
     * @param bool      $hit
     * @param int|float $disc
     * @return string
     */
    function promoLabel($action, $hot, $hit, $disc) {

        if ($disc >= 100) {
            $disc = 99;
        }
        $out = '';

        if ($action && (int) $action > 0) {
            $out .= '<span class="product-status action"></span>';
        }
        if ($hot && (int) $hot > 0) {
            $out .= '<span class="product-status nowelty"></span>';
        }
        if ($hit && (int) $hit > 0) {
            $out .= '<span class="product-status hit"></span>';
        }
        if ($disc && (float) $disc > 0) {
            $out .= '<span class="product-status discount"><span class="text-el">' . round($disc, 0) . '%</span></span>';
        }
        return $out;
    }

}


if (!function_exists('productImageUrl')) {

    /**
     * @param string     $name
     * @param bool|FALSE $useRand
     * @return string
     */
    function productImageUrl($name, $useRand = FALSE) {

        $rand = ($useRand === TRUE) ? ('?' . rand(1, 1000)) : null;
        return (!empty($name)) ? media_url('uploads/shop/' . $name . $rand) : media_url('uploads/shop/nophoto/nophoto.jpg' . $rand);
    }

}

if (!function_exists('promoLabelBtn')) {

    /**
     * @param $action
     * @param $hot
     * @param int    $hit
     * @param int    $disc
     * @return array
     */
    function promoLabelBtn($action, $hot, $hit, $disc) {

        $out = [];
        if ($action && (int) $action > 0) {
            $out['action'] = $action;
        }
        if ($hot && (int) $hot > 0) {
            $out['hot'] = $hot;
        }
        if ($hit && (int) $hit > 0) {
            $out['hit'] = $hit;
        }
        if ($disc && (float) $disc > 0) {
            $out['disc'] = round($disc, 0);
        }

        return $out;
    }

}

if (!function_exists('getAmountInCart')) {

    /**
     * Checks if product/kit is in cart already
     * @param string  $instance
     * @param integer $id
     * @return int 0 if product is not in cart, or quantity in cart
     */
    function getAmountInCart($instance, $id) {

        $items = BaseCart::getInstance()->getItems();
        foreach ($items['data'] as $itemData) {
            if ($itemData->instance == $instance & $itemData->id == $id) {
                return $itemData->quantity;
            }
        }
        return 0;
    }

}
if (!function_exists('isExistsItems')) {

    /**
     * Сhecking exists items
     * @param string  $instance SProducts|ShopKit
     * @param integer $id       id of product|kit
     * @return boolean
     */
    function isExistsItems($instance, $id) {

        $ci = &get_instance();
        if ($instance == 'SProducts') {
            return $ci->db->where('id', $id)->get('shop_product_variants')->num_rows();
        }

        if ($instance == 'ShopKit') {
            return count($ci->db->where('id', $id)->get('shop_kit')->num_rows());
        }
    }

}

if (!function_exists('getCartItems')) {

    /**
     * Сhecking exists items
     * @param string  $instance SProducts|ShopKit
     * @param integer $id       id of product|kit
     * @return boolean
     */
    function getCartItems($instance, $id) {

        $items = BaseCart::getInstance()->getItems();
        foreach ($items['data'] as $itemData) {
            if ($itemData->instance == $instance & $itemData->id == $id) {
                return $itemData;
            }
        }
        return 0;
    }

}

if (!function_exists('isAviableInStock')) {

    /**
     * Сhecking quantities in stock (if enabled)
     * @todo rename isAvailableInStock
     * @param string  $instance SProducts|ShopKit
     * @param integer $id       id of product|kit
     * @param integer $quantity (optional) needed amount
     * @return boolean
     */
    function isAviableInStock($instance, $id, $quantity = 1) {

        $ordersCheckStocks = ShopCore::app()->SSettings->getOrdersCheckStocks();

        if ($ordersCheckStocks) {

            $stock = getItemStock($instance, $id);

            return $stock > 0 && $stock >= $quantity;
        }

        return true;

    }

}

if (!function_exists('getItemStock')) {

    function getItemStock($instance, $id) {

        switch ($instance) {
            case 'SProducts':
                $model = SProductVariantsQuery::create()->setComment(__METHOD__)->findOneById($id);
                break;
            case 'ShopKit':
                $model = ShopKitQuery::create()->setComment(__METHOD__)->findOneById($id);
                break;
            default:
                return false;
        }

        return $model ? $model->getStock() : false;
    }

}

if (!function_exists('shop_url')) {

    /**
     * @param string $url
     * @return string
     */
    function shop_url($url) {

        if (empty($url)) {
            return '/';
        }
        return site_url('shop/' . $url);
    }

}

if (!function_exists('countRating')) {

    /**
     * @param int $productId
     * @return float|int
     */
    function countRating($productId) {

        $rating = SProductsRatingQuery::create()->setComment(__METHOD__)->findPk($productId);
        if ($rating !== null) {
            $rating = round($rating->getRating() / $rating->getVotes());
        } else {
            $rating = 0;
        }

        return $rating;
    }

}


if (!function_exists('is_property_in_get')) {

    /**
     * @param int    $pId
     * @param string $index
     * @return bool
     */
    function is_property_in_get($pId, $index) {

        $getData = CI::$APP->input->get();
        if (isset($getData['f'][$pId]) && in_array($index, $getData['f'][$pId])) {
            return true;
        }

        return false;
    }

}

if (!function_exists('get_currencies')) {

    function get_currencies() {

        return SCurrenciesQuery::create()->setComment(__METHOD__)->find();
    }

}

// For Windows
if (!function_exists('money_format')) {

    function money_format($format, $price) {

        return round($price, ShopCore::app()->SSettings->getPricePrecision());
    }

}

if (!function_exists('getDefaultLanguage')) {

    /**
     * Get default language
     * @return array
     */
    function getDefaultLanguage() {

        $ci = get_instance();
        $ci->db->cache_on();
        $languages = $ci->db
            ->where('default', 1)
            ->get('languages');

        if ($languages) {
            $languages = $languages->row_array();
        }
        $ci->db->cache_off();

        return $languages;
    }

}

if (!function_exists('setCurentLanguage')) {

    /**
     * Get default language
     * @param SProducts|SProductVariants $model
     */
    function setDefaultLanguage($model) {

        $curentLanguage = getDefaultLanguage();
        $curentLanguage = $curentLanguage['identif'];
        $model->setLocale($curentLanguage);
    }

}

if (!function_exists('getPromoBlock')) {

    /**
     * @param string $type
     * @param int    $limit
     * @param null   $idCategory
     * @param null   $idBrand
     * @return $this|SProductsQuery
     */
    function getPromoBlock($type = 'action', $limit = 10, $idCategory = NULL, $idBrand = NULL) {

        $model = SProductsQuery::create()
            ->joinWithI18n(ShopController::getCurrentLocale())
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
            $model = $model->_or()->filterByOldPrice(true);
        }
        if (strpos($type, 'category') AND ($categoryId = filterCategoryId()) !== false) {
            $model = $model->useShopProductCategoriesQuery()->filterByCategoryId($categoryId)->endUse();
        }

        if (strpos($type, 'date')) {
            $model = $model->orderByUpdated(Criteria::DESC);
        }
        $model = $model->filterByActive(true)->limit($limit)->find();

        return $model;
    }

}
if (!function_exists('filterCategoryId')) {

    /**
     * @return bool|int
     */
    function filterCategoryId() {

        $CI = &get_instance();
        $core_data = $CI->core->core_data;
        if ($core_data['data_type'] == 'product') {

            $productId = $core_data['id'];
            $CI->db->cache_on();
            $CI->db->select('shop_category.id');
            $CI->db->from('shop_category');
            $CI->db->join('shop_products', 'shop_products.category_id = shop_category.id');
            $CI->db->where('shop_products.id', $productId);
            $query = $CI->db->get()->result_array();
            $CI->db->cache_off();

            $idCategory = (int) $query[0]['id'];
        } elseif ($core_data['data_type'] == 'shop_category') {
            $idCategory = (int) $core_data['id'];
        } else {
            $idCategory = (bool) false;
        }
        return $idCategory;
    }

}

if (!function_exists('getProduct')) {

    /**
     * @param int $id
     * @return array|mixed|SProducts
     */
    function getProduct($id) {

        return SProductsQuery::create()
            ->joinWithI18n(MY_Controller::getCurrentLocale())
            ->findPk($id);
    }

}

//Simular Function-------------------------------

if (!function_exists('getSimilarProduct')) {

    /**
     * @param SProducts $model
     * @param int       $limit
     * @return array|mixed|ActiveRecordInterface[]|ObjectCollection|SCurrencies[]
     */
    function getSimilarProduct($model, $limit = 8) {

        return $model->getSimilarPriceProductsModels($limit);
    }

}

//Simular Function END----------------------------

if (!function_exists('getVariant')) {

    /**
     * @param int $pid
     * @param int $vid
     * @return SProductVariants
     */
    function getVariant($pid, $vid) {

        return SProductVariantsQuery::create()
            ->joinWithI18n(ShopController::getCurrentLocale())
            ->filterByProductId($pid)
            ->filterById($vid)
            ->findOne();
    }

}

if (!function_exists('currency_convert')) {

    function currency_convert($val, $currencyId) {

        $currentCurrency = Currency::create()->current;
        $nextCurrency = Currency::create()->additional;
        if ($currencyId == null) {
            $currencyId = $currentCurrency->getId();
        }
        if ($currentCurrency->getId() == $currencyId) {
            $result['main']['price'] = $val;
            if (count(Currency::create()->getCurrencies()) > 1) {
                $result['second']['price'] = Currency::create()->convertnew($val, $nextCurrency->getId());
            }
        } else {
            $result['main']['price'] = Currency::create()->convert($val, $currencyId);
            if (count(Currency::create()->getCurrencies()) > 1) {
                if ($nextCurrency->getId() == $currencyId) {
                    $result['second']['price'] = $val;
                } else {
                    $result['second']['price'] = Currency::create()->convertnew($result['main']['price'], $nextCurrency->getId());
                }
            }
        }
        $result['main']['symbol'] = $currentCurrency->getSymbol();
        if (count(Currency::create()->getCurrencies()) > 1) {
            $result['second']['symbol'] = $nextCurrency->getSymbol();
        }
        return $result;
    }

}

if (!function_exists('count_star')) {

    function count_star($rate) {

        switch ($rate) {
            case 0:
                $result = 'nostar';
                break;
            case 1:
                $result = 'onestar';
                break;
            case 2:
                $result = 'twostar';
                break;
            case 3:
                $result = 'threestar';
                break;
            case 4:
                $result = 'fourstar';
                break;
            case 5:
                $result = 'fivestar';
                break;
            default :
                $result = 'nosrtar';
        }
        echo $result;
    }

}

if (!function_exists('getVariants')) {

    function getVariants($productId) {

        if ($productId != null) {
            $CI = &get_instance();
            return $CI->db->query("SELECT * FROM `shop_product_variants` JOIN `shop_product_variants_i18n` ON shop_product_variants.id=shop_product_variants_i18n.id WHERE locale='" . ShopController::getCurrentLocale() . "' AND `product_id`=" . $productId)->result();
        }
    }

}

if (!function_exists('var_dumps')) {

    function var_dumps($args) {

        $args = func_num_args() === 1 ? array_shift(func_get_args()) : func_get_args();
        VarDumper::dump($args);
    }

}

if (!function_exists('var_dumps_exit')) {

    function var_dumps_exit() {

        $args = func_num_args() === 1 ? array_shift(func_get_args()) : func_get_args();
        var_dumps($args);
        exit;
    }

}

if (!function_exists('dd')) {

    function dd($args) {

        $args = func_num_args() === 1 ? array_shift(func_get_args()) : func_get_args();
        var_dumps_exit($args);
        exit;
    }

}


if (!function_exists('ajax_var_dumps')) {

    function ajax_var_dumps($args) {

        $args = func_num_args() === 1 ? array_shift(func_get_args()) : func_get_args();
        echo '<pre>';
        var_dump($args);
    }

}

if (!function_exists('ajax_var_dumps_exit')) {

    function ajax_var_dumps_exit() {

        $args = func_num_args() === 1 ? array_shift(func_get_args()) : func_get_args();
        ajax_var_dumps($args);
        exit;
    }

}

if (!function_exists('ajax_dd')) {

    /**
     * @param $args
     */
    function ajax_dd($args) {

        $args = func_num_args() === 1 ? array_shift(func_get_args()) : func_get_args();
        ajax_var_dumps($args);
        exit;
    }

}

if (!function_exists('searchResultsInCategories')) {

    /**
     * @param $tree
     * @param $categories
     * @return mixed
     */
    function searchResultsInCategories($tree, $categories) {

        foreach ($tree as $item) {
            if ($item->getLevel() == '0') {
                $id_0 = $item->getId();
                $cat[0][$item->getId()][name] = $item->getName();
                $cat[0][$item->getId()][count] = $categories[$item->getId()];
                $cat[$id_0][$id_1][childs] = 0;
            } elseif ($item->getLevel() == '1') {
                if ($categories[$item->getId()]) {
                    $cat[0][$id_0][childs]++;
                }

                $id_1 = $item->getId();
                $cat[$id_0][$item->getId()][name] = $item->getName();
                $cat[$id_0][$item->getId()][count] = $categories[$item->getId()];
                $cat[$id_0][$id_1][childs] = 0;
            } else {
                if ($categories[$item->getId()]) {
                    $cat[0][$id_0][childs]++;
                    $cat[$id_0][$id_1][childs]++;
                    $cat[$id_1][$item->getId()][name] = $item->getName();
                    $cat[$id_1][$item->getId()][count] = $categories[$item->getId()];
                    $cat[$id_1][$item->getId()][id] = $item->getId();
                }
            }
        }
        return $cat;
    }

}


if (!function_exists('html_wraper')) {

    /**
     * @param array  $data
     * @param array  $template
     * @param string $delimiter
     * @param string $item_delimiter
     * @return string
     */
    function html_wraper($data, $template, $delimiter = '', $item_delimiter = '') {

        $result = '';

        foreach ($data as $key => $value) {
            $pre = '';
            $after = '';

            foreach ($template[0] as $t_key => $t_value) {
                $pre .= '<' . $t_key . ' ' . http_build_query($t_value, '', ' ') . '>';
                $after .= '</' . $t_key . '>';
            }

            if (is_array($value)) {
                $item_value = $pre . implode(', ', $value) . $after;
            } else {
                $item_value = $pre . $value . $after;
            }

            $pre = '';
            $after = '';

            if ($key) {
                if ($template[1]) {
                    foreach ($template[1] as $t_key => $t_value) {
                        $pre .= '<' . $t_key . ' ' . http_build_query($t_value, '', ' ') . '>';
                        $after .= '</' . $t_key . '>';
                    }
                    $item_key = $pre . $key . $delimiter . $after;
                } else {
                    $item_key = $key . $delimiter;
                }
            } else {
                $item_key = '';
            }

            $result .= $item_key . $item_value . $item_delimiter;
        }
        return $result;
    }

}
if (!function_exists('getCountOrders')) {

    /**
     * @param int $status
     * @return int
     */
    function getCountOrders($status = null) {

        $Orders = SOrdersQuery::create();

        return ($status === null) ? $Orders->count() : $Orders->filterByStatus($status)->count();
    }

}
if (!function_exists('getCountProductNotify')) {

    /**
     * @return int
     */
    function getCountProductNotify() {

        return SNotificationsQuery::create()->setComment(__METHOD__)->count();
    }

}
if (!function_exists('createOrderCode')) {

    /**
     *
     * @return string
     */
    function createOrderCode() {

        $ci = get_instance();
        $ci->load->helper('string');

        $result = random_string('alnum', 10);

        $orderKeyCount = SOrdersQuery::create()->setComment(__METHOD__)->filterByKey($result)->select(['order_key'])->limit(1)->find()->count();
        if ($orderKeyCount) {
            $result = createOrderCode();
        }
        return strtolower($result);
    }

}

if (!function_exists('getDefaultSort')) {

    /**
     * @return mixed
     */
    function getDefaultSort() {

        $model = SSortingQuery::create()
            ->orderByPos()
            ->filterByActive(1)
            ->findOne();

        return $model ? $model->getGet() : false;
    }

}

if (!function_exists('getFields')) {

    /**
     * @param string $entity
     * @param int $productId
     * @param string $fieldName
     * @return array|string
     */
    function getFields($entity, $productId, $fieldName = null) {

        return \CustomFields\CustomFields::getInstance()
            ->setEntity($entity)
            ->getFields($productId, $fieldName);
    }

}

if (!function_exists('getProductsFields')) {

    /**
     * @param int $productId
     * @param string $fieldName
     * @return array|string
     */
    function getProductsFields($productId, $fieldName = null) {

        return getFields('product', $productId, $fieldName);
    }

}

if (!function_exists('getCategoryFields')) {

    /**
     * @param int $categoryId
     * @param string $fieldName
     * @return array|string
     */
    function getCategoryFields($categoryId, $fieldName = null) {

        return getFields('category', $categoryId, $fieldName);
    }

}

if (!function_exists('getBrandFields')) {

    /**
     * @param int $brandId
     * @param string $fieldName
     * @return array|string
     */
    function getBrandFields($brandId, $fieldName = null) {

        return getFields('brand', $brandId, $fieldName);
    }

}