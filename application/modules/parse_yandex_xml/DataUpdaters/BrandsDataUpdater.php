<?php

namespace parser_upd_prods\DataUpdaters;

use Category\CategoryApi;
use Products\ProductApi;

use CI; 

class BrandsDataUpdater
{
    /**
     * @var \CI_DB_active_record
     */
    protected $db;

    /**
     * @var IDataGetter
     */
    protected $dataGetter;


    /**
     * @var string
     */
    protected $locale = "ru";

    /**
     * @var array
     */
    protected $categoryData = [];
    protected $existingBrandsData;

    /**
     * @param IDataGetter $dataGetter
     */
    public function __construct($dataBrandsUg)
    {
        $this->dataGetter = $dataBrandsUg;
        $this->db = CI::$APP->db;
        CI::$APP->load->helper('translit');

        $this->loadExistingBarnds($dataBrandsUg);
    }

    private function loadExistingBarnds($dataBrandsUg)
    {

        $exist_brands = \CI::$APP->db
            ->select('id, url')
//            ->join('shop_category_i18n', 'shop_category.id=shop_category_i18n.id AND shop_category_i18n.locale="' . $this->locale . '"')
            ->get('shop_brands');

        if ($exist_brands && $exist_brands != null) {
            $all_brands = $exist_brands->result_array();
            foreach ($all_brands as $cat_id => $data) {
                $this->existingBrandsData[$data['url']] = $data['id'];
                unset($cat_id, $data);
            }
        }
        // $count_br = count($exist_brands);
        $count_br = $this->db->query('SELECT id FROM shop_brands ORDER BY id DESC LIMIT 1')->result()['0']->id;
        foreach ($dataBrandsUg as $key_pr => $prod_data) {


            if (!$this->existingBrandsData[$key_pr]) {
                $count_br++;
                $new_br[] = [
                    'id' => $count_br,
                    'url' => translit_url(trim($prod_data)),
                    'created' => time(),
                    'updated' => time(),
                ];
                $new_br_18n[] = [
                    'id' => $count_br,
                    'name' => (string)trim($prod_data),
                    'locale' => 'ru'
                ];

                $this->existingBrandsData[$key_pr] = $count_br;
            }
            unset($key_pr, $prod_data);
        }
        unset($dataBrandsUg);

        $this->db->insert_batch('shop_brands', $new_br);
        if ($this->db->_error_message() && $this->db->_error_message() != null && $this->db->_error_message() != '') {
            dump('shop_brands');
            // Log::changeLast(Log::TYPE_MAIN_SYNC, Log::STATUS_ERROR_CATS);
            dd($this->db->_error_message());
        }
        $this->db->insert_batch('shop_brands_i18n', $new_br_18n);
        if ($this->db->_error_message() && $this->db->_error_message() != null && $this->db->_error_message() != '') {
            dump('shop_brands_i18n');
            // Log::changeLast(Log::TYPE_MAIN_SYNC, Log::STATUS_ERROR_CATS);
            dd($this->db->_error_message());
        }
        unset($new_br);
        unset($new_br_18n);

    }

    public function getAllBrands()
    {
        return $this->existingBrandsData;
    }


}
