<?php

use Category\CategoryApi;
use Currency\Currency;
use Products\ProductApi;
use CMSFactory\ModuleSettings;
// use MediaManager\GetImages;
use MediaManager\Image;

use parse_yandex_xml\classes\DataGetter\ConfigXMLReaderUrl;
use parse_yandex_xml\classes\DataGetter\ConfigXMLReader;

(defined('BASEPATH')) OR exit('No direct script access allowed');

/**
 * Image CMS
 * Module Frame
 */
class Cron_updete extends MY_Controller
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
        $this->settings = ModuleSettings::ofModule('parse_yandex_xml')->get()['cron']['to_update'];

        $cur_shop = \Currency\Currency::create()->getCurrencies();
        $codeAndId = [];
        foreach ($cur_shop as $val) {
            $codeAndId[$val->getCode()] = $val->getId();
        }
        $this->codeAndId = $codeAndId;// для валют
    }


    public function update($provider)
    {

$providers = ModuleSettings::ofModule('parse_yandex_xml')->get()['list_provider_files'];
        $site_url_link = $providers[$provider];
        
         
        if(!key_exists($provider, $providers) || $provider== null || $site_url_link =='' || $site_url_link==null){
            exit();
        }
        unlink('./uploads/files/create_Yml_file_update.xml');

        if (substr_count($site_url_link, 'http://') > 0 || substr_count($site_url_link, 'https://') > 0) {
            if ($this->settings['on_off'] == '1') {
                if (file_put_contents('./uploads/files/create_Yml_file_update.xml', file_get_contents($site_url_link)) == 0) {
                    $curl = curl_init();
                    curl_setopt($curl, CURLOPT_URL, $site_url_link);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0');
                    $out = curl_exec($curl);

                    file_put_contents('./uploads/files/create_Yml_file_update.xml', $out);

                }
            } else {
                dd('Опция для обновления не активна');
                return false;
                dd('Опция для обновления не активна');
            }

            $site_url = 'uploads/files/create_Yml_file_update.xml';
        } else {
            dd('Сыылка на YML файл не задана');
            return false;
            dd('Сыылка на YML файл не задана');
        }

//        require_once('ConfigXMLReaderUrl.php');
        if ($site_url_link == 'http://rn-sport.com.ua/index.php?route=feed/hotline') {
            $reader1 = new ConfigXMLReader($site_url);
            // чтобы не тратить память на хранение ненужных элементов, мы их просто выбрасываем на каждой итерации
            $reader1->onEvent('afterParseElement', function ($name, $context) {
                $context->clearResult();
            });
            // мы хотим получать только настройки наценок
            // эта анонимная функция(PHP5.3 и выше) будет вызвана сразу по завершению парсинга элементов <offer>
            $reader1->onEvent('parseOffer', function ($context) {
                $ratio = $context->getResult()['offers'][0];
            });
            // запускаем парсинг

//                $reader->parseCats($this->input->post());
            $reader1->parse1($this->input->post());
            $parse_result = $reader1->resultOfferAlfa;


        }else{
            $reader = new ConfigXMLReaderUrl($site_url); //uploads/tekstil.xml
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

            $parse_result = $reader->resultOfferAlfa;
        }



        $prods_array = $parse_result['all_offers'];


        unset($parse_result);

        $count_new_offers = self::update_naw_products($prods_array, $provider);
        dd('Обновлено ' . $count_new_offers . ' товаров!');
        return $count_new_offers;

        return json_encode(array(lang('Создано ', '') . $created_prods . lang(' товаров ', ''), 'g'));

    }

    private function update_naw_products($all_offers, $provider)
    {
        $provider = '-' .$provider;

        /*
        Фикс пустых значений свойств для товара после загрузки товаров
        */
        $this->db->query('DELETE FROM  shop_product_properties_data WHERE  value_id IS NULL');

        /*
        Фикс пустых значений свойств для товара после загрузки товаров
        */
        $this->db->query("UPDATE shop_product_variants SET stock= 0 WHERE  external_id = '$provider'");

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
            ->where('external_id', $provider)
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
// dump($product['number']);
            $my_some_counter++;
 
            if (key_exists($product['number'], $exist_prods_ids)){
                 
                $this->db->where('product_id', $exist_prods_ids[$product['id']])->delete('shop_product_images');
                foreach ($product['pictures'] as $key_pic => $val_pic) {
                    // if ($key_pic == 0) {
                    //     if(!file_exists('./uploads/shop/products/origin/' . $product['id'] . pathinfo($val_pic, PATHINFO_BASENAME))){
                    //           $pic_data = file_get_contents($val_pic);
                    //         $picture[$exist_prods_ids[$product['id']]] = $product['id'] . pathinfo($val_pic, PATHINFO_BASENAME);
                    //         file_put_contents('./uploads/shop/products/origin/' . $product['id'] . pathinfo($val_pic, PATHINFO_BASENAME), $pic_data);
                    //         // Image::create()
                    //         //     ->resizeById($exist_prods_ids[$product['id']]);
                    //     }
                    //     $picture[$exist_prods_ids[$product['id']]] = $product['id'] . pathinfo($val_pic, PATHINFO_BASENAME);
                    // } else {
                    //     if(!file_exists('./uploads/shop/products/origin/additional/' . $product['id'] . pathinfo($val_pic, PATHINFO_BASENAME))){
                    //         $pic_data = file_get_contents($val_pic);
                    //         file_put_contents('./uploads/shop/products/origin/additional/' . $product['id'] . pathinfo($val_pic, PATHINFO_BASENAME), $pic_data);
                            
                    //         $add_images[$exist_prods_ids[$product['id']]] = pathinfo($val_pic, PATHINFO_BASENAME);
                    //     }
                    //     $data_new_prod_image[$exist_prods_ids[$product['id']]] = ['product_id' => $exist_prods_ids[$product['id']],
                    //             'image_name' => $product['id'] . pathinfo($val_pic, PATHINFO_BASENAME),
                    //             'position' => $key_pic + 1];
                    //     $this->db->insert('shop_product_images', $data_new_prod_image[$exist_prods_ids[$product['id']]]);
                    // }

                    // if ($key_pic == 0) {
                    //     $pic_data = file_get_contents($val_pic);
                        
                    //     if($pic_data == ''|| $pic_data ===null || $pic_data==0){
                    //     $val_pic =  str_replace('http://', 'https://', $val_pic);
                    //         $curl = curl_init();
                    //         curl_setopt($curl, CURLOPT_URL, $val_pic);
                    //         curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    //         curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0');
                    //         $out = curl_exec($curl);                             
                    //         $pic_data = $out;
                    //         $pic_data = file_get_contents($val_pic);
                    //     }
                        
                    //     //     if ($pic_data) {
                    //     $picture = $product['id'] . pathinfo($val_pic, PATHINFO_BASENAME);
                    //     file_put_contents('./uploads/shop/products/origin/' . $product['id'] . pathinfo($val_pic, PATHINFO_BASENAME), $pic_data);
                    //     //         $main_imagec[]=$picture;
                    //     //     }
                    //     // }
                    // } else {
                    //     $pic_data = file_get_contents($val_pic);
                        
                    //     if($pic_data == ''|| $pic_data ===null || $pic_data==0){
                    //     $val_pic =  str_replace('http://', 'https://', $val_pic);
                    //         $curl = curl_init();
                    //         curl_setopt($curl, CURLOPT_URL, $val_pic);
                    //         curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    //         curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0');
                    //         $out = curl_exec($curl);                             
                    //         $pic_data = $out;
                    //         $pic_data = file_get_contents($val_pic);
                    //     }
                    //     //     if ($pic_data) {
                    //     file_put_contents('./uploads/shop/products/origin/additional/' . $product['id'] . pathinfo($val_pic, PATHINFO_BASENAME), $pic_data);
                    //     $data_new_prod_image[] = ['product_id' => $prod_vars_count,
                    //         'image_name' => $product['id'] . pathinfo($val_pic, PATHINFO_BASENAME),
                    //         'position' => $key_pic + 1];
                    //     $add_images[] = pathinfo($val_pic, PATHINFO_BASENAME);
                    //     //     }
                    //     // }
                    // }
                }
//dump($product['number']);
 
                $count_for_ext_prods++;
                $data_upd_prod[] = [
                'active' => $product['available'] ? 1 : 0,
                    'id' => $exist_prods_ids[$product['number']],
                    'hot' => $product['is_new'] != '0' ? 1 : 0,

                    'updated' => time(),
                ];
                $data_upd_var[] = [
                    'id' => $exist_prods_ids[$product['number']],
                    'price_in_main' => $product['price'],
                    'currency' => $codeAndId[$product['currencyId']],
//                    'number' => $product['vendorCode'],
                    'stock' => $product['available'] ? 10 : 0,
                    // 'mainImage' =>$picture[$exist_prods_ids[$product['id']]]
                ];
                $data_upd_prodi18[] = [
                    'id' => $exist_prods_ids[$product['number']],
                    'full_description' =>  addslashes(html_entity_decode($product['description']))
                ];
                // if($picture[$exist_prods_ids[$product['id']]] && $picture[$exist_prods_ids[$product['id']]] !=null){
                //    $data_upd_var[]['mainImage'] = $picture[$exist_prods_ids[$product['id']]];
                // }
            }
            unset($pr_key, $product);

        }
        foreach($add_images as $key=>$image){
            Image::create()
                    ->resizeByIdAdditional($key, TRUE);
            unset($key, $image);
        }
        
 
        if ($count_for_ext_prods > 0 && $this->settings['type'] == 'all') {
            // $mainImage ='mainImage = (CASE id';
            // $ids1 = '';
            // foreach($picture as $pr_id=>$main_im){
                   
            //                      $mainImage .= " WHEN '" . $pr_id . "' THEN  '" . $main_im . "' ";
            //                      $ids1 .= '"' . $pr_id . '",';                                 
            // }
            // $mainImage .= 'END)';
            //  $ids1 = rtrim($ids1, ',');
            //  $z1= "UPDATE shop_product_variants SET " . $mainImage .  " WHERE id IN (" . $ids1 . ")";
            // $this->db->query($z1);
            // if ($this->db->_error_message() && $this->db->_error_message() != null && $this->db->_error_message() != '') {
            //     dump('shop_product_variantsPIC');
            //     dd($this->db->_error_message());
            // }
            


            $hot = 'hot = (CASE id';
            $active = 'active = (CASE id';
            $updated = 'updated = (CASE id';
            $ids = '';
            foreach ($data_upd_prod as $key => $val) {                

                $hot .= " WHEN '" . $val['id'] . "' THEN  '" . $val['hot'] . "' ";
                $active .= " WHEN '" . $val['id'] . "' THEN '" . $val['active'] . "' ";
                $updated .= " WHEN '" . $val['id'] . "' THEN '" . $val['updated'] . "' ";
                $ids .= '"' . $val['id'] . '",';
                unset($key, $val);
            }
            $hot .= 'END),';
            $active .= 'END) ,';
            $updated .= 'END) ';
            $ids = rtrim($ids, ',');
            $z = "UPDATE shop_products SET " . $hot . ' ' . $active . ' ' . $updated . " WHERE id IN (" . $ids . ")";
            $this->db->query($z);
            if ($this->db->_error_message() && $this->db->_error_message() != null && $this->db->_error_message() != '') {
                dump('cccc');
                dd($this->db->_error_message());
            }

            $price_in_main = 'price_in_main = (CASE id';
            $currency = 'currency = (CASE id';
//            $number = 'number = (CASE id';
            $stock = 'stock = (CASE id';

            $ids_var = '';
            foreach ($data_upd_var as $keys => $vals) {
                $price_in_main .= " WHEN '" . $vals['id'] . "' THEN  '" . $vals['price_in_main'] . "' ";
                $currency .= " WHEN '" . $vals['id'] . "' THEN '" . $vals['currency'] . "' ";
//                $number .= " WHEN '" . $vals['id'] . "' THEN '" . $vals['number'] . "' ";
                $stock .= " WHEN '" . $vals['id'] . "' THEN '" . $vals['stock'] . "' ";
                $ids_var .= '"' . $vals['id'] . '",';
                unset($keys, $vals);
            }
            $price_in_main .= 'END),';
            $currency .= 'END) ,';

            $stock .= 'END) ';
            $ids_var = rtrim($ids_var, ',');
            $zz = "UPDATE shop_product_variants SET " . $price_in_main . '  ' . $currency  . ' ' . $stock . " WHERE id IN (" . $ids_var . ")";

            $this->db->query($zz);
            if ($this->db->_error_message() && $this->db->_error_message() != null && $this->db->_error_message() != '') {
                dump('zzzz');
                dd($this->db->_error_message());
            }
            $count = (count($data_upd_var));
            unset($count_for_ext_prods);
            unset($data_upd_cat_18n);


            $full_description = 'full_description = (CASE id';
             
            $idsi = '';
            foreach ($data_upd_prodi18 as $keyi => $vali) {                

                $full_description .= " WHEN '" . $vali['id'] . "' THEN  '" . $vali['full_description'] . "' ";
               
                $idsi .= '"' . $vali['id'] . '",';
                unset($keyi, $vali);
            }
            $full_description .= 'END)';
            
            $idsi = rtrim($idsi, ',');
            $zi = "UPDATE shop_products_i18n SET " . $full_description .  " WHERE id IN (" . $idsi . ")";
            $this->db->query($zi);
            if ($this->db->_error_message() && $this->db->_error_message() != null && $this->db->_error_message() != '') {
                dump('shop_products_i18n');
                dd($this->db->_error_message());
            }

            foreach($picture as $pr_id=>$main_im){
                    Image::create()
                                ->resizeById($pr_id);
            }
        }
        if ($count_for_ext_prods > 0 && $this->settings['type'] == 'stk') {
 

            $active = 'active = (CASE id';
            $updated = 'updated = (CASE id';
            $ids = '';
            foreach ($data_upd_prod as $key => $val) {

                $active .= " WHEN '" . $val['id'] . "' THEN '" . $val['active'] . "' ";
                $updated .= " WHEN '" . $val['id'] . "' THEN '" . $val['updated'] . "' ";
                $ids .= '"' . $val['id'] . '",';
                unset($key, $val);
            }

            $active .= 'END) ,';
            $updated .= 'END) ';
            $ids = rtrim($ids, ',');
            $z = "UPDATE shop_products SET " . $active . ' ' . $updated . " WHERE id IN (" . $ids . ")";
            $this->db->query($z);
            if ($this->db->_error_message() && $this->db->_error_message() != null && $this->db->_error_message() != '') {
                dump('cccc');
                dd($this->db->_error_message());
            }


//            $number = 'number = (CASE id';
            $stock = 'stock = (CASE id';
            $ids_var = '';
            foreach ($data_upd_var as $keys => $vals) {
//                $number .= " WHEN '" . $vals['id'] . "' THEN '" . $vals['number'] . "' ";
                $stock .= " WHEN '" . $vals['id'] . "' THEN '" . $vals['stock'] . "' ";
                $ids_var .= '"' . $vals['id'] . '",';
                unset($keys, $vals);
            }

//            $number .= 'END) ,';
            $stock .= 'END) ';
            $ids_var = rtrim($ids_var, ',');
            $zz = "UPDATE shop_product_variants SET "  . $stock . " WHERE id IN (" . $ids_var . ")";

            $this->db->query($zz);
            if ($this->db->_error_message() && $this->db->_error_message() != null && $this->db->_error_message() != '') {
                dump('zzzz');
                dd($this->db->_error_message());
            }
            $count = (count($data_upd_var));
            unset($count_for_ext_prods);
            unset($data_upd_cat_18n);
        }
        if ($count_for_ext_prods > 0 && $this->settings['type'] == 'prc') {


            $price_in_main = 'price_in_main = (CASE id';
            $currency = 'currency = (CASE id';

            $ids_var = '';
            foreach ($data_upd_var as $keys => $vals) {
                $price_in_main .= " WHEN '" . $vals['id'] . "' THEN  '" . $vals['price_in_main'] . "' ";
                $currency .= " WHEN '" . $vals['id'] . "' THEN '" . $vals['currency'] . "' ";

                $ids_var .= '"' . $vals['id'] . '",';
                unset($keys, $vals);
            }
            $price_in_main .= 'END),';
            $currency .= 'END)';

            $ids_var = rtrim($ids_var, ',');
            $zz = "UPDATE shop_product_variants SET " . $price_in_main . '  ' . $currency . " WHERE id IN (" . $ids_var . ")";

            $this->db->query($zz);
            if ($this->db->_error_message() && $this->db->_error_message() != null && $this->db->_error_message() != '') {
                dump('zzzz');
                dd($this->db->_error_message());
            }
            $count = (count($data_upd_var));
            unset($count_for_ext_prods);
            unset($data_upd_cat_18n);
        }
        if ($count_for_ext_prods > 0 && $this->settings['type'] == 'prc_stk') {


            $price_in_main = 'price_in_main = (CASE id';
            $currency = 'currency = (CASE id';
//            $number = 'number = (CASE id';
            $stock = 'stock = (CASE id';
            $ids_var = '';
            foreach ($data_upd_var as $keys => $vals) {
                $price_in_main .= " WHEN '" . $vals['id'] . "' THEN  '" . $vals['price_in_main'] . "' ";
                $currency .= " WHEN '" . $vals['id'] . "' THEN '" . $vals['currency'] . "' ";
//                $number .= " WHEN '" . $vals['id'] . "' THEN '" . $vals['number'] . "' ";
                $stock .= " WHEN '" . $vals['id'] . "' THEN '" . $vals['stock'] . "' ";
                $ids_var .= '"' . $vals['id'] . '",';
                unset($keys, $vals);
            }
            $price_in_main .= 'END),';
            $currency .= 'END) ,';
//            $number .= 'END) ,';
            $stock .= 'END) ';
            $ids_var = rtrim($ids_var, ',');
            $zz = "UPDATE shop_product_variants SET " . $price_in_main . '  ' . $currency . ' ' . $stock . " WHERE id IN (" . $ids_var . ")";

            $this->db->query($zz);
            if ($this->db->_error_message() && $this->db->_error_message() != null && $this->db->_error_message() != '') {
                dump('zzzz');
                dd($this->db->_error_message());
            }
            $count = (count($data_upd_var));
            unset($count_for_ext_prods);
            unset($data_upd_cat_18n);
        }

        Currency::create()->checkPrices();
        return $count;
    }

}

/*
  Родительский класс для XML обработчиков.
 */




