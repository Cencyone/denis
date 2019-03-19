<?php

namespace parser_upd_prods\DataUpdaters;

use Base\SCurrenciesQuery;
use CMSFactory\ModuleSettings;
use MediaManager\Image;
use parser_upd_prods\DataUpdaters\Property;
use parser_upd_prods\DataUpdaters\Property2;


class ProductsDataUpdater
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

    /**
     * @param IDataGetter $dataGetter
     */
    public function __construct($dataGetter)
    {
        $this->dataGetter = $dataGetter;
        $this->db = \CI::$APP->db;
//        $this->images = new Images(PUBPATH . 'uploads/product_images.json', PUBPATH . 'uploads/shop/products/origin/');
        $this->loadExistingProducts();
//        $this->loadExistingVariants();
//        $this->loadImages();
        $this->def_cur = SCurrenciesQuery::create()->findByMain('1')->getFirst()->id;
        $this->getExistingCategories();
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
            ->select('id, category_id')
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
            ->select('id, id_morion')
            ->where('id_morion is not NULL')
            ->get('shop_product_variants');

        if ($exist_vars && $exist_vars != null) {
            $all_exist_vars = $exist_vars->result_array();
            foreach ($all_exist_vars as $vars_id => $data_v) {
                $this->existingProductVarData[$data_v['id_morion']] = $data_v['id'];
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
    public function updateData()
    {
        if (!$this->dataGetter['all_offers'] || $this->dataGetter['all_offers'] == '' || $this->dataGetter['all_offers'] == null) {
            return 'empty_data_to_update';
        }
        foreach ($this->dataGetter['all_offers'] as $prods) {
            $pr_with_vars[$prods['id']] = $prods;
        }

        $data_prodParse = $pr_with_vars;
//        $brands = new BrandsDataUpdater();
//        $all_brands = $brands->getAllBrands();
//        $categoryIds = $this->existingCategoriesData;

        $count_for_ext_prods = 0;
        foreach ($data_prodParse as $pr_key => $product_array) {

            if (!$this->productExists($product_array['id'])) { // creating

            } else { // updating
                foreach ($product_array['pictures'] as $key_pic => $val_pic) {
//                    $val_pic = str_replace('http://', 'https://', $val_pic);
                    if ($key_pic == 0) {
                        $pic_data[$product_array['id']] = file_get_contents($val_pic);

                        // if ($pic_data == '' || $pic_data === null || $pic_data == 0) {

                        //     $curl = curl_init();
                        //     curl_setopt($curl, CURLOPT_URL, $val_pic);
                        //     curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                        //     curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0');
                        //     $out = curl_exec($curl);

                        //     $pic_data = file_get_contents($out);
                        // }

//                        if ($pic_data) {
                        $picture[$product_array['id']] = $product_array['id'] . pathinfo($val_pic, PATHINFO_BASENAME);
                        file_put_contents('./uploads/shop/products/origin/' . $product_array['id'] . pathinfo($val_pic, PATHINFO_BASENAME), $pic_data[$product_array['id']]);
                        $main_imagec[$product_array['id'] . '-0' ] = $picture[$product_array['id']];
                        //     }
//                        }
                    } else {

                        $pic_datas[$product_array['id']] = file_get_contents($val_pic);
                        // if ($pic_data == '' || $pic_data === null || $pic_data == 0) {
                        //     $curl = curl_init();
                        //     curl_setopt($curl, CURLOPT_URL, $val_pic);
                        //     curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                        //     curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0');
                        //     $out = curl_exec($curl);

                        //     $pic_data = file_get_contents($out);
                        // }
//                        if ($pic_data) {
                        file_put_contents('./uploads/shop/products/origin/additional/' . $product_array['id'] . pathinfo($val_pic, PATHINFO_BASENAME), $pic_datas[$product_array['id']]);
                        $data_new_prod_image[] = ['product_id' => $product_array['id'],
                            'image_name' => $product_array['id'] . pathinfo($val_pic, PATHINFO_BASENAME),
                            'position' => $key_pic + 1];
                        $data_new_prod_imageArr[$product_array['id']]=$product_array['id'];
                        $add_images[] = pathinfo($val_pic, PATHINFO_BASENAME);
//                        }
                        // }
                    }
                    unset($key_pic, $val_pic);
                }


                $count_for_ext_prods++;
                $data_upd_prod[$this->existingProductData[$product_array['id']]] = [
                    'id' => $this->existingProductData[$product_array['id']],
                    'active' => 1,
                    'archive' => 0,
                    'updated' => time(),
                ];
                $data_upd_prod_i18[$this->existingProductData[$product_array['id']]] = [
                    'id' => $this->existingProductData[$product_array['id']],
                    'description' => $product_array['description'],
                ];

                $data_upd_var[$this->existingProductData[$product_array['id']]] = [
//                    'id' => $prod_vars_count,
                    'product_id' => $this->existingProductData[$product_array['id']],
                    'mainImage' => $main_imagec[$product_array['id'] . '-0'],
                ];

                if (count($product_array['params']) > 0) {
                    $prod_props_data_to_update[$this->existingProductData[$product_array['id']]] = $product_array['params'];
                    $one_rpod_full_data[$this->existingProductData[$product_array['id']]] = [
                        'params' => $product_array['params'],
                        'category_id' => $this->existingProductCatData[$product_array['id']]
                    ];
                }

            }
            unset($pr_key, $product_array);
        }

        if($count_for_ext_prods > 0){
            $active = 'active = (CASE id';
            $archive = 'archive = (CASE id';
            $updated = 'updated = (CASE id';
            $ids = '';
            foreach ($data_upd_prod as $key => $val) {
                $active .= " WHEN '" . $val['id'] . "' THEN '" . $val['active'] . "' ";
                $archive .= " WHEN '" . $val['id'] . "' THEN '" . $val['archive'] . "' ";
                $updated .= " WHEN '" . $val['id'] . "' THEN '" . $val['updated'] . "' ";
                $ids .= '"' . $val['id'] . '",';
                unset($key, $val);
            }
            $active .= 'END) ,';
            $archive .= 'END) ,';
            $updated .= 'END) ';
            $ids = rtrim($ids, ',');
            $z = "UPDATE shop_products SET " . $active . ' ' . $archive . ' ' . $updated . " WHERE id IN (" . $ids . ")";
            $this->db->query($z);
            if ($this->db->_error_message() && $this->db->_error_message() != null && $this->db->_error_message() != '') {
                dump('shop_productUPD');
                dd($this->db->_error_message());
            }

            if (count($data_upd_prod_i18) > 0) {

                $description = 'full_description = (CASE id';
                $ids_var18 = '';

                foreach ($data_upd_prod_i18 as $keys18=>$vals18) {
                    $description .= " WHEN '" . $vals18['id'] . "' THEN '" . $vals18['description'] . "' ";
                    $ids_var18 .= '"' . $vals18['id'] . '",';
                    unset($keys18, $vals18);
                }
                $description .= 'END) ';
                $ids_var18 = rtrim($ids_var18, ',');
                $zz18 = "UPDATE shop_products_i18n SET " . $description . " WHERE id IN (" . $ids_var18 . ")";
                $this->db->query($zz18);

                if ($this->db->_error_message() && $this->db->_error_message() != null && $this->db->_error_message() != '') {
                    dump('shop_products_i18nUPD');
                    dd($this->db->_error_message());
                }
            }

            if (count($data_upd_var) > 0) {

                $mainImage = 'mainImage = (CASE product_id';
                $ids_var = '';

                foreach ($data_upd_var as $keysV => $valsV) {
                    $mainImage .= " WHEN '" . $valsV['product_id'] . "' THEN '" . $valsV['mainImage'] . "' ";
                    $ids_var .= '"' . $valsV['product_id'] . '",';
                    unset($keysV, $valsV);
                }
                $mainImage .= 'END) ';
                $ids_var = rtrim($ids_var, ',');
                $zz = "UPDATE shop_product_variants SET " . $mainImage . " WHERE product_id IN (" . $ids_var . ")";
                $this->db->query($zz);
                if ($this->db->_error_message() && $this->db->_error_message() != null && $this->db->_error_message() != '') {
                    dump('shop_product_variantsUPD');
                    dd($this->db->_error_message());
                }
            }
            $this->db->where_in('product_id', $data_new_prod_imageArr)->delete('shop_product_images');

            $this->db->insert_batch('shop_product_images', $data_new_prod_image);
            if ($this->db->_error_message() && $this->db->_error_message() != null && $this->db->_error_message() != '') {
                dump('CRshop_product_images');
                dd($this->db->_error_message());
            }


            foreach($data_new_prod_image as $key=>$image){
                Image::create()
                    ->resizeByIdAdditional($key, TRUE);
                unset($key, $image);
            }

//            $props = new Property();
//
//            $properties = $props->get_save_props($one_rpod_full_data);
//
//            list($prod_property_new, $prod_property_ex) = $properties;
//            $props->fill_product_props($prod_property_new, $prod_property_ex);
//
//            $props->fill_prod_props_exist_vals_not_ext($prod_property_ex);
//            $props->fill_prod_props_exist_vals_exist($prod_property_ex);
//
//            unset($count_for_ext_prods);
//            unset($data_upd_cat_18n);
//            \CI::$APP->db->query('DELETE FROM  shop_product_properties_data WHERE  value_id IS NULL');
//            if (\CI::$APP->db->_error_message() && \CI::$APP->db->_error_message() != null && \CI::$APP->db->_error_message() != '') {
//                dump(\CI::$APP->db->last_query());
//                dd(\CI::$APP->db->_error_message());
//            }


            $props = new PropertyN();
            $properties = $props->get_save_props($one_rpod_full_data);

            exit('success');


        }

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
