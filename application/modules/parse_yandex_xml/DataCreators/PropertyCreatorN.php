<?php

namespace parse_yandex_xml\DataCreators;
(defined('BASEPATH')) OR exit('No direct script access allowed');

//use CI;
use SProperties;
use SPropertiesQuery;
use SPropertyValueQuery;
use SPropertyValue;
use Products\ProductApi;


class PropertyCreatorN
{

    /**
     * @var array [name => [array of values...]...]
     */
    private static $properties = [];

    /**
     * @var Property[]
     */
    public static $instances = [];

    /**
     * @var array [product_id => category_id]
     */
    public static $propertyCategories = [];

    private $id;

    private $name;

    private $value;
    private $db;

    /**
     * @var array
     */
    private static $existingProductsCategories;

    public function __construct()
    {
        $this->db = &get_instance();

//        self::loadExistingProductCategories();
//
//        // adding property for saving
//        if (!isset(self::$properties[$name])) {
//            self::$properties[$name] = [];
//        }
//
//        if (!in_array($value, self::$properties[$name])) {
//            self::$properties[$name][] = $value;
//        }
//
//        // adding category in which property must be present
//        if (!isset(self::$propertyCategories[$name])) {
//            self::$propertyCategories[$name] = [];
//        }
//
//        if (!in_array($categoryId, self::$propertyCategories[$name]) && !is_null($categoryId)) {
//            self::$propertyCategories[$name][] = (int)$categoryId;
//        }
//
//        $this->name = $name;
//        $this->value = $value;
//
//        self::$instances[$name] = $this;
    }

    /**
     * @return array
     */
    public static function get_save_props($one_rpod_full_data)
    {

        $ci =& get_instance();
        $all_exist_props = $ci->db->select('id, csv_name')->get('shop_product_properties');

        if ($all_exist_props && $all_exist_props != null) {
            $all_exist_props = $all_exist_props->result_array();
            foreach ($all_exist_props as $prop_id => $csv) {
                $exist_props[$csv['csv_name']] = $csv['id'];
                unset($prop_id, $csv);
            }
        }
        foreach ($one_rpod_full_data as $prod_id => $pr_data) {
            foreach ($pr_data['params'] as $prop_name => $prop_val) {
                $props_names[translit_url(trim($prop_name))] = $prop_name;
                unset($prop_name, $prop_val);
            }
            unset($prod_id, $pr_data);
        }


        $props_count = $ci->db->query('SELECT id FROM shop_product_properties ORDER BY id DESC LIMIT 1')->result()['0']->id;

        foreach ($props_names as $prop_id => $prop_names) {

            $csv = translit_url(trim($prop_names));
            if (key_exists($csv, $exist_props)) {
                $prod_property[$prop_id][$exist_props[$csv]] = $exist_props[$csv]; //ИД свойства /= [имя и значения свойства]
//
            } else {
                $props_count++;
                $data_cr_prop[$csv] = [
                    'id' => $props_count,
                    'csv_name' => $csv,
                    'active' => 1,
                    'show_in_filter' => 1,
                    'show_on_site' => 1,
                    'multiple' => 0,
                    'main_property' => 1,
                    'external_id' => null
                ];
                $data_cr_prop_18n[$csv] = [
                    'id' => $props_count,
                    'name' => trim($prop_names),
                    'locale' => 'ru'
                ];

                $prod_property[$prop_id][$props_count] = $props_count;//CSV свойства / имя и значения свойства
            }
            unset($prop_id, $prop_names);
        }

        if ($props_count > 0) {
            $ci->db->insert_batch('shop_product_properties', $data_cr_prop);
            if ($ci->db->_error_message() && \CI::$APP->db->_error_message() != null && \CI::$APP->db->_error_message() != '') {
                dump($ci->db->last_query());
                dd($ci->db->_error_message());
            }
            $ci->db->insert_batch('shop_product_properties_i18n', $data_cr_prop_18n);
            if ($ci->db->_error_message() && \CI::$APP->db->_error_message() != null && \CI::$APP->db->_error_message() != '') {
                dump($ci->db->last_query());
                dd($ci->db->_error_message());
            }
        }

        unset($exist_props);

        $prods_full_datas_prop =  self::connect_props_bd_to_yml($prod_property, $one_rpod_full_data);
        self::fill_prod_props_data_cr_vals($prods_full_datas_prop);

        self::repairProductPropertiesCategories($prods_full_datas_prop);

        return $prod_property;

    }


    public function connect_props_bd_to_yml($prod_property, $prod_full_data)
    {
        foreach ($prod_full_data as $prod_id => $data) {
            foreach ($data['params'] as $csv => $csv_prop) {
                $prods_full_datas[$prod_id]['properties'][key($prod_property[translit_url(trim($csv))])]=$csv_prop;
                $prods_full_datas[$prod_id]['category_id']=$data['category_id'];
                unset($csv, $csv_prop);
            }
            unset($prod_id, $data);
        }
        return $prods_full_datas;
    }

    private function fill_prod_props_data_cr_vals($prod_data_to_prop)
    {


        if (count($prod_data_to_prop) > 0) {
            foreach ($prod_data_to_prop as $prod_id => $props) {

                foreach ($props['properties'] as $prop_id => $prop) {

                    if ($prop != '') {


                        $property = SPropertiesQuery::create()->findOneById($prop_id);

                        if(ctype_digit(str_split($prop)['0'])){
                            $prop_temp = strripos($prop, '-');
                            $prop_temp2 = strripos($prop, 'x');
                            if ($prop_temp === false && $prop_temp2 === false && strripos($prop, '°') === false) {
                                $prop = (int)$prop;
                            }elseif ($prop_temp2 !== false) {
                                $prop = trim($prop);
                                $prop = str_replace(' ', '',$prop);
                            }
                            elseif(strripos($prop, '°') !== false  || strripos($prop, '°C') !== false ) {
                                $prop = str_replace('~', '-',str_replace(' ', '',trim($prop)));
                                $prop = str_replace('0°-', '0-',$prop);
                            }else{
                                $prop = trim($prop);
                            }

                        }else{
                            $prop = trim($prop);
                        }
                        $propertyValue = SPropertyValueQuery::create()
                            ->joinWithI18n('ru')
                            ->useI18nQuery('ru')
                            ->filterByValue($prop)
                            ->endUse()
                            ->findOneByPropertyId($prop_id);

                        if (!$property) {

                            continue;
                        } else {
                            $prop_cat = \ShopProductPropertiesCategoriesQuery::create()->filterByCategoryId($props['category_id'])->findByPropertyId($prop_id);
                            if(empty($prop_cat->getData())){
                                $prop_cat_Data=[
                                    'property_id' => $prop_id,
                                    'category_id' => $props['category_id']
                                ];
                                \CI::$APP->db->insert('shop_product_properties_categories', $prop_cat_Data);

                            }
                            if ($propertyValue !== null) {
                                $pr_val_id = $propertyValue->getId();
                            } else {

                                $propertyValue = new SPropertyValue();
                                $propertyValue->setLocale('ru')
                                    ->setPropertyId($prop_id)
//                                    ->setValue(trim($prop))
                                    ->setValue($prop)
                                    ->save();
                                $pr_val_id = $propertyValue->getId();
                            }
                            ProductApi::getInstance()->setProductPropertyValue($prod_id, $prop_id, $pr_val_id, 'ru');
                        }
                    }else{

                    }

                }
            }
        }
    }

    private function repairProductPropertiesCategories($prod_data_to_prop)
    {
        $db = \CI::$APP->db;
        $productCategoriesRows = $db->select(['property_id', 'category_id'])->get('shop_product_properties_categories')->result_array();



        /** @var array $productCategories [product id => category id,...] */
        $productCategories = [];
        foreach ($productCategoriesRows as $row) {
            $productCategories[$row['id']] = $row['category_id'];
        }

        $insertedControl = [];

        foreach ($propertiesProductsRows as $row) {
            $data = [
                'property_id' => $row['property_id'],
                'category_id' => $productCategories[$row['product_id']]
            ];

            if (in_array($data, $insertedControl)) {
                continue;
            }

            //$db->insert('shop_product_properties_categories', $data);

            $insertedControl[] = $data;
        }


    }













    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }


    public static function saveProperties()
    {
        $db = \CI::$APP->db;
        foreach (self::$properties as $name => $values) {
            $csvName = self::getCsvName($name);
//            $result = $db->where('csv_name', $csvName)->get('shop_product_properties');
//            if (!$result) {
//                continue;
//            }
//            $propertyId = $result->row()->id;
//            if (!empty($propertyId)) {
//                self::deleteOneProperty($propertyId);
//            }

            $existingCatIds = isset(self::$existingProductsCategories[$name]) ? self::$existingProductsCategories[$name] : [];

            $model = self::saveOneProperty([
                "Name" => $name,
                "CsvName" => $csvName,
                "Active" => "1",
                "Description" => "",
                "Data" => implode(PHP_EOL, $values)
            ]);

            foreach (self::$propertyCategories[$name] as $catId) {
                $db
                    ->where('property_id', $model->getId())
                    ->where('category_id', $catId)
                    ->limit(1)
                    ->delete('shop_product_properties_categories');

                $db->insert('shop_product_properties_categories', [
                    'property_id' => $model->getId(),
                    'category_id' => $catId,
                ]);
            }

            self::$instances[$name]->id = $model->getId();
        }
    }

    private function bindPropertyToCategory($propertyId, $categoryId)
    {

    }

    private static function saveOneProperty(array $data)
    {
        $model = self::firstOrNew($data['CsvName']);
        $model->fromArray($data);
        $model->save();
        return $model;
    }

    private static function firstOrNew($csvName)
    {
        $model = SPropertiesQuery::create()->filterByCsvName($csvName)->findOne();
        return $model ?: new SProperties;
    }

    private static function deleteOneProperty($propertyId)
    {
        $db = \CI::$APP->db;

        $db->where('id', $propertyId)->delete('shop_product_properties');
        $db->where('id', $propertyId)->delete('shop_product_properties_i18n');
        $db->where('property_id', $propertyId)->delete('shop_product_properties_categories');
        $db->where('property_id', $propertyId)->delete('shop_product_properties_data');
    }

    private static function getCsvName($name)
    {
        $name = translit($name);
        $name = strtolower($name);
        $name = preg_replace('/[^a-z]+/', '', $name);
        return $name;
    }

    private static function loadExistingProductCategories()
    {
        if (is_array(self::$existingProductsCategories)) {
            return;
        }

        self::$existingProductsCategories = [];

        $result = \CI::$APP->db->get('shop_product_properties_categories');

        if (!$result) {
            return;
        }

        $result = $result->result_array();

        $names = self::getPropertiesNames();

        foreach ($result as $row) {
            self::$existingProductsCategories[$names[$row['property_id']]][] = $row['category_id'];
        }
    }


    private static function getPropertiesNames()
    {
        $result = \CI::$APP->db->get('shop_product_properties');

        if (!$result) {
            return;
        }

        $names = [];

        $result = $result->result_array();

        foreach ($result as $row) {
            $names[$row['id']] = $row['name'];
        }

        return $names;
    }


}
