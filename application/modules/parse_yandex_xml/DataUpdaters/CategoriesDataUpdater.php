<?php

namespace ug_contrakt\src\Parsers\DataUpdaters;

use Category\CategoryApi;
use Products\ProductApi;
use thesouth\src\Interfaces\IDataUpdater;
use thesouth\src\Interfaces\IDataGetter;
use CI;
use ug_contrakt\src\Log\Log;

class CategoriesDataUpdater
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
            ->select('id, external_id_ug')
//            ->join('shop_category_i18n', 'shop_category.id=shop_category_i18n.id AND shop_category_i18n.locale="' . $this->locale . '"')
            ->get('shop_category');

        if ($exist_cats && $exist_cats != null) {
            $all_exist_cats = $exist_cats->result_array();
            foreach ($all_exist_cats as $cat_id => $data) {
                $this->existingCategoriesData[$data['external_id_ug']] = $data['id'];
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
        
        

        $data_cats_from_ug = $this->dataGetter;
        $cats_count = $this->db->query('SELECT id FROM shop_category ORDER BY id DESC LIMIT 1')->result()['0']->id;

        foreach($data_cats_from_ug as $kat_key=>$item_item) {
            if (!$this->categoryExists($item_item['id'])) {
                $cats_count++;

                $data_cr_cat[] = [
                    'id' => $cats_count,
                    'active' => 1,
                    'show_in_menu' => 1,
                    'external_id' => $item_item['id'],

                    'created' => time(),
                    'updated' => time(),
                    'route_id' => '',
                    'external_id_ug' => $item_item['id']
                ];
                $data_cr_cat_18n[] = [
                    'id' => $cats_count,
                    'name' => (string)$item_item['name'],
                    'locale' => 'ru'
                ];


                $this_result_for_fix['categories'][$cats_count] = [
                    'id' => $cats_count,
                    'name' => (string)$item_item['name'],
                    'external_id' => (int)$item_item['id'],
                    'parent_id_exter' =>  !$item_item['parent_id']
                        || $item_item['parent_id'] =='' || $item_item['parent_id'] == null? 0 : $item_item['parent_id']
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
            Log::changeLast(Log::TYPE_MAIN_SYNC, Log::STATUS_ERROR_CATS);
            dd($this->db->_error_message());
        }
        $this->db->insert_batch('shop_category_i18n', $data_cr_cat_18n);
        if ($this->db->_error_message() && $this->db->_error_message() != null && $this->db->_error_message() != '') {
            dump('shop_category_i18n');
            Log::changeLast(Log::TYPE_MAIN_SYNC, Log::STATUS_ERROR_CATS);
            dd($this->db->_error_message());
        }
        unset($data_cr_cat);
        unset($data_cr_cat_18n);

        $this->runPathsFix($this_result_for_fix['categories']);

        Log::changeLast(Log::TYPE_MAIN_SYNC, Log::STATUS_SUCCESS_CATS);

        return true;

    }


    protected function runPathsFix($this_result_for_fix)
    {


        $categories = $this_result_for_fix;

        unset($this_result_for_fix);
        foreach ($categories as $id => $category) {

            $parent_id = (int)$category['parent_id_exter'] ? $this->db->where('external_id_ug', (int)$category['parent_id_exter'])->get('shop_category')->row()->id : 0;
            $categories[$id]['parent_id'] = (int)$parent_id;

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
                Log::changeLast(Log::TYPE_MAIN_SYNC, Log::STATUS_ERROR_CATS);
                dd($this->db->_error_message());
            }
        }


    }

    private function getPathsAndParents($categories11)
    {
        $categories = $categories11;

        foreach ($categories as $categoryId => $categoryData) {
            $categories1[$categoryData['id']] = $categoryData;
        }

        // creating id-paths and url-paths of each category
        foreach ($categories1 as $categoryId => $categoryData) {
            $currentPathIds = [];

            $neededCid = $categoryData['parent_id'];

            while ($neededCid != 0) {
                $currentPathIds[] = $neededCid;
                $neededCid = $categories[$neededCid]['parent_id'];
            }
            $ext_rote = $this->db
                ->where('entity_id', $categoryData['id'])
                ->where('type', 'shop_category')
                ->where('url', translit_url($categoryData['name']) . '-' . $categoryData['id'])
                ->get('route')->result_array();

            if (!$ext_rote || count($ext_rote) == 0 || $ext_rote == null) {
                $parentUrl = $this->db
                    ->where('entity_id', $categoryData['parent_id'])
                    ->where('type', 'shop_category')
                    ->get('route')->result_array();


                $parentUrl1 = !empty($parentUrl) ? $parentUrl['0']['parent_url'] . '/' . $parentUrl['0']['url'] : '';

                $route = [
                    'parent_url' => $parentUrl1,
                    'url' => translit_url($categoryData['name']) . '-' . $categoryData['id'],
                    'entity_id' => $categoryData['id'],
                    'type' => 'shop_category',
                ];

                $this->db->insert('route', $route);
                $newRouteId = $this->db->insert_id();
//                $this->db->update('shop_category', ['route_id' => $newRouteId], ['id' => $categoryData['id']]);
                $categories[$categoryId]['route_id'] = $newRouteId;
            } else {
                $parentUrl = $this->db
                    ->where('entity_id', $categoryData['parent_id'])
                    ->where('type', 'shop_category')
                    ->get('route')->result_array();

                $parentUrl1 = !empty($parentUrl) ? $parentUrl['0']['url'] : '';
                $route = [
                    'parent_url' => $parentUrl1,
                    'url' => translit_url($categoryData['name']) . '-' . $categoryData['id'],
                    'entity_id' => $categoryData['id'],
                    'type' => 'shop_category',
                ];

                $this->db->update('route', $route, ['id' => $ext_rote['0']['id']]);
//                $this->db->update('shop_category', ['route_id' => $ext_rote['0']['id']], ['id' => $categoryData['id']]);
                $categories[$categoryId]['route_id'] = $ext_rote['0']['id'];
            }
            $categories[$categoryId]['full_path_ids'] = serialize(array_reverse($currentPathIds));
            $this->create_full_rote_urls($categoryId, array_reverse($currentPathIds));
            unset($categoryId, $categoryData);
        }
        unset($categories1);
        return $categories;
    }

    public function create_full_rote_urls($categoryId, $currentPathIds)
    {
        if (!empty($currentPathIds)) {
            $currentPathIds[max($currentPathIds)] = $categoryId;
            $urls = $this->db->select('url')->where('type', 'shop_category')
                ->where_in('entity_id', $currentPathIds)->get('route')->result_array();
            krsort($urls);
            foreach ($urls as $key => $url_1) {
                $full_rote_urls[] = $url_1['url'];
            }
            $this->db->set('parent_url', implode('/', $full_rote_urls))->where('entity_id', $categoryId)->where('type', 'shop_category')->update('route');
        }
    }


}
