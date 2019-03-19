<?php

namespace parser_upd_prods\DataUpdaters;


//use CI;
use SProperties;
use SPropertiesQuery;
use SPropertyValueQuery;
use SPropertyValue;
use Products\ProductApi;

class Property
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
    /**
     * @var \CI_DB_active_record
     */
    protected $db;

    /**
     * @var array
     */
    private static $existingProductsCategories;

    public function __construct()
    {
//        \CI::$APP->db = &get_instance();
        \CI::$APP->db = \CI::$APP->db;
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
    public static function get_save_props($rpods_full_data)
    {

        \CI::$APP->db->query('DELETE FROM  shop_product_properties_data WHERE  product_id IN (' . implode(',', array_keys($rpods_full_data)) . ')');
        if (\CI::$APP->db->_error_message() && \CI::$APP->db->_error_message() != null && \CI::$APP->db->_error_message() != '') {
            dump(\CI::$APP->db->last_query());
            dd(\CI::$APP->db->_error_message());
        }
        \CI::$APP->db->query('DELETE FROM  shop_product_properties_data WHERE  value_id IS NULL');


        $all_exist_props = \CI::$APP->db->select('id, csv_name')->get('shop_product_properties');
        if ($all_exist_props && $all_exist_props != null) {
            $all_exist_props = $all_exist_props->result_array();
            foreach ($all_exist_props as $prop_id => $csv) {
                $exist_props[$csv['csv_name']] = $csv['id'];
                unset($prop_id, $csv);
            }
        }

        unset($all_exist_props);
        $props_count = \CI::$APP->db->query('SELECT id FROM shop_product_properties ORDER BY id DESC LIMIT 1')->result()['0']->id;

        $all_prop_cats = \CI::$APP->db->get('shop_product_properties_categories');
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
            \CI::$APP->db->insert_batch('shop_product_properties_categories', $new_prop_catsN);
            if (\CI::$APP->db->_error_message() && \CI::$APP->db->_error_message() != null && \CI::$APP->db->_error_message() != '') {
                dump('shop_product_properties_categoriesN');
                dd(\CI::$APP->db->_error_message());
            }
            \CI::$APP->db->insert_batch('shop_product_properties_categories', $new_prop_cats);
            if (\CI::$APP->db->_error_message() && \CI::$APP->db->_error_message() != null && \CI::$APP->db->_error_message() != '') {
                dump('shop_product_properties_categories');
                dd(\CI::$APP->db->_error_message());
            }
            \CI::$APP->db->insert_batch('shop_product_properties', $data_cr_prop);
            \CI::$APP->db->insert_batch('shop_product_properties_i18n', $data_cr_prop_18n);
            if (\CI::$APP->db->_error_message() && \CI::$APP->db->_error_message() != null && \CI::$APP->db->_error_message() != '') {
                dump('xxxx');
                dd(\CI::$APP->db->_error_message());
            }
        }

        unset($exist_props);
//        exit();
        return [$prod_property_new, $prod_property_ex];
    }

    public function fill_product_props($prod_property_new, $prod_property_ex)
    {
        $props_data_count = \CI::$APP->db->query('SELECT id FROM shop_product_properties_data ORDER BY id DESC LIMIT 1')->result()['0']->id;
        $props_vals_count = \CI::$APP->db->query('SELECT id FROM shop_product_property_value_i18n ORDER BY id DESC LIMIT 1')->result()['0']->id;

        $all_prop_vals = \CI::$APP->db
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

            $all_exist_props = \CI::$APP->db->select('id, csv_name')->get('shop_product_properties');
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

                    if (count($exist_prop_vals) > 0) {
                        foreach ($exist_prop_vals as $key_val => $val) {
                            if ($val['property_id'] == $exist_props[$csv] && translit_url(trim($val['value'])) == translit_url(trim($prop['1']))) {
                                $prod_prop_data[] = [
                                    'id' => $props_data_count,
                                    'property_id' => $exist_props[$csv],
                                    'product_id' => $prod_id,
                                    'value_id' => $val['value_id']
                                ];
                            } else {//creating new prop vals
                                $new_val[$exist_props[$csv]][translit_url(trim($prop['1']))] = [
                                    'id' => $props_vals_count,
                                    'property_id' => $exist_props[$csv]
                                ];
                                $new_val_i18[$exist_props[$csv]][translit_url(trim($prop['1']))] = [
                                    'id' => $props_vals_count,
                                    'locale' => 'ru',
                                    'value' => trim($prop['1'])
                                ];

                                $prod_prop_data[] = [
                                    'id' => $props_data_count,
                                    'property_id' => $exist_props[$csv],
                                    'product_id' => $prod_id,
                                    'value_id' => $props_vals_count
                                ];
                            }

                            unset($key_val, $val);
                        }
                    } else {
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

                    }
                    unset($csv, $prop);
                }
                unset($prod_id, $props);
            }
//            \CI::$APP->db->insert_batch('shop_product_properties_data', $prod_prop_data);
            if (\CI::$APP->db->_error_message() && \CI::$APP->db->_error_message() != null && \CI::$APP->db->_error_message() != '') {
                dump('111');
                dd(\CI::$APP->db->_error_message());
            }

            foreach ($new_val as $key_val => $val_val) {
//                \CI::$APP->db->insert_batch('shop_product_properties_data', $prod_prop_data[$key_val]);
                \CI::$APP->db->insert_batch('shop_product_property_value', $val_val);
                \CI::$APP->db->insert_batch('shop_product_property_value_i18n', $new_val_i18[$key_val]);
                if (\CI::$APP->db->_error_message() && \CI::$APP->db->_error_message() != null && \CI::$APP->db->_error_message() != '') {
                    dump('222');
                    dd(\CI::$APP->db->_error_message());
                }
                unset($key_val, $val_val);
            }
            $some_count = 0;
            foreach ($prod_prop_data as $som_k => $som_pr_prop_data) {
                $some_count++;
                $created_pr_vals = \CI::$APP->db
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
            \CI::$APP->db->insert_batch('shop_product_properties_data', $prod_prop_data_val_to_inser);
        }

        unset($exist_props);
        unset($exist_prop_vals);

    }

    public function fill_prod_props_exist_vals_exist($prod_property_ex)
    {

        $props_data_count = \CI::$APP->db->query('SELECT id FROM shop_product_properties_data ORDER BY id DESC LIMIT 1')->result()['0']->id;
        $props_vals_count = \CI::$APP->db->query('SELECT id FROM shop_product_property_value_i18n ORDER BY id DESC LIMIT 1')->result()['0']->id;

        $all_prop_vals = \CI::$APP->db
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
        $all_exist_props = \CI::$APP->db->select('id, csv_name')->get('shop_product_properties');
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

            \CI::$APP->db->insert_batch('shop_product_properties_data', $prod_prop_data_prod_id);
            if (\CI::$APP->db->_error_message() && \CI::$APP->db->_error_message() != null && \CI::$APP->db->_error_message() != '') {
                dump('ggggg');
                dd(\CI::$APP->db->_error_message());
            }
//            \CI::$APP->db->insert_batch('shop_product_properties_data', $prod_prop_data);
//            if(\CI::$APP->db->_error_message() && \CI::$APP->db->_error_message() !=null && \CI::$APP->db->_error_message() !='' ){
//                dump('3333');
//                dd(\CI::$APP->db->_error_message());
//            }

//            foreach ($new_val as $key_val => $val_val) {
//                \CI::$APP->db->insert_batch('shop_product_property_value', $val_val);
//                \CI::$APP->db->insert_batch('shop_product_property_value_i18n', $new_val_i18[$key_val]);
//                if(\CI::$APP->db->_error_message() && \CI::$APP->db->_error_message() !=null && \CI::$APP->db->_error_message() !='' ){
//                    dump('444');
//                    dd(\CI::$APP->db->_error_message());
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


}
