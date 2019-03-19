<?php

use Category\RenderMenu;
use Category\STreeMenu;
use template_manager\classes\TComponentShortcut;

if (!function_exists('getOpenLevels')) {

    function getOpenLevels() {

        $tcs = new TComponentShortcut();
        return $tcs->TMenuColumn->getOpenLevels();
    }

}

if (!function_exists('getCategoryColumns')) {

    function getCategoryColumns($category_id = NULL) {

        $tcs = new TComponentShortcut();
        return $tcs->TMenuColumn->getCategoryColumns($category_id);
    }

}

if (!function_exists('load_catalog_menu')) {

    function load_catalog_menu($menu_path) {
        if (class_exists('\Category\STreeMenu')) {
            STreeMenu::create()
            ->setConf(['cache' => true, 'object_render' => false])
            ->load($menu_path);
        } else {
            RenderMenu::create()
            ->setConfig(['cache' => false])
            ->load($menu_path);
        }

    }

}


if (!function_exists('tpl_locale_date')) {

    function tpl_locale_date($format, $timestamp = 0, $nominative_month = false) {

        /* Get current language locale */
        $lang_locale = MY_Controller::getCurrentLanguage()['locale'];

        if (!$timestamp) {
            $timestamp = time();
        } elseif (!preg_match('/^[0-9]+$/', $timestamp)) {
            $timestamp = strtotime($timestamp);
        }

        switch ($lang_locale) {
            case 'ru_RU':
                $F = $nominative_month ? [
                    1 => 'Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'
                                         ] : [
                    1 => 'Января', 'Февраля', 'Марта', 'Апреля', 'Мая', 'Июня', 'Июля', 'Августа', 'Сентября', 'Октября', 'Ноября', 'Декабря'
                                             ];
                $M = [
                    1 => 'Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн', 'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек'
                     ];
                $l = [
                      'Воскресенье',
                      'Понедельник',
                      'Вторник',
                      'Среда',
                      'Четверг',
                      'Пятница',
                      'Суббота',
                     ];
                $D = [
                      'Вс',
                      'Пн',
                      'Вт',
                      'Ср',
                      'Чт',
                      'Пт',
                      'Сб',
                     ];
                break;

            case 'uk_UA':
                $F = $nominative_month ? [
                    1 => 'Січень', 'Лютий', 'Березень', 'Квітень', 'Травень', 'Червень', 'Липень', 'Серпень', 'Вересень', 'Жовтень', 'Листопад', 'Грудень'
                                         ] : [
                    1 => 'Січня', 'Лютого', 'Березня', 'Квітня', 'Травня', 'Червня', 'Липня', 'Серпня', 'Вересеня', 'Жовтня', 'Листопада', 'Грудня'
                                             ];
                $M = [
                    1 => 'Січ', 'Лют', 'Бер', 'Кві', 'Тра', 'Чер', 'Лип', 'Сер', 'Вер', 'Жов', 'Лис', 'Гру'
                     ];
                $l = [
                      'Неділя',
                      'Понеділок',
                      'Вівторок',
                      'Середа',
                      'Четвер',
                      'П&#8217;ятниця',
                      'Субота',
                     ];
                $D = [
                      'Вс',
                      'Пн',
                      'Вт',
                      'Ср',
                      'Чт',
                      'Пт',
                      'Сб',
                     ];
                break;

            default:
                return date($format, $timestamp);
        }

        $format = str_replace('F', $F[date('n', $timestamp)], $format);
        $format = str_replace('M', $M[date('n', $timestamp)], $format);
        $format = str_replace('l', $l[date('w', $timestamp)], $format);
        $format = str_replace('D', $D[date('w', $timestamp)], $format);

        return date($format, $timestamp);
    }

}

if (!function_exists('tpl_album_images')) {

    function tpl_album_images($album_id, $order = 'latest') {

        $ci = &get_instance();

        $ci->db->select('gallery_images.*, gallery_images_i18n.*')->where('gallery_images.album_id', $album_id);

        if ($order == 'latest') {
            $ci->db->order_by('uploaded', 'DESC');
        } elseif ($order == 'random') {
            $ci->db->order_by('uploaded', 'RANDOM');
        } else {
            $ci->db->order_by('uploaded', 'DESC');
        }

        $locale = MY_Controller::getCurrentLocale();
        $query = $ci->db
            ->join('gallery_images_i18n', "gallery_images_i18n.id=gallery_images.id AND locale='$locale'", 'left')
            ->get('gallery_images');

        if ($query && $query->num_rows() > 0) {
            $result = $query->result_array();

            $recount = count($result);

            for ($i = 0; $i < $recount; $i++) {
                $result[$i]['url'] = 'gallery/album/' . $result[$i]['album_id'] . '/image/' . $result[$i]['id'];
                $result[$i]['file_path'] = 'uploads/gallery/' . $result[$i]['album_id'] . '/' . $result[$i]['file_name'] . '_prev' . $result[$i]['file_ext'];
            }

            return $result;
        } else {
            return [];
        }
    }

}

if (!function_exists('tpl_compare_cat_properties')) {

    function tpl_category_properties($cat_id) {

        return ShopCore::app()->SPropertiesRenderer->renderCategoryPropertiesArray($cat_id);
    }

}

if (!function_exists('tpl_product_compare_properties')) {

    function tpl_product_compare_properties($product, $names) {

        $properties = ShopCore::app()->SPropertiesRenderer->renderPropertiesCompareArray($product);
        $result = [];
        foreach ($names as $name) {
            if (isset($properties[$name])) {
                $value = is_array($properties[$name]) ? implode(', ', $properties[$name]) : $properties[$name];
            } else {
                $value = null;
            }
            $result[$name] = $value;
        }
        return $result;
    }

}


if (!function_exists('tpl_menu_parent_link')) {

    function tpl_menu_parent_link($link) {

        return substr($link, 0, strrpos($link, '/'));
    }

}


if (!function_exists('tpl_category_active_filter')) {

    function tpl_category_active_filter($cat_id, $sections) {

        foreach ($sections as $section) {
            foreach ($section as $categories) {
                foreach ($categories as $category) {
                    if ($category['id'] == (int) $cat_id) {
                        return $category['name'];
                    }
                }
            }
        }
    }

}

if (!function_exists('tpl_self_url')) {

    function tpl_self_url() {

        $ci = &get_instance();
        return media_url($ci->uri->uri_string());
    }

}


if (!function_exists('getPropertyTypes')) {

    function getPropertyTypes($property_id = NULL) {

        $tcs = new TComponentShortcut();
        return $tcs->TProperties->getPropertyTypes($property_id);
    }

}


if (!function_exists('tpl_load_comments')) {

    function tpl_load_comments() {

        $ci = &get_instance();
        $comments_module = $ci->load->module('comments/commentsapi')->renderAsArray($ci->uri->uri_string());
        return $comments_module['comments'];
    }

}


if (!function_exists('tpl_per_page_selected')) {

    function tpl_per_page_selected($current_item, $use_get = true) {

        $ci = &get_instance();
        if ($ci->input->get('user_per_page') && $use_get) {
            $result = $ci->input->get('user_per_page');
        } else {
            if (method_exists('SSettings', 'getArrayFrontProductsPerPage')) {
                $result = ShopCore::app()->SSettings->getFrontProductsPerPage();
            } else {
                /* Deprecated from 4.10 */
                $result = ShopCore::app()->SSettings->frontProductsPerPage;
            }
        }
        return $result == $current_item;
    }

}

if (!function_exists('tpl_per_page_array')) {

    function tpl_per_page_array() {

        if (method_exists('SSettings', 'getArrayFrontProductsPerPage')) {
            return unserialize(ShopCore::app()->SSettings->getArrayFrontProductsPerPage());
        } else {
            /* Deprecated from 4.10 */
            return unserialize(ShopCore::app()->SSettings->arrayFrontProductsPerPage);
        }
    }

}

if (!function_exists('tpl_query_string_filter')) {

    function tpl_http_query_filter($params) {

        $ci = &get_instance();
        $get_array = $ci->input->get();
        $filter_array = array_diff_key($get_array, array_flip($params));
        return http_build_query($filter_array);
    }

}

if (!function_exists('tpl_get_default_sorting')) {

    function tpl_get_default_sorting($default_order, $use_get = true) {

        $ci = &get_instance();

        if ($use_get && $ci->input->get('order')) {
            return $ci->input->get('order');
        }
        $sorting = ShopCore::app()->SSettings->getSortingFront($default_order);
        return count($sorting) ? array_shift($sorting)['get'] : null;

    }

}

if (!function_exists('tpl_encode')) {

    function tpl_encode($data) {

        return htmlspecialchars(strip_tags($data));
    }

}

if (!function_exists('tpl_money_to_str')) {

    function tpl_money_to_str($prices) {

        $prices_string = '';

        foreach($prices as $price){
            $prices_string .= $price->getPrice().'^'.$price->getCoins().'|';
        }

        return rtrim($prices_string, '|');
    }

}

/* DEPRECATED from 4.11 */
if (!function_exists('tpl_variant_or_product_name')) {

    /**
     * @param SProducts $variant
     * @return mixed
     */
    function tpl_variant_or_product_name($variant) {

        if (method_exists($variant, 'getNameOrProductName')) {
            return $variant->getNameOrProductName();
        }

        return $variant->getName();
    }

}


/* DEPRECATED from 4.11 */
if (!function_exists('tpl_is_product_archived')) {

    /**
     * @param SProducts $item
     * @return bool
     */
    function tpl_is_product_archived($item) {
        return (bool) method_exists($item, 'getArchive') ? $item->getArchive() : 0;
    }

}

if (!function_exists('tpl_is_filtered')) {

    function tpl_is_filtered($minPrice, $maxPrice) {

        $ci = &get_instance();
        if ($ci->input->get('brand') != ''
            or $ci->input->get('pv') != ''
            or ($ci->input->get('lp') and $ci->input->get('lp') != $minPrice)
            or ($ci->input->get('rp') and $ci->input->get('rp') != $maxPrice)
        ) {
            return true;
        }

        return false;
    }

}

if (!function_exists('tpl_date_dif')) {

    function tpl_date_dif($expire) {

        $nowDate = new DateTime('now');
        $expireDate = new DateTime(date('c', $expire));
        return $expireDate->diff($nowDate);

    }

}

/* DEPRECATED from 4.11 */
if (!function_exists('tpl_product_comments_votes')) {

    /**
     * @param SProducts $model
     * @return bool
     */
    function tpl_product_comments_votes($model) {

        if (method_exists(CI::$APP->load->module('comments')->load->model('base'), 'get_count_comments')) {

            return CI::$APP->load->module('comments')->load->model('base')->get_count_comments($model->getId());
        }
        return $model->getVotes();
    }

}

/* DEPRECATED from 4.12 */
if (!function_exists('tpl_register_asset')) {

    /**
     * @param string $path
     * @param string $position
     * @return bool|void
     */
    function tpl_register_asset($path, $position = 'before') {

        if (is_array($path)) {

            foreach ($path as $value) {
                tpl_register_asset($value, $position);
            }
        }

        if (method_exists('\CMSFactory\assetManager', 'assetTemplateFiles')) {
            \CMSFactory\assetManager::create()
                ->assetTemplateFiles($path, $position);

        } else {

            $template_name = config_item('template');

            $path_info = pathinfo($path);

            $path = '/templates/' . $template_name . '/' . ltrim($path, '/');

            if ($path_info['extension'] == 'css') {

                CI_Controller::get_instance()->template->registerCssFile($path, $position);
            } elseif ($path_info['extension'] == 'js') {

                CI_Controller::get_instance()->template->registerJsFile($path, $position, false);
            }

        }

    }

}

/* DEPRECATED from 4.12 */
if (!function_exists('tpl_get_kit_order_price')) {

    function tpl_get_kit_order_price(SOrderProducts $kit) {
        if ($kit->hasVirtualColumn('TotalKitPrice')) {
            return $kit->getVirtualColumn('TotalKitPrice');
        }
        return $kit->getKit()->getFinalPrice();
    }
}