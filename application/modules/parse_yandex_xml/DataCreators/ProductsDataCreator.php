<?php

namespace parse_yandex_xml\DataCreators;

use Base\SCurrenciesQuery;
use CMSFactory\ModuleSettings;
use MediaManager\Image;
use parser_upd_prods\DataUpdaters\BrandsDataUpdater;
use parser_upd_prods\DataUpdaters\Property;
use parser_upd_prods\DataUpdaters\Property2;
use Currency\Currency;
use SPropertiesQuery;
use SPropertyValue;
use SPropertyValueQuery;
use Products\ProductApi;


class ProductsDataCreator
{

    protected $dataGetter;

    /**
     * @var \CI_DB_active_record
     */
    protected $db;

    /**
     * @var Images
     */
    protected $images;

    /**
     * @var array
     */
    protected $existingProduct = [];

    /**
     * @var array
     */
    protected $existingImages = [];

    /**
     *
     */
    protected $idsFromYugContract = [];

    /**
     * @var array
     */
    protected $guaranteeInCats = [];

    protected $guaranteePropId;
    protected $def_cur;
    protected $existingCategoriesData;
    protected $existingProductVarData;
    protected $existingProductCatData;
    protected $existingVarDataNumber;
    protected $main_cur_id;

    /**
     * @param IDataGetter $dataGetter
     */
    public function __construct($dataGetter)
    {
        $this->dataGetter = $dataGetter;
        $this->db = \CI::$APP->db;
//        $this->images = new Images(PUBPATH . 'uploads/product_images.json', PUBPATH . 'uploads/shop/products/origin/');
        $this->loadExistingProducts();
        $this->loadExistingVariants();
//        $this->loadImages();
        $this->def_cur = SCurrenciesQuery::create()->findByMain('1')->getFirst()->id;
        $this->getExistingCategories();
        $this->main_cur_id = SCurrenciesQuery::create()->findOneByMain()->getId();

    }

    private function getExistingCategories()
    {
        $exist_cats = \CI::$APP->db
            ->select('id, external_id')
//            ->join('shop_category_i18n', 'shop_category.id=shop_category_i18n.id AND shop_category_i18n.locale="' . $this->locale . '"')
            ->get('shop_category');

        if ($exist_cats && $exist_cats != null) {
            $all_exist_cats = $exist_cats->result_array();
            foreach ($all_exist_cats as $cat_id => $data) {
                $this->existingCategoriesData[$data['external_id']] = $data['id'];
                unset($cat_id, $data);
            }
        }

    }

    private function loadExistingProducts()
    {
        $exist_prods = \CI::$APP->db
            ->select('id, category_id, external_id')
            ->get('shop_products');

        if ($exist_prods && $exist_prods != null) {
            $all_exist_prods = $exist_prods->result_array();
            foreach ($all_exist_prods as $var_id => $data) {
                $this->existingProductData[$data['id']] = $data['id'];
                $this->existingProductCatData[$data['id']] = $data['category_id'];
                unset($var_id, $data);
            }
        }
    }

    private function loadExistingVariants()
    {
        $exist_vars = \CI::$APP->db
            ->select('id, external_id, number')
            ->get('shop_product_variants');

        if ($exist_vars && $exist_vars != null) {
            $all_exist_vars = $exist_vars->result_array();
            foreach ($all_exist_vars as $vars_id => $data_v) {
                $this->existingProductVarData[$data_v['external_id']] = $data_v['id'];
                $this->existingVarDataNumber[$data_v['number']] = $data_v['id'];
                unset($vars_id, $data_v);
            }
        }
    }

    /**
     * @param $productId
     * @return bool
     */
    protected function productExists($productUgId)
    {
        return isset($this->existingProductData[$productUgId]);
    }

    protected function variantExists($productUgId)
    {

        return isset($this->existingProductVarData[$productUgId]);
    }

    /**
     * @return mixed
     */
    public function updateData($all_offers, $count_post, $curs)
    {

        $settings = ModuleSettings::ofModule('parse_yandex_xml')->get()['main_set'];

        $provider = !empty($settings['provider_name_to_current_parsing']) &&
        $settings['provider_name_to_current_parsing'] != null &&
        $settings['provider_name_to_current_parsing'] != '' ? '-' . $settings['provider_name_to_current_parsing'] : '';

        if (!$this->dataGetter['all_offers'] || $this->dataGetter['all_offers'] == '' || $this->dataGetter['all_offers'] == null) {
            return 'empty_data_to_update';
        }
//        foreach ($this->dataGetter['all_offers'] as $prods) {
//            $pr_with_vars[$prods['id']] = $prods;
//        }

        $currensy_shop_codeId = $curs;
        array_splice($all_offers, $count_post);
        if ($count_post > 150) {
            array_splice($all_offers, 0, $count_post - 51);
        }

        $locale_shop = \MY_Controller::getDefaultLanguage();


        $brands = new BrandsDataCreator();
        $all_brands = $brands->getAllBrands();
        $categoryIds = $this->existingCategoriesData;


        $prod_vars_count = $this->db->query('SELECT id FROM shop_product_variants ORDER BY id DESC LIMIT 1')->result()['0']->id;
        $count_for_ext_prods = 0;
        $count_for_new_prod = 0;

        foreach ($all_offers as $pr_key => $product) {

            if (!key_exists(/*$product['id'] . '-' . */$product['number'], $this->existingVarDataNumber)) {
                $count_for_new_prod++;
                $prod_vars_count++;
                foreach ($product['pictures'] as $key_pic => $val_pic) {
//                    $val_pic = str_replace('http://', 'https://', $val_pic);
                    if ($key_pic == 0) {
//                        $pic_data = file_get_contents($val_pic);

                         if (!$pic_data || $pic_data == '' || $pic_data == null || $pic_data == 0) {

                             $curl = curl_init();
                             curl_setopt($curl, CURLOPT_HEADER, 0);
                             curl_setopt($curl, CURLOPT_URL, $val_pic);
                             curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                             curl_setopt($curl, CURLOPT_TIMEOUT, 400);
                             curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0');
                             $out = curl_exec($curl);

//                             $pic_data = file_get_contents($out);
                             $pic_data = $out;
                         }

//                        if ($pic_data) {
                        $picture = $product['id'] . pathinfo($val_pic, PATHINFO_BASENAME);
                        file_put_contents('./uploads/shop/products/origin/' . $product['id'] . pathinfo($val_pic, PATHINFO_BASENAME), $pic_data);
                        $main_imagec[] = $picture;
                        //     }
//                        }
                    } else {

//                        $pic_data = file_get_contents($val_pic);
                         if ($pic_data == '' || $pic_data === null || $pic_data == 0) {
                             $curl = curl_init();
                             curl_setopt($curl, CURLOPT_URL, $val_pic);
                             curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                             curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0');
                             $out = curl_exec($curl);

//                             $pic_data = file_get_contents($out);
                             $pic_data = $out;
                         }
//                        if ($pic_data) {
                        file_put_contents('./uploads/shop/products/origin/additional/' . $product['id'] . pathinfo($val_pic, PATHINFO_BASENAME), $pic_data);
                        $data_new_prod_image[] = ['product_id' => $prod_vars_count,
                            'image_name' => $product['id'] . pathinfo($val_pic, PATHINFO_BASENAME),
                            'position' => $key_pic + 1];
                        $add_images[] = pathinfo($val_pic, PATHINFO_BASENAME);
//                        }
                        // }
                    }
                }

                $prod_cat[$prod_vars_count] = key_exists($product['categoryId'] . $provider, $categoryIds) ? $categoryIds[$product['categoryId'] . $provider] : null;
                $prod_cat[$prod_vars_count] = $prod_cat[$prod_vars_count] == null || $prod_cat[$prod_vars_count] == false ? $this->settings['main_set']['CategoryIdOasis'] : $prod_cat[$prod_vars_count];

                $data_new_prod[$prod_vars_count] = [
                    'id' => $prod_vars_count,
                    'active' => 1,
                    'hot' => 0,
                    'external_id' => $provider,
                    'category_id' => $prod_cat[$prod_vars_count],
                    'brand_id' => key_exists(translit_url(trim($product['vendor'])), $all_brands) ? $all_brands[translit_url(trim($product['vendor']))] : 1,//$pr_brand && $pr_brand !=null?$pr_brand:null,//$product['vendorID']
                    'enable_comments' => 1,
                    'created' => time(),
                    'updated' => time(),
                ];

                $data_new_prod_for_urls[$prod_vars_count] = [
                    'id' => $prod_vars_count,
                    'name' => html_entity_decode(html_entity_decode($product['name'])),
                    'category_id' => $prod_cat[$prod_vars_count]
                ];
                $data_new_prod_cats[$prod_vars_count] = [
                    'product_id' => $prod_vars_count,
                    'category_id' => $prod_cat[$prod_vars_count]
                ];
                if($product['id'] . '-' . $product['number']=='363599315-500761'){
                    // dd($product['description']);
                }
                $data_new_prod_i18[] = [
                    'id' => $prod_vars_count,
                    'name' => html_entity_decode(html_entity_decode($product['name'])),
                    'locale' => $locale_shop['identif'],
                    'full_description' => $product['description'],//html_entity_decode($product['description']),
                    'short_description' => '',
                ];
                $data_new_var[] = [
                    'id' => $prod_vars_count,
                    'product_id' => $prod_vars_count,
                    'price_in_main' => $product['price'] && $product['price'] != '0' ? $product['price'] : $product['price'],
                    'currency' => $currensy_shop_codeId[$product['currencyId']] && $currensy_shop_codeId[$product['currencyId']] != null &&
                    $currensy_shop_codeId[$product['currencyId']] != 0 && $currensy_shop_codeId[$product['currencyId']] != '' ? $currensy_shop_codeId[$product['currencyId']] : $this->main_cur_id,
                    'number' =>$product['number'],// $product['id'] . '-' . $product['number'],

                    'stock' => $product['available'] ? 10 : 0,
                    'external_id' => $provider,
                    'mainImage' => $picture,

                ];
                $data_new_var_i18[] = [
                    'id' => $prod_vars_count,
                    'name' => html_entity_decode(html_entity_decode($product['name'])),
                    'locale' => $locale_shop['identif'],
                ];
                if (count($product['params']) > 0) {
                    $one_rpod_full_data[$prod_vars_count] = [
                        'params' => $product['params'],
                        'category_id' => $prod_cat[$prod_vars_count]
                    ];
                }

                $this->result['products'][$prod_vars_count] = $prod_vars_count;
            }
            unset($pr_key, $product);
        }
        if ($count_for_new_prod > 0) {
            $this->db->insert_batch('shop_products', $data_new_prod);
            if ($this->db->_error_message() && $this->db->_error_message() != null && $this->db->_error_message() != '') {
                dump('CRshop_products');
                dd($this->db->_error_message());
            }
            $this->db->insert_batch('shop_products_i18n', $data_new_prod_i18);
            if ($this->db->_error_message() && $this->db->_error_message() != null && $this->db->_error_message() != '') {
                dump('CRshop_products_i18n');
                dd($this->db->_error_message());
            }
            $this->db->insert_batch('shop_product_variants', $data_new_var);
            if ($this->db->_error_message() && $this->db->_error_message() != null && $this->db->_error_message() != '') {
                dump('CRshop_product_variants');
                dd($this->db->_error_message());
            }
            $this->db->insert_batch('shop_product_variants_i18n', $data_new_var_i18);
            if ($this->db->_error_message() && $this->db->_error_message() != null && $this->db->_error_message() != '') {
                dump('CRshop_product_variants_i18n');
                dd($this->db->_error_message());
            }
            $this->db->insert_batch('shop_product_categories', $data_new_prod_cats);
            if ($this->db->_error_message() && $this->db->_error_message() != null && $this->db->_error_message() != '') {
                dump('CRshop_product_categories');
                dd($this->db->_error_message());
            }
            $this->db->insert_batch('shop_product_images', $data_new_prod_image);
            if ($this->db->_error_message() && $this->db->_error_message() != null && $this->db->_error_message() != '') {
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
            if ($this->db->_error_message() && $this->db->_error_message() != null && $this->db->_error_message() != '') {
                dump('shop_productsURLS');
                dd($this->db->_error_message());
            }

            foreach ($data_new_var as $var => $var_data) {
                Image::create()
                    ->resizeById($var_data['id'])
                    ->resizeByIdAdditional($var_data['id'], TRUE);
                unset($var, $var_data);
            }

            /** Check folder and process images * */


            // Image::create()->resizeByName($main_imagec);
            // Image::create()->resizeByNameAdditional($add_images);

            if ($one_rpod_full_data && $one_rpod_full_data != null) {
                $properties = self::parseParam($one_rpod_full_data);//
                list($prod_property_new, $prod_property_ex) = $properties;
                $this->fill_product_props($prod_property_new, $prod_property_ex);

                $this->fill_prod_props_exist_vals_not_ext($prod_property_ex);
                $this->fill_prod_props_exist_vals_exist($prod_property_ex);
            }

        }
//        if ($count_for_ext_prods > 0) {
//            $active = 'active = (CASE id';
//            $archive = 'archive = (CASE id';
//            $updated = 'updated = (CASE id';
//            $ids = '';
//            foreach ($data_upd_prod as $key => $val) {
//                $active .= " WHEN '" . $val['id'] . "' THEN '" . $val['active'] . "' ";
//                $archive .= " WHEN '" . $val['id'] . "' THEN '" . $val['archive'] . "' ";
//                $updated .= " WHEN '" . $val['id'] . "' THEN '" . $val['updated'] . "' ";
//                $ids .= '"' . $val['id'] . '",';
//                unset($key, $val);
//            }
//            $active .= 'END) ,';
//            $archive .= 'END) ,';
//            $updated .= 'END) ';
//            $ids = rtrim($ids, ',');
//            $z = "UPDATE shop_products SET " . $active . ' ' . $archive . ' ' . $updated . " WHERE id IN (" . $ids . ")";
//            $this->db->query($z);
//            if ($this->db->_error_message() && $this->db->_error_message() != null && $this->db->_error_message() != '') {
//                dump('shop_productUPD');
//                dd($this->db->_error_message());
//            }
//
//            if (count($data_upd_prod_i18) > 0) {
//
//                $description = 'full_description = (CASE id';
//                $ids_var18 = '';
//
//                foreach ($data_upd_prod_i18 as $keys18 => $vals18) {
//                    $description .= " WHEN '" . $vals18['id'] . "' THEN '" . $vals18['description'] . "' ";
//                    $ids_var18 .= '"' . $vals18['id'] . '",';
//                    unset($keys18, $vals18);
//                }
//                $description .= 'END) ';
//                $ids_var18 = rtrim($ids_var18, ',');
//                $zz18 = "UPDATE shop_products_i18n SET " . $description . " WHERE id IN (" . $ids_var18 . ")";
//                $this->db->query($zz18);
//
//                if ($this->db->_error_message() && $this->db->_error_message() != null && $this->db->_error_message() != '') {
//                    dump('shop_products_i18nUPD');
//                    dd($this->db->_error_message());
//                }
//            }
//
//            if (count($data_upd_var) > 0) {
//
//                $mainImage = 'mainImage = (CASE product_id';
//                $ids_var = '';
//
//                foreach ($data_upd_var as $keysV => $valsV) {
//                    $mainImage .= " WHEN '" . $valsV['product_id'] . "' THEN '" . $valsV['mainImage'] . "' ";
//                    $ids_var .= '"' . $valsV['product_id'] . '",';
//                    unset($keysV, $valsV);
//                }
//                $mainImage .= 'END) ';
//                $ids_var = rtrim($ids_var, ',');
//                $zz = "UPDATE shop_product_variants SET " . $mainImage . " WHERE product_id IN (" . $ids_var . ")";
//                $this->db->query($zz);
//                if ($this->db->_error_message() && $this->db->_error_message() != null && $this->db->_error_message() != '') {
//                    dump('shop_product_variantsUPD');
//                    dd($this->db->_error_message());
//                }
//            }
//            $this->db->where_in('product_id', $data_new_prod_imageArr)->delete('shop_product_images');
//
//            $this->db->insert_batch('shop_product_images', $data_new_prod_image);
//            if ($this->db->_error_message() && $this->db->_error_message() != null && $this->db->_error_message() != '') {
//                dump('CRshop_product_images');
//                dd($this->db->_error_message());
//            }
//
//
//            foreach ($data_new_prod_image as $key => $image) {
//                Image::create()
//                    ->resizeByIdAdditional($key, TRUE);
//                unset($key, $image);
//            }
//
////            $props = new Property();
////
////            $properties = $props->get_save_props($one_rpod_full_data);
////
////            list($prod_property_new, $prod_property_ex) = $properties;
////            $props->fill_product_props($prod_property_new, $prod_property_ex);
////
////            $props->fill_prod_props_exist_vals_not_ext($prod_property_ex);
////            $props->fill_prod_props_exist_vals_exist($prod_property_ex);
////
////            unset($count_for_ext_prods);
////            unset($data_upd_cat_18n);
////            \CI::$APP->db->query('DELETE FROM  shop_product_properties_data WHERE  value_id IS NULL');
////            if (\CI::$APP->db->_error_message() && \CI::$APP->db->_error_message() != null && \CI::$APP->db->_error_message() != '') {
////                dump(\CI::$APP->db->last_query());
////                dd(\CI::$APP->db->_error_message());
////            }
//
//
//            $props = new PropertyN();
//            $properties = $props->get_save_props($one_rpod_full_data);
//
//            exit('success');
//
//
//        }
        Currency::create()->checkPrices();
    }

    private
    function getPathsAndParentsProds($data_new_prod)
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

                $exist_prop_cats[$prop_cat['property_id'] . '-' . $prop_cat['category_id']] = $prop_cat['category_id'];//что бі не пропустить существующих при формировании из одинаковіх ключей
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


                    if (!key_exists($exist_props[$by_csv] . '-' . $pr_data['category_id'], $exist_prop_cats)) {
                        $new_prop_catsN[$exist_props[$by_csv] . '-' . $pr_data['category_id']] = ['property_id' => $exist_props[$by_csv], 'category_id' => $pr_data['category_id']];
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
            if ($this->db->_error_message() && $this->db->_error_message() != null && $this->db->_error_message() != '') {
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
            ->select('shop_product_property_value.id,  shop_product_property_value.property_id as property_id, shop_product_property_value_i18n.value as value')
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


//                    if (count($exist_prop_vals) > 0) {
//                        foreach ($exist_prop_vals as $key_val => $val) {
//                            if ($val['property_id'] == $exist_props[$csv] && translit_url(trim($val['value'])) == translit_url(trim($prop['1']))) {
//                                $prod_prop_data[] = [
//                                    'id' => $props_data_count,
//                                    'property_id' => $exist_props[$csv],
//                                    'product_id' => $prod_id,
//                                    'value_id' => $val['value_id']
//                                ];
//                            }
//                            else {//creating new prop vals
//                                $new_val[$exist_props[$csv]][translit_url(trim($prop['1']))] = [
//                                    'id' => $props_vals_count,
//                                    'property_id' => $exist_props[$csv]
//                                ];
//                                $new_val_i18[$exist_props[$csv]][translit_url(trim($prop['1']))] = [
//                                    'id' => $props_vals_count,
//                                    'locale' => 'ru',
//                                    'value' => trim($prop['1'])
//                                ];
//
//                                $prod_prop_data[] = [
//                                    'id' => $props_data_count,
//                                    'property_id' => $exist_props[$csv],
//                                    'product_id' => $prod_id,
//                                    'value_id' => $props_vals_count
//                                ];
//                            }
//
//                            unset($key_val, $val);
//                        }
//                    }
//                    else {
                    //creating new prop vals
                    $new_val[$exist_props[$csv]][translit_url(trim($prop['1']))] = [
                        'id' => $props_vals_count,
                        'property_id' => $exist_props[$csv]
                    ];
                    $new_val_i18[$exist_props[$csv]][translit_url(trim($prop['1']))] = [
                        'id' => $props_vals_count,
                        'locale' => 'ru',
                        'value' => trim($prop['1'])
                    ];

//                    $prod_prop_data[$exist_props[$csv]][translit_url(trim($prop['1']))] = [
//                        'id' => $props_data_count,
//                        'property_id' => $exist_props[$csv],
//                        'product_id' => $prod_id,
//                        'value_id' => $props_vals_count
//                    ];
                    $prod_prop_data[$props_data_count] = [
                        'id' => $props_data_count,
                        'property_id' => $exist_props[$csv],
                        'product_id' => $prod_id,
                        'value_val' => trim($prop['1'])
                    ];

//                    }
                    unset($csv, $prop);
                }
                unset($prod_id, $props);
            }
//            $this->db->insert_batch('shop_product_properties_data', $prod_prop_data);
            if ($this->db->_error_message() && $this->db->_error_message() != null && $this->db->_error_message() != '') {
                dump('111');
                dd($this->db->_error_message());
            }

            foreach ($new_val as $key_val => $val_val) {
//                $this->db->insert_batch('shop_product_properties_data', $prod_prop_data[$key_val]);
                $this->db->insert_batch('shop_product_property_value', $val_val);
                $this->db->insert_batch('shop_product_property_value_i18n', $new_val_i18[$key_val]);
                if ($this->db->_error_message() && $this->db->_error_message() != null && $this->db->_error_message() != '') {
                    dump('222');
                    dd($this->db->_error_message());
                }
                unset($key_val, $val_val);
            }
            $some_count = 0;
            foreach ($prod_prop_data as $som_k => $som_pr_prop_data) {
                $some_count++;
                $created_pr_vals = $this->db
                    ->select('shop_product_property_value.id,  shop_product_property_value.property_id as property_id, shop_product_property_value_i18n.value as value')
                    ->join('shop_product_property_value', 'shop_product_property_value.id=shop_product_property_value_i18n.id')
                    ->where('locale', 'ru')
                    ->where('value', $som_pr_prop_data['value_val'])
                    ->where('property_id', $som_pr_prop_data['property_id'])
                    ->get('shop_product_property_value_i18n');
                $created_pr_vals = $created_pr_vals->result_array();

                $prod_prop_data_val_to_inser[] = [
                    'id' => $some_count + $props_data_count,
                    'property_id' => $som_pr_prop_data['property_id'],
                    'product_id' => $som_pr_prop_data['product_id'],
                    'value_id' => $created_pr_vals['0']['id']
                ];
                unset($som_k, $som_pr_prop_data);
            }
            $this->db->insert_batch('shop_product_properties_data', $prod_prop_data_val_to_inser);
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

//                        $property = SPropertiesQuery::create()->findOneById($prop_id);
//                        $propertyValue = SPropertyValueQuery::create()
//                            ->joinWithI18n('ru')
//                            ->useI18nQuery('ru')
//                            ->filterByValue(trim($prop['1']))
//                            ->endUse()
//                            ->findOneByPropertyId($prop_id);
//                        $id = false;
//                        if (!$property) {
//                        } elseif ($propertyValue !== null) {
//                            $pr_val_id = $propertyValue->getId();
//                        } else {
//                            $propertyValue = new SPropertyValue();
//                            $propertyValue->setLocale('ru')
//                                ->setPropertyId($prop_id)
//                                ->setValue(trim($prop['1']))
//                                ->save();
//                            $pr_val_id = $propertyValue->getId();
//                        }
//                        ProductApi::getInstance()->setProductPropertyValue($prod_id, $prop_id, $pr_val_id, 'ru');


//                         else {//creating new prop vals//
//                            $new_val[$prop_id][translit_url(trim($prop['1']))] = [
//                                'id' => $props_vals_count,
//                                'property_id' => $prop_id
//                            ];
//
//
//                            $new_val_i18[$prop_id][translit_url(trim($prop['1']))] = [
//                                'id' => $props_vals_count,
//                                'locale' => 'ru',
//                                'value' => trim($prop['1'])
//                            ];
//
//                            $prod_prop_data[] = [
//                                'id' => $data_exist_id+$props_data_count,
//                                'property_id' => $prop_id,
//                                'product_id' => $prod_id,
//                                'value_id' => $props_vals_count
//                            ];
//
//                       }
                    }

                    unset($prop_id, $prop);
                }
                unset($prod_id, $props);
            }

            $this->db->insert_batch('shop_product_properties_data', $prod_prop_data_prod_id);
            if ($this->db->_error_message() && $this->db->_error_message() != null && $this->db->_error_message() != '') {
                dump('ggggg');
                dd($this->db->_error_message());
            }
//            $this->db->insert_batch('shop_product_properties_data', $prod_prop_data);
//            if($this->db->_error_message() && $this->db->_error_message() !=null && $this->db->_error_message() !='' ){
//                dump('3333');
//                dd($this->db->_error_message());
//            }

//            foreach ($new_val as $key_val => $val_val) {
//                $this->db->insert_batch('shop_product_property_value', $val_val);
//                $this->db->insert_batch('shop_product_property_value_i18n', $new_val_i18[$key_val]);
//                if($this->db->_error_message() && $this->db->_error_message() !=null && $this->db->_error_message() !='' ){
//                    dump('444');
//                    dd($this->db->_error_message());
//                }
//                unset($key_val, $val_val);
//            }

        }
        unset($exist_props);
        unset($exist_prop_vals);
        return ($prod_property_ex);
    }


    public function fill_prod_props_exist_vals_not_ext($prod_property_ex)
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
                        try {
                            $propertyValue = new SPropertyValue();
                            $propertyValue->setLocale('ru')
                                ->setPropertyId($prop_id)
                                ->setValue(trim($prop['1']))
                                ->save();
                        } catch (\Exception $e) {

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


    /**
     * @return int
     * @throws \Exception
     */
    protected
    function getMainCurrencyId()
    {
        $result = $this->db->select('id')->where('main', 1)->limit(1)->get('shop_currencies');
        if (!$result) {
            throw new \Exception('Unable to get main currency');
        }

        return $result->row()->id;
    }


    public
    function addParentsCatsAsAdditional()
    {
        $products = \SProductsQuery::create()->find();

        $existingData = $this->getProductCategories();

        $insertData = [];
        foreach ($products as $p) {
            $mainCategory = $p->getMainCategory();
            $parentCategories = $mainCategory->buildCategoryPath();
            foreach ($parentCategories as $c) {
                $insertDataRow = [
                    'product_id' => $p->getId(),
                    'category_id' => $c->getId()
                ];

                if (!in_array($insertDataRow, $existingData)) {
                    $insertData[] = $insertDataRow;
                    $this->db->insert('shop_product_categories', $insertDataRow);
                }
            }
        }

        //$this->db->insert_batch('shop_product_categories', $insertData);

    }


    protected
    function getProductCategories()
    {
        $result = $this->db->get('shop_product_categories');
        if (!$result) {
            return [];
        }

        return $result->result_array();
    }


}
