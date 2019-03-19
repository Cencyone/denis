<?php

namespace parse_yandex_xml\DataCreators;

use Category\CategoryApi;
use CI;
use CMSFactory\ModuleSettings;

//use ug_contrakt\src\Log\Log;

class CategoriesDataCreator
{


    /**
     * @var IDataGetter
     */
    protected $dataGetter;

    /**
     * @var \CI_DB_active_record
     */
    protected $db;


    /**
     * @var CategoryApi
     */
    protected $categoryApi;

    /**
     * @var array
     */
    protected $existingCategoriesData = [];

    /**
     * @var string
     */
    protected $locale = "ru";

    /**
     * @var array
     */
    protected $categoryData = [];

    /**
     * @param IDataGetter $dataGetter
     */
    public function __construct($dataCatsUg)
    {
        $this->dataGetter = $dataCatsUg;
        $this->db = CI::$APP->db;
        CI::$APP->load->helper('translit');

        $this->loadExistingCategories();
    }

    private function loadExistingCategories()
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

    /**
     * @param int $id
     * @return bool
     */
    public function categoryExists($id)
    {
        return isset($this->existingCategoriesData[$id]);
    }

    /**
     * @return mixed
     */
    public function updateData()
    {
//        $this->db->truncate('shop_category');
//        $this->db->truncate('shop_category_i18n');
//        $this->db->query("DELETE FROM `route` WHERE `type` ='product'");
//        $this->db->query("DELETE FROM `route` WHERE `type` ='shop_category'");

        $settings = ModuleSettings::ofModule('parse_yandex_xml')->get()['main_set'];

        $provider = !empty($settings['provider_name_to_current_parsing']) &&
        $settings['provider_name_to_current_parsing'] != null &&
        $settings['provider_name_to_current_parsing'] != '' ? '-'.$settings['provider_name_to_current_parsing'] : '';


        $data_cats_from_ug = $this->dataGetter;
        $cats_count = $this->db->query('SELECT id FROM shop_category ORDER BY id DESC LIMIT 1')->result()['0']->id;

        foreach ($data_cats_from_ug as $kat_key => $item_item) {
//            dump($kat_key, $item_item);

            if (!$this->categoryExists($item_item['id'].$provider)) {
                $cats_count++;
                $data_cr_cat[] = [
                    'id' => $cats_count,
                    'active' => 1,
                    'show_in_menu' => 1,
                    'external_id' => $item_item['id'].$provider,

                    'created' => time(),
                    'updated' => time(),
                    'route_id' => '',
                ];
                $data_cr_cat_18n[] = [
                    'id' => $cats_count,
                    'name' => (string)$item_item['name'],
                    'locale' => 'ru'
                ];



                $this_result_for_fix['categories'][$cats_count] = [
                    'id' => $cats_count,
                    'name' => (string)$item_item['name'],
                    'url' => translit_url((string)$item_item['name']).'-'.$cats_count,
                    'external_id' => $item_item['id'].$provider,
                    'parent_id_exter' => !$item_item['parent_id']
                    || $item_item['parent_id'] == '' || $item_item['parent_id'] == null
                        ? 0 : $item_item['parent_id'].$provider
                ];

            } else {

//                $this->updateCategory($data[$i]);
            }
            unset($kat_key, $item_item);
        }

        unset($data_cats_from_ug);
        $this->db->insert_batch('shop_category', $data_cr_cat);
        if ($this->db->_error_message() && $this->db->_error_message() != null && $this->db->_error_message() != '') {
            dump('shop_category');
//            Log::changeLast(Log::TYPE_MAIN_SYNC, Log::STATUS_ERROR_CATS);
            dd($this->db->_error_message());
        }
        $this->db->insert_batch('shop_category_i18n', $data_cr_cat_18n);
        if ($this->db->_error_message() && $this->db->_error_message() != null && $this->db->_error_message() != '') {
            dump('shop_category_i18n');
//            Log::changeLast(Log::TYPE_MAIN_SYNC, Log::STATUS_ERROR_CATS);
            dd($this->db->_error_message());
        }
        $count = count($data_cr_cat);
        unset($data_cr_cat);
        unset($data_cr_cat_18n);

//        $this->runPathsFix($this_result_for_fix['categories']);
        $this->runPathsFix($this_result_for_fix['categories'], $settings);

//        Log::changeLast(Log::TYPE_MAIN_SYNC, Log::STATUS_SUCCESS_CATS);

        return $count;

    }

    protected function runPathsFix($this_result_for_fix, $settings)
    {
        $categories = $this_result_for_fix;


        unset($this_result_for_fix);
        foreach ($categories as $id => $category) {
            $parent_id_prev[$id] = $category['parent_id_exter'] ? $this->db
                ->where('external_id', $category['parent_id_exter'])
                ->get('shop_category')
                ->row()->id : 0;

            $parent_id[$id] = $parent_id_prev[$id];

            $par_id[$id] = !$parent_id_prev[$id]
            || $parent_id_prev[$id] == '' || $parent_id_prev[$id] == null
                ? 0 : $parent_id_prev[$id];

            $parent_id[$id] = $parent_id[$id]==0 && $settings['CategoryIdOasis']!=0
                ?$settings['CategoryIdOasis']:$parent_id[$id];


            $categories[$id]['parent_id'] = $parent_id[$id];
        }
        if (count($categories) > 0) {
            $categories2 = $this->getPathsAndParents($categories);
            unset($categories);
            $parent_id = 'parent_id = (CASE id';
            $route_id = 'route_id = (CASE id';
            $full_path_ids = 'full_path_ids = (CASE id';
            $ids = '';

            foreach ($categories2 as $key => $val) {

                $parent_id .= " WHEN '" . $val['id'] . "' THEN  '" . $val['parent_id'] . "' ";
                $route_id .= " WHEN '" . $val['id'] . "' THEN '" . $val['route_id'] . "' ";
                $full_path_ids .= " WHEN '" . $val['id'] . "' THEN '" . $val['full_path_ids'] . "' ";
                $ids .= '"' . $val['id'] . '",';

                unset($key, $val);
            }
            unset($categories2);
            $parent_id .= 'END),';
            $route_id .= 'END), ';
            $full_path_ids .= 'END) ';
            $ids = rtrim($ids, ',');
            $z = "UPDATE shop_category SET " . $parent_id . ' ' . $route_id . ' ' . $full_path_ids . " WHERE id IN (" . $ids . ")";
            $this->db->query($z);
            if ($this->db->_error_message() && $this->db->_error_message() != null && $this->db->_error_message() != '') {
                dump('shop_category');
//                Log::changeLast(Log::TYPE_MAIN_SYNC, Log::STATUS_ERROR_CATS);
                dd($this->db->_error_message());
            }
        }


    }

    private function getPathsAndParents($categories11)
    {
        $categories = $categories11;

        foreach ($categories as $categoryId0 => $categoryData0) {
            $categories1[$categoryData0['id']] = $categoryData0;
            unset($categoryId0 , $categoryData0);
        }
        ksort($categories1);

        // creating id-paths and url-paths of each category
        foreach ($categories1 as $categoryId => $categoryData) {
            $currentPathIds = [];

            $neededCid[$categoryId] = $categoryData['parent_id'];

            while ($neededCid[$categoryId] != 0) {
                $currentPathIds[$categoryId][] = (int)$neededCid[$categoryId];
                $neededCid[$categoryId] = (int)$categories[$neededCid[$categoryId]]['parent_id'];
            }
            $ext_rote[$categoryId] = $this->db
                ->where('entity_id', $categoryData['id'])
                ->where('type', 'shop_category')
                ->where('url', $categoryData['url'])
                ->get('route')->result_array();
            $ext_roteU[$categoryId] = $this->db
                ->where('url', $categoryData['url'])
                ->get('route')->result_array();


            if (!$ext_rote[$categoryId] || count($ext_rote[$categoryId]) == 0 || $ext_rote[$categoryId] == null
            ) {
                $parentUrl[$categoryId] = $this->db
                    ->where('entity_id', $categoryData['parent_id'])
                    ->where('type', 'shop_category')
                    ->get('route')->result_array();


                $parentUrl1[$categoryId] = !empty($parentUrl[$categoryId]) ? $parentUrl[$categoryId]['0']['parent_url'].'/'.$parentUrl[$categoryId]['0']['url'] : '';

                $route[$categoryId] = [
//                    'parent_url' => $parentUrl1[$categoryId],
                    'url' => $categoryData['url'],
                    'entity_id' => $categoryData['id'],
                    'type' => 'shop_category',
                ];

                $this->db->insert('route', $route[$categoryId]);
                if ($this->db->_error_message() && $this->db->_error_message() != null && $this->db->_error_message() != '') {
                    dump('route');
//                Log::changeLast(Log::TYPE_MAIN_SYNC, Log::STATUS_ERROR_CATS);
                    dd($this->db->_error_message());
                }
                $newRouteId[$categoryId] = $this->db->insert_id();
//                $this->db->update('shop_category', ['route_id' => $newRouteId], ['id' => $categoryData['id']]);
                $categories[$categoryId]['route_id'] = $newRouteId[$categoryId];
            } else {
                $parentUrl[$categoryId] = $this->db
                    ->where('entity_id', $categoryData['parent_id'])
                    ->where('type', 'shop_category')
                    ->get('route')->result_array();

                $parentUrl1[$categoryId] = !empty($parentUrl[$categoryId]) ?$parentUrl[$categoryId]['0']['parent_url'].'/'. $parentUrl[$categoryId]['0']['url'] : '';
                $route[$categoryId] = [
//                    'parent_url' => $parentUrl1[$categoryId],
                    'url' => $categoryData['url'] . '-' . $categoryData['id'],
                    'entity_id' => $categoryData['id'],
                    'type' => 'shop_category',
                ];

                $this->db->update('route', $route[$categoryId], ['id' => $ext_rote[$categoryId]['0']['id']]);
                if ($this->db->_error_message() && $this->db->_error_message() != null && $this->db->_error_message() != '') {
                    dump('route2');
//                Log::changeLast(Log::TYPE_MAIN_SYNC, Log::STATUS_ERROR_CATS);
                    dd($this->db->_error_message());
                }
//                $this->db->update('shop_category', ['route_id' => $ext_rote['0']['id']], ['id' => $categoryData['id']]);
                $categories[$categoryId]['route_id'] = $ext_rote[$categoryId]['0']['id'];
            }
            array_reverse($currentPathIds[$categoryId]);
            if($currentPathIds[$categoryId] == null){
                $currentPathIds[$categoryId] = [0];
            }
            $categories[$categoryId]['full_path_ids'] = serialize(array_reverse($currentPathIds[$categoryId]));
            $this->create_full_rote_urls($categoryId, $currentPathIds[$categoryId]);
            unset($categoryId, $categoryData);
        }
        unset($categories1);
        return $categories;
    }

    public function create_full_rote_urls($categoryId, $currentPathIds)
    {
        unset($full_rote_urls);

        if (!empty($currentPathIds) && $currentPathIds !=null) {
            $currentPathIds[max($currentPathIds)] = $categoryId;
            $urls = $this->db->select('url')->where('type', 'shop_category')
                ->where_in('entity_id', $currentPathIds)->order_by('entity_id')->get('route')->result_array();
//            krsort($urls);

            foreach ($urls as $key => $url_1) {
                if($key+1 == count($urls)){

                }else{
                    $full_rote_urls[] = $url_1['url'];
                }

            }

            $this->db->set('parent_url', implode('/', $full_rote_urls))->where('entity_id', $categoryId)->where('type', 'shop_category')->update('route');
            if ($this->db->_error_message() && $this->db->_error_message() != null && $this->db->_error_message() != '') {
                dump('route3');
//                Log::changeLast(Log::TYPE_MAIN_SYNC, Log::STATUS_ERROR_CATS);
                dd($this->db->_error_message());
            }
        }
    }



//    protected function runPathsFix($this_result_for_fix)
//    {
//        $categories = $this_result_for_fix;
//
//        $settings = ModuleSettings::ofModule('parse_yandex_xml')->get()['main_set'];
//        $def_shop_cat =!empty($settings['CategoryIdOasis']) &&
//        $settings['CategoryIdOasis'] != null &&
//        $settings['CategoryIdOasis'] != '' ? $settings['CategoryIdOasis'] : null;
//
//        unset($this_result_for_fix);
//        foreach ($categories as $id => $category) {
//            $parent_id_prev = $category['parent_id_exter'] ? $this->db
//                ->where('external_id', $category['parent_id_exter'])
//                ->get('shop_category')
//                ->row()->id : 0;
//
//            $parent_id = $def_shop_cat && $def_shop_cat != '' &&
//            $def_shop_cat != null ? ($parent_id_prev != 0 && $parent_id_prev != null && $parent_id_prev ? $parent_id_prev : $def_shop_cat) : $parent_id_prev;
//
//            $categories[$id]['parent_id'] = $parent_id;
//        }
//        if (count($categories) > 0) {
//            $categories2 = $this->getPathsAndParents($categories);
//            unset($categories);
//            $parent_id = 'parent_id = (CASE id';
//            $route_id = 'route_id = (CASE id';
//            $full_path_ids = 'full_path_ids = (CASE id';
//            $ids = '';
//
//            foreach ($categories2 as $key => $val) {
//
//                $parent_id .= " WHEN '" . $val['id'] . "' THEN  '" . $val['parent_id'] . "' ";
//                $route_id .= " WHEN '" . $val['id'] . "' THEN '" . $val['route_id'] . "' ";
//                $full_path_ids .= " WHEN '" . $val['id'] . "' THEN '" . $val['full_path_ids'] . "' ";
//                $ids .= '"' . $val['id'] . '",';
//
//                unset($key, $val);
//            }
//            unset($categories2);
//            $parent_id .= 'END),';
//            $route_id .= 'END), ';
//            $full_path_ids .= 'END) ';
//            $ids = rtrim($ids, ',');
//            $z = "UPDATE shop_category SET " . $parent_id . ' ' . $route_id . ' ' . $full_path_ids . " WHERE id IN (" . $ids . ")";
//            $this->db->query($z);
//            if ($this->db->_error_message() && $this->db->_error_message() != null && $this->db->_error_message() != '') {
//                dump('shop_category');
////                Log::changeLast(Log::TYPE_MAIN_SYNC, Log::STATUS_ERROR_CATS);
//                dd($this->db->_error_message());
//            }
//        }
//
//
//    }
//
//    private function getPathsAndParents($categories11)
//    {
//        $categories = $categories11;
//
//        foreach ($categories as $categoryId => $categoryData) {
//            $categories1[$categoryData['id']] = $categoryData;
//        }
//
//        // creating id-paths and url-paths of each category
//        foreach ($categories1 as $categoryId => $categoryData) {
//            $currentPathIds = [];
//
//            $neededCid = $categoryData['parent_id'];
//
//            while ($neededCid != 0) {
//                $currentPathIds[] = (int)$neededCid;
//                $neededCid = (int)$categories[$neededCid]['parent_id'];
//            }
//            $ext_rote = $this->db
//                ->where('entity_id', $categoryData['id'])
//                ->where('type', 'shop_category')
//                ->where('url', translit_url($categoryData['name']) . '-' . $categoryData['id'])
//                ->get('route')->result_array();
//
//            if (!$ext_rote || count($ext_rote) == 0 || $ext_rote == null) {
//                $parentUrl = $this->db
//                    ->where('entity_id', $categoryData['parent_id'])
//                    ->where('type', 'shop_category')
//                    ->get('route')->result_array();
//
//
//                $parentUrl1 = !empty($parentUrl) ? $parentUrl['0']['parent_url'] . '/' . $parentUrl['0']['url'] : '';
//
//                $route = [
//                    'parent_url' => $parentUrl1,
//                    'url' => translit_url($categoryData['name']) . '-' . $categoryData['id'],
//                    'entity_id' => $categoryData['id'],
//                    'type' => 'shop_category',
//                ];
//
//                $this->db->insert('route', $route);
//                $newRouteId = $this->db->insert_id();
////                $this->db->update('shop_category', ['route_id' => $newRouteId], ['id' => $categoryData['id']]);
//                $categories[$categoryId]['route_id'] = $newRouteId;
//            } else {
//                $parentUrl = $this->db
//                    ->where('entity_id', $categoryData['parent_id'])
//                    ->where('type', 'shop_category')
//                    ->get('route')->result_array();
//
//                $parentUrl1 = !empty($parentUrl) ? $parentUrl['0']['url'] : '';
//                $route = [
//                    'parent_url' => $parentUrl1,
//                    'url' => translit_url($categoryData['name']) . '-' . $categoryData['id'],
//                    'entity_id' => $categoryData['id'],
//                    'type' => 'shop_category',
//                ];
//
//                $this->db->update('route', $route, ['id' => $ext_rote['0']['id']]);
////                $this->db->update('shop_category', ['route_id' => $ext_rote['0']['id']], ['id' => $categoryData['id']]);
//                $categories[$categoryId]['route_id'] = $ext_rote['0']['id'];
//            }
//
//            $categories[$categoryId]['full_path_ids'] = serialize(array_reverse($currentPathIds));
//
//            $this->create_full_rote_urls($categoryId, array_reverse($currentPathIds));
//            unset($categoryId, $categoryData);
//        }
//        unset($categories1);
//        return $categories;
//    }
//
//    public function create_full_rote_urls($categoryId, $currentPathIds)
//    {
//        if (!empty($currentPathIds)) {
//            $currentPathIds[max($currentPathIds)] = $categoryId;
//            $urls = $this->db->select('url')->where('type', 'shop_category')
//                ->where_in('entity_id', $currentPathIds)->get('route')->result_array();
//            krsort($urls);
//            foreach ($urls as $key => $url_1) {
//                $full_rote_urls[] = $url_1['url'];
//            }
//            $this->db->set('parent_url', implode('/', $full_rote_urls))->where('entity_id', $categoryId)->where('type', 'shop_category')->update('route');
//        }
//    }


}
