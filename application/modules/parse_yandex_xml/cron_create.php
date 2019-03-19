<?php

use Category\CategoryApi;
use Currency\Currency;
use Products\ProductApi;
use CMSFactory\ModuleSettings;
// use MediaManager\GetImages;
use MediaManager\Image;

use core\models\Route;
use Exception;
use stdClass;
use CI_DB_active_record;

(defined('BASEPATH')) OR exit('No direct script access allowed');

/**
 * Image CMS
 * Module Frame
 */
class Cron_create extends MY_Controller
{

   
    private $arr = [];
    private $xml;
    protected $result_create = array();


    public function __construct()
    {
        parent::__construct();
        $lang = new MY_Lang();
        $lang->load('parse_yandex_xml');
        $this->load->helper('translit');
        $this->time = time();
        $this->settings = ModuleSettings::ofModule('parse_yandex_xml')->get()['cron']['to_create'];

        $cur_shop = \Currency\Currency::create()->getCurrencies();
        $codeAndId = [];
        foreach ($cur_shop as $val) {
            $codeAndId[$val->getCode()] = $val->getId();
        }
        $this->codeAndId = $codeAndId;
    }


    public function create($provider)
    {
        $providers = ModuleSettings::ofModule('parse_yandex_xml')->get()['list_provider_files'];
        $site_url_link = $providers[$provider];
        if(!key_exists($provider, $providers) || $provider== null || $site_url_link =='' || $site_url_link==null){

            exit();
        }
//        $site_url_link = ModuleSettings::ofModule('parse_yandex_xml')->get()['cron']['url_oasis_cron'];
        if (substr_count($site_url_link, 'http://') > 0 || substr_count($site_url_link, 'https://') > 0) {
            if ($this->settings['on_off'] == '1') {
                if (file_put_contents('./uploads/files/create_Yml_file_create.xml', file_get_contents($site_url_link)) == 0) {
                    $curl = curl_init();
                    curl_setopt($curl, CURLOPT_URL, $site_url_link);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0');
                    $out = curl_exec($curl);

                    file_put_contents('./uploads/files/create_Yml_file_create.xml', $out);

                }
            } else {
                dd('Опция для Создания не активна');
                return false;
                dd('Опция для обновления не активна');
            }

            $site_url = 'uploads/files/create_Yml_file_create.xml';
        } else {
            dd('Сыылка на YML файл не задана');
            return false;
            dd('Сыылка на YML файл не задана');
        }

        require_once('ConfigXMLReaderUrlOasis.php');

        $reader = new ConfigXMLReaderUrlOasis($site_url); //uploads/tekstil.xml
        // чтобы не тратить память на хранение ненужных элементов, мы их просто выбрасываем на каждой итерации
        $reader->onEvent('afterParseElement', function ($name, $context) {
            $context->clearResult();
        });
        // мы хотим получать только настройки наценок
        // эта анонимная функция(PHP5.3 и выше) будет вызвана сразу по завершению парсинга элементов <offer>
        $reader->onEvent('parseOffer', function ($context) {
            $ratio = $context->getResult()['offers'][0];
        });
        // запускаем парсинг

        $reader->parse($this->input->post());
        $reader1 = new ConfigXMLReaderUrlOasis($site_url);
        $reader1->parseVendors();

        $parse_result = $reader->resultOfferAlfa;

        $reader = new ConfigXMLReaderUrlOasis($site_url); //uploads/tekstil.xml
        // чтобы не тратить память на хранение ненужных элементов, мы их просто выбрасываем на каждой итерации
        $reader->onEvent('afterParseElement', function ($name, $context) {
            $context->clearResult();
        });
        // мы хотим получать только настройки наценок
        // эта анонимная функция(PHP5.3 и выше) будет вызвана сразу по завершению парсинга элементов <offer>
        $reader->onEvent('parseOffer', function ($context) {
            $ratio = $context->getResult()['offers'][0];
        });
        // запускаем парсинг

        $reader->parse();

        $reader1 = new ConfigXMLReaderUrlOasis($site_url);
        $reader1->parseVendors();
        $parse_result1 = $reader1->resultOfferAlfa;
        $this->vendors_create($parse_result1['vendors']);
unset($parse_result1);
        $parse_result = $reader->resultOfferAlfa;

        $prods_array = $parse_result['all_offers'];

        unset($parse_result);

        $count_new_offers = self::create_naw_products($prods_array, $provider);
        dd('Создано ' . $count_new_offers . ' товаров!');
        return $count_new_offers;

        return json_encode(array(lang('Создано ', '') . $created_prods . lang(' товаров ', ''), 'g'));

    }

    private function create_naw_products($all_offers, $provider)
    {
/*
Фикс пустых значений свойств для товара после загрузки товаров
*/
        $this->db->query('DELETE FROM  shop_product_properties_data WHERE  value_id IS NULL');

/*
Фикс пустых значений свойств для товара после загрузки товаров
*/

        $codeAndId = $this->codeAndId;

        $locale_shop = \MY_Controller::getDefaultLanguage();

        $pr_cats = $this->db->select('id,external_id')
            ->get('shop_category')->result_array();
        foreach ($pr_cats as $cat_key => $cat) {
            $exist_cats[$cat['external_id']] = $cat['id'];
            unset($cat_key, $cat);
        }

        $pr_brands = $this->db->select('id, url')->get('shop_brands')->result_array();
        foreach ($pr_brands as $br_key => $brand) {
            $exist_brands[$brand['url']] = $brand['id'];
            unset($br_key, $brand);
        }

        $prod_exist_ids = $this->db
            ->select('id, number')
            ->get('shop_product_variants')->result_array();
        foreach ($prod_exist_ids as $prod_key => $prod) {
            $exist_prods_ids[$prod['number']] = $prod['id'];
            unset($prod_key, $prod);
        }

        $prod_exist_ids_exter = $this->db
            ->select('id, external_id')
            ->get('shop_product_variants')->result_array();
        foreach ($prod_exist_ids_exter as $prod_key_exter => $prod_exter) {
            $exist_prods_ids_exter[$prod_exter['external_id']] = $prod_exter['id'];
            unset($prod_key_exter, $prod_exter);
        }

        $prod_vars_count = $this->db->query('SELECT id FROM shop_product_variants ORDER BY id DESC LIMIT 1')->result()['0']->id;
        $count_for_ext_prods = 0;
        $count_for_new_prod = 0;
        $my_some_counter = 0;

        foreach ($all_offers as $pr_key => $product) {
            $my_some_counter++;

            if (!key_exists($product['number'], $exist_prods_ids) && !key_exists($provider, $exist_prods_ids_exter)) {
                if (key_exists($product['categoryId'].$provider, $exist_cats)) {

                    $count_for_new_prod++;
                    $prod_vars_count++;

                    foreach ($product['pictures'] as $key_pic => $val_pic) {
                        if ($key_pic == 0) {
                            $pic_data = file_get_contents($val_pic);
                            $picture = $product['id'] . pathinfo($val_pic, PATHINFO_BASENAME);
                            file_put_contents('./uploads/shop/products/origin/' . $product['id'] . pathinfo($val_pic, PATHINFO_BASENAME), $pic_data);
                        } else {
                            $pic_data = file_get_contents($val_pic);
                            file_put_contents('./uploads/shop/products/origin/additional/' . $product['id'] . pathinfo($val_pic, PATHINFO_BASENAME), $pic_data);
                            $data_new_prod_image[] = ['product_id' => $prod_vars_count,
                                'image_name' => $product['id'] . pathinfo($val_pic, PATHINFO_BASENAME),
                                'position' => $key_pic + 1];
                            $add_images[] = pathinfo($val_pic, PATHINFO_BASENAME);
                        }
                        unset($key_pic, $val_pic);
                    }


                    $data_new_prod[$prod_vars_count] = [
                        'id' => $prod_vars_count,
                        'active' => 1,
                        'hot' => 0,
                        'external_id' => $provider,
                        'category_id' => key_exists($product['categoryId'].$provider, $exist_cats) ? $exist_cats[$product['categoryId'].$provider] : 1,//$pr_cat && $pr_cat !=null?$pr_cat:1,
                        'brand_id' => key_exists(translit_url(trim($product['vendor'])), $exist_brands) ? $exist_brands[translit_url(trim($product['vendor']))] : 1,//$pr_brand && $pr_brand !=null?$pr_brand:null,//$product['vendorID']
                        'enable_comments' => 1,
                        'created' => time(),
                        'updated' => time(),

                    ];
                    $data_new_prod_for_urls[$prod_vars_count] = [
                        'id' => $prod_vars_count,
                        'name' => $product['name'],
                        'category_id' => key_exists($product['categoryId'].$provider, $exist_cats) ? $exist_cats[$product['categoryId'].$provider] : 1,
                    ];
                    $data_new_prod_cats[$prod_vars_count] = [
                        'product_id' => $prod_vars_count,
                        'category_id' => key_exists($product['categoryId'].$provider, $exist_cats) ? $exist_cats[$product['categoryId'].$provider] : 1,
                    ];
                    $data_new_prod_i18[] = [
                        'id' => $prod_vars_count,
                        'name' => $product['name'],
                        'locale' => $locale_shop['identif'],
                        'full_description' => html_entity_decode(strip_tags($product['description'])),
                        'short_description' => html_entity_decode(strip_tags($product['description'])),

                    ];
                    $data_new_var[] = [
                        'id' => $prod_vars_count,
                        'product_id' => $prod_vars_count,
                        'price_in_main' => $product['price'] && $product['price'] != '0' ? $product['price'] : $product['price'],
                        'currency' => $codeAndId[$product['currencyId']],
                        'number' => $product['vendorCode'],

                        'stock' => $product['available'] ? 10 : 0,
                        'external_id' => $provider,
                        'mainImage' => $picture,

                    ];
                    $data_new_var_i18[] = [
                        'id' => $prod_vars_count,
                        'name' => $product['name'],
                        'locale' => $locale_shop['identif'],
                    ];
                    if (count($product['params']) > 0) {
                        $one_rpod_full_data[$prod_vars_count] = [
                            'params' => $product['params'],
                            'category_id' => key_exists($product['categoryId'].$provider, $exist_cats) ? $exist_cats[$product['categoryId'].$provider] : 1,
                        ];
                    }

                    $this->result['products'][$prod_vars_count] = $prod_vars_count;

                }
            }
            unset($pr_key, $product);

        }

        if ($count_for_new_prod > 0) {
            $this->db->insert_batch('shop_products', $data_new_prod);
            if($this->db->_error_message() && $this->db->_error_message() !=null && $this->db->_error_message() !='' ){
                dump('CRshop_products');
                dd($this->db->_error_message());
            }
            $this->db->insert_batch('shop_products_i18n', $data_new_prod_i18);
            if($this->db->_error_message() && $this->db->_error_message() !=null && $this->db->_error_message() !='' ){
                dump('CRshop_products_i18n');
                dd($this->db->_error_message());
            }
            $this->db->insert_batch('shop_product_variants', $data_new_var);
            if($this->db->_error_message() && $this->db->_error_message() !=null && $this->db->_error_message() !='' ){
                dump('CRshop_product_variants');
                dd($this->db->_error_message());
            }
            $this->db->insert_batch('shop_product_variants_i18n', $data_new_var_i18);
            if($this->db->_error_message() && $this->db->_error_message() !=null && $this->db->_error_message() !='' ){
                dump('CRshop_product_variants_i18n');
                dd($this->db->_error_message());
            }
            $this->db->insert_batch('shop_product_categories', $data_new_prod_cats);
            if($this->db->_error_message() && $this->db->_error_message() !=null && $this->db->_error_message() !='' ){
                dump('CRshop_product_categories');
                dd($this->db->_error_message());
            }
            $this->db->insert_batch('shop_product_images', $data_new_prod_image);
            if($this->db->_error_message() && $this->db->_error_message() !=null && $this->db->_error_message() !='' ){
                dump('CRshop_product_images');
                dd($this->db->_error_message());
            }


            unset($data_cr_cat);
            unset($data_cr_cat_18n);

            $prod_routes = self::getPathsAndParentsProds($data_new_prod_for_urls);
            $route_id = 'route_id = (CASE id';
            $ids_prs = '';
            foreach ($prod_routes as $key_pr => $val_pr) {
                $route_id .= " WHEN '" . $key_pr . "' THEN  '" . $val_pr['route_id'] . "' ";
                $ids_prs .= '"' . $key_pr . '",';
                unset($key_pr, $val_pr);
            }
            $route_id .= 'END) ';

            $ids_prs = rtrim($ids_prs, ',');
            $z_pr = "UPDATE shop_products SET " . $route_id . " WHERE id IN (" . $ids_prs . ")";
            $this->db->query($z_pr);

                foreach($data_new_var as $var=>$var_data){
                    Image::create()
                        ->resizeById($var_data['id'])
                        ->resizeByIdAdditional($var_data['id'], TRUE);
                        unset($var, $var_data);
                }
            
             /** Check folder and process images * */
                

                // Image::create()->resizeByName($main_imagec);
                // Image::create()->resizeByNameAdditional($add_images);

 
            $properties = self::parseParam($one_rpod_full_data);//
            list($prod_property_new, $prod_property_ex) = $properties;
            $this->fill_product_props($prod_property_new, $prod_property_ex);

            $this->fill_prod_props_exist_vals_not_ext($prod_property_ex);
            $this->fill_prod_props_exist_vals_exist($prod_property_ex);


        }

        Currency::create()->checkPrices();
    }

    public function parseParam($rpods_full_data)
    {
        $all_exist_props = $this->db->select('id, csv_name')->get('shop_product_properties');
        if ($all_exist_props && $all_exist_props != null) {
            $all_exist_props = $all_exist_props->result_array();
            foreach ($all_exist_props as $prop_id => $csv) {
                $exist_props[$csv['csv_name']] = $csv['id'];
                unset($prop_id, $csv);
            }
        }
        unset($all_exist_props);
        $props_count = $this->db->query('SELECT id FROM shop_product_properties ORDER BY id DESC LIMIT 1')->result()['0']->id;

        $all_prop_cats = $this->db->get('shop_product_properties_categories');
        if ($all_prop_cats && $all_prop_cats != null) {
            $all_prop_cats = $all_prop_cats->result_array();
            foreach ($all_prop_cats as $key3 => $prop_cat) {
                // $exist_prop_cats[$prop_cat['property_id']] = $prop_cat['category_id'];

                 $exist_prop_cats[$prop_cat['property_id'].'-'.$prop_cat['category_id']] = $prop_cat['category_id'];//что бі не пропустить существующих при формировании из одинаковіх ключей
                unset($key3, $prop_cat);
            }
        }

        foreach ($rpods_full_data as $prod_id => $pr_data) {


            $options = $pr_data['params'];
            $model1 = [];
            foreach ($options as $opt_key => $property_ob) {

                $property = $property_ob;
                unset($property_ob);

                $by_csv = trim(translit_url($opt_key));

                if (key_exists($by_csv, $exist_props)) {
                    $prod_property_ex[$prod_id][$exist_props[$by_csv]] = [$opt_key, $property];//ИД продукта/ИД свойства / имя и значения свойства


                    if(!key_exists($exist_props[$by_csv].'-'.$pr_data['category_id'], $exist_prop_cats)){
                        $new_prop_catsN[$exist_props[$by_csv].'-'.$pr_data['category_id']] =  ['property_id' => $exist_props[$by_csv], 'category_id' => $pr_data['category_id']] ;
                    }
                    
                } else {
                    $props_count++;
                    $new_prop_cats[] = ['property_id' => $props_count, 'category_id' => $pr_data['category_id']];
                    $data_cr_prop[$by_csv] = [
                        'id' => $props_count,
                        'csv_name' => $by_csv,
                        'active' => 1,
                        'show_on_site' => 1,
                        'multiple' => 0,
                        'main_property' => 1,
                    ];
                    $data_cr_prop_18n[$by_csv] = [
                        'id' => $props_count,
                        'name' => (string)trim($opt_key),
                        'locale' => 'ru'
                    ];

                    $prod_property_new[$prod_id][$by_csv] = [$opt_key, $property];//ИД продукта/CSV свойства / имя и значения свойства

                }
                unset($opt_key, $property);
            }
            unset($options);
            unset($prod_id, $pr_data);
        }
        if ($props_count > 0) {
            $this->db->insert_batch('shop_product_properties_categories', $new_prop_catsN);
            if ($this->db->_error_message() && $this->db->_error_message() != null && $this->db->_error_message() != '') {
                dump('shop_product_properties_categoriesN');
                dd($this->db->_error_message());
            }
            $this->db->insert_batch('shop_product_properties_categories', $new_prop_cats);
            if ($this->db->_error_message() && $this->db->_error_message() != null && $this->db->_error_message() != '') {
                dump('shop_product_properties_categories');
                dd($this->db->_error_message());
            }
            $this->db->insert_batch('shop_product_properties', $data_cr_prop);
            $this->db->insert_batch('shop_product_properties_i18n', $data_cr_prop_18n);
            if($this->db->_error_message() && $this->db->_error_message() !=null && $this->db->_error_message() !='' ){
                dump('xxxx');
                dd($this->db->_error_message());
            }
        }

        unset($exist_props);
//        exit();
        return [$prod_property_new, $prod_property_ex];
    }

    public function fill_product_props($prod_property_new, $prod_property_ex)
    {
        $props_data_count = $this->db->query('SELECT id FROM shop_product_properties_data ORDER BY id DESC LIMIT 1')->result()['0']->id;
        $props_vals_count = $this->db->query('SELECT id FROM shop_product_property_value_i18n ORDER BY id DESC LIMIT 1')->result()['0']->id;

        $all_prop_vals = $this->db
            ->select('shop_product_property_value.id, value, shop_product_property_value.property_id as property_id, shop_product_property_value_i18n.value as value')
            ->join('shop_product_property_value', 'shop_product_property_value.id=shop_product_property_value_i18n.id')
            ->where('locale', 'ru')
            ->get('shop_product_property_value_i18n');
        if ($all_prop_vals && $all_prop_vals != null) {
            $all_prop_vals = $all_prop_vals->result_array();
            foreach ($all_prop_vals as $key2 => $prop_val) {
                $exist_prop_vals[$prop_val['id']] = $prop_val;
                unset($key2, $prop_val);
            }
        }

        if (count($prod_property_new) > 0) {

            $all_exist_props = $this->db->select('id, csv_name')->get('shop_product_properties');
            if ($all_exist_props && $all_exist_props != null) {
                $all_exist_props = $all_exist_props->result_array();
                foreach ($all_exist_props as $prop_id => $csv1) {
                    $exist_props[$csv1['csv_name']] = $csv1['id'];
                    unset($prop_id, $csv1);
                }
            }

            foreach ($prod_property_new as $prod_id => $props) {


                foreach ($props as $csv => $prop) {

                    $props_data_count++;
                    $props_vals_count++;
                        $new_val[$exist_props[$csv]][translit_url(trim($prop['1']))] = [
                            'id' => $props_vals_count,
                            'property_id' => $exist_props[$csv]
                        ];
                        $new_val_i18[$exist_props[$csv]][translit_url(trim($prop['1']))] = [
                            'id' => $props_vals_count,
                            'locale' => 'ru',
                            'value' => trim($prop['1'])
                        ];

                        $prod_prop_data[$props_data_count] = [
                            'id' => $props_data_count,
                            'property_id' => $exist_props[$csv],
                            'product_id' => $prod_id,
                            'value_id' => $props_vals_count
                        ];

                    unset($csv, $prop);
                }
                unset($prod_id, $props);
            }

            $this->db->insert_batch('shop_product_properties_data', $prod_prop_data);
            if($this->db->_error_message() && $this->db->_error_message() !=null && $this->db->_error_message() !='' ){
                dump('111');
                dd($this->db->_error_message());
            }

            foreach ($new_val as $key_val => $val_val) {
                $this->db->insert_batch('shop_product_property_value', $val_val);
                $this->db->insert_batch('shop_product_property_value_i18n', $new_val_i18[$key_val]);
                if($this->db->_error_message() && $this->db->_error_message() !=null && $this->db->_error_message() !='' ){
                    dump('222');
                    dd($this->db->_error_message());
                }
                unset($key_val, $val_val);
            }
        }

        unset($exist_props);
        unset($exist_prop_vals);

    }

    public function fill_prod_props_exist_vals_exist($prod_property_ex)
    {
        $props_data_count = $this->db->query('SELECT id FROM shop_product_properties_data ORDER BY id DESC LIMIT 1')->result()['0']->id;
        $props_vals_count = $this->db->query('SELECT id FROM shop_product_property_value_i18n ORDER BY id DESC LIMIT 1')->result()['0']->id;

        $all_prop_vals = $this->db
            ->select('shop_product_property_value.id, value, shop_product_property_value.property_id as property_id, shop_product_property_value_i18n.value as value')
            ->join('shop_product_property_value', 'shop_product_property_value.id=shop_product_property_value_i18n.id')
            ->where('locale', 'ru')
            ->get('shop_product_property_value_i18n');
        if ($all_prop_vals && $all_prop_vals != null) {
            $all_prop_vals = $all_prop_vals->result_array();
            foreach ($all_prop_vals as $key2 => $prop_val) {
                $exist_prop_vals[$prop_val['id']] = $prop_val;
                unset($key2, $prop_val);
            }
        }
        $all_exist_props = $this->db->select('id, csv_name')->get('shop_product_properties');
        if ($all_exist_props && $all_exist_props != null) {
            $all_exist_props = $all_exist_props->result_array();
            foreach ($all_exist_props as $prop_id => $csv1) {
                $exist_props[$csv1['id']] = $csv1['id'];
                unset($prop_id, $csv1);
            }
        }
        if (count($prod_property_ex) > 0) {
            foreach ($prod_property_ex as $prod_id => $props) {
                $props_data_count++;
                $props_vals_count++;

                foreach ($props as $prop_id => $prop) {

                    $props_data_count++;
                    $props_vals_count++;


                    if (count($exist_prop_vals) > 0) {
                        $data_exist_id = $props_data_count;
                        foreach ($exist_prop_vals as $key_val => $val) {
                            $data_exist_id++;
                            if ($val['property_id'] == $prop_id && translit_url(trim($val['value'])) == translit_url(trim($prop['1']))) {
                                $prod_prop_data_prod_id[$data_exist_id] = [
                                    'id' => $data_exist_id,
                                    'property_id' => $prop_id,
                                    'product_id' => $prod_id,
                                    'value_id' => $val['value_id']
                                ];

                            }
                        }
                    }

                    unset($prop_id, $prop);
                }
                unset($prod_id, $props);
            }

            $this->db->insert_batch('shop_product_properties_data', $prod_prop_data_prod_id);
            if($this->db->_error_message() && $this->db->_error_message() !=null && $this->db->_error_message() !='' ){
                dump('ggggg');
                dd($this->db->_error_message());
            }

        }
        unset($exist_props);
        unset($exist_prop_vals);
        return($prod_property_ex);
    }



    public function  fill_prod_props_exist_vals_not_ext($prod_property_ex)
    {
        if (count($prod_property_ex) > 0) {
            foreach ($prod_property_ex as $prod_id => $props) {

                foreach ($props as $prop_id => $prop) {

                    $property = SPropertiesQuery::create()->findOneById($prop_id);
                    $propertyValue = SPropertyValueQuery::create()
                        ->joinWithI18n('ru')
                        ->useI18nQuery('ru')
                        ->filterByValue(trim($prop['1']))
                        ->endUse()
                        ->findOneByPropertyId($prop_id);
                    $id = false;
                    if (!$property) {
                    } elseif ($propertyValue !== null) {
                        $pr_val_id = $propertyValue->getId();
                    } else {
                        try{
                        $propertyValue = new SPropertyValue();
                        $propertyValue->setLocale('ru')
                            ->setPropertyId($prop_id)
                            ->setValue(trim($prop['1']))
                            ->save();
                        }catch (\Exception $e) {
                           
                        }

                         
                        $pr_val_id = $propertyValue->getId();
                    }
                    ProductApi::getInstance()->setProductPropertyValue($prod_id, $prop_id, $pr_val_id, 'ru');

                    unset($prop_id, $prop);
                }
                unset($prod_id, $props);
            }
        }
        unset($exist_props);
        unset($exist_prop_vals);
    }




    private function getPathsAndParentsProds($data_new_prod)
    {

        // creating id-paths and url-paths of each prod
        foreach ($data_new_prod as $pr_id => $product) {

            $ext_rote = $this->db
                ->where('entity_id', $product['id'])
                ->where('type', 'product')
                ->where('url', translit_url($product['name']) . '-' . $product['id'])
                ->get('route')->result_array();

            if (!$ext_rote || count($ext_rote) == 0 || $ext_rote == null) {
                $parentUrl = $this->db
                    ->where('entity_id', $product['category_id'])
                    ->where('type', 'shop_category')
                    ->get('route')->result_array();


                $parentUrl1 = !empty($parentUrl) ? $parentUrl['0']['parent_url'] . '/' . $parentUrl['0']['url'] : '';

                $route = [
                    'parent_url' => $parentUrl1,
                    'url' => translit_url($product['name']) . '-' . $product['id'],
                    'entity_id' => $product['id'],
                    'type' => 'product',
                ];

                $this->db->insert('route', $route);
                $newRouteId = $this->db->insert_id();
//                $this->db->update('shop_category', ['route_id' => $newRouteId], ['id' => $categoryData['id']]);
                $products[$product['id']]['route_id'] = $newRouteId;
            }
            unset($pr_id, $product);
        }
        unset($data_new_prod);
        return $products;
    }

    public function vendors_create($vendors)
    {
        $brands_count = $this->db->query('SELECT id FROM shop_brands ORDER BY id DESC LIMIT 1')->result()['0']->id;
        $pr_brands = $this->db->select('id, url')->get('shop_brands')->result_array();
        foreach ($pr_brands as $br_key => $brand) {
            $exist_brands[$brand['url']] = $brand['id'];
            unset($br_key, $brand);
        }
        foreach ($vendors as $k => $one_vendor) {


            if (key_exists($k, $exist_brands)) {
            } else {
                $brands_count++;
                $data_cr_br[] = [
                    'id' => $brands_count,
                    'url' => translit_url(trim($one_vendor['name'])),

                    'created' => time(),
                    'updated' => time(),

                ];
                $data_cr_br_18n[] = [
                    'id' => $brands_count,
                    'name' => (string)trim($one_vendor['name']),
                    'locale' => 'ru'
                ];
            }
            unset($k, $one_vendor);
        }


        $this->db->insert_batch('shop_brands', $data_cr_br);
        $this->db->insert_batch('shop_brands_i18n', $data_cr_br_18n);
        unset($data_cr_br);
        unset($data_cr_br_18n);

    }


}

/*
  Родительский класс для XML обработчиков.
 */




