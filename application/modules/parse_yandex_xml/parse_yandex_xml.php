<?php

use parse_yandex_xml\classes\DataGetter\ConfigXMLReaderUrl;
use parse_yandex_xml\classes\DataGetter\ConfigXMLReader;

use Currency\Currency;
use Products\ProductApi;
use CMSFactory\ModuleSettings;
use MediaManager\Image;


(defined('BASEPATH')) OR exit('No direct script access allowed');

/**
 * Image CMS
 * Module Frame
 */
class Parse_yandex_xml extends MY_Controller
{


    private $time;
    protected $result_create = array();
    private $curs_in_shop;


    public function __construct()
    {
        parent::__construct();
        $lang = new MY_Lang();
        $lang->load('parse_yandex_xml');
        $this->load->helper('translit');
        $this->time = time();
        $this->settings = ModuleSettings::ofModule('parse_yandex_xml')->get();

        $this->curs_in_shop = \Currency\Currency::create()->getCurrencies();
        $currensy_shop_codeId = [];
        foreach ($this->curs_in_shop as $val) {
            $currensy_shop_codeId[$val->getCode()] = $val->getId();
        }
        $this->currensy_shop_codeId = $currensy_shop_codeId;
    }


    public function start_parse()
    {

        $site_url = substr($this->settings['main_set']['url_oasis'], 1);
        if (!$this->input->post()) {
            unlink('./uploads/files/create_Yml_file.xml');
            // $site_url = substr($this->settings['url_oasis'], 1);//str_replace('/', '', substr(site_url(), -1));

            if (substr_count($this->settings['main_set']['url_oasis'], 'http://') > 0 || substr_count($this->settings['main_set']['url_oasis'], 'https://') > 0) {
                if ($this->settings['main_set']['url_oasis'] /*and ! file_exists('./uploads/create_Yml_file.xml')*/) {
                    if (file_put_contents('./uploads/files/create_Yml_file.xml', file_get_contents($this->settings['main_set']['url_oasis'])) == 0) {
                        $curl = curl_init();
                        curl_setopt($curl, CURLOPT_URL, $this->settings['main_set']['url_oasis']);
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0');
                        $out = curl_exec($curl);
                        file_put_contents('./uploads/files/create_Yml_file.xml', $out);
                    }
                }
                $site_url = './uploads/files/create_Yml_file.xml';
            }
            return true;
        } else {
            if (substr_count($this->settings['main_set']['url_oasis'], 'http://') > 0 || substr_count($this->settings['main_set']['url_oasis'], 'https://') > 0) {
                $site_url = './uploads/files/create_Yml_file.xml';
            }
        }

//        $reader1 = new ConfigXMLReaderUrl($site_url);
//        $parse_result1 = $reader1->resultOfferAlfa;
        if ($this->settings['main_set']['url_oasis'] == 'http://rn-sport.com.ua/index.php?route=feed/hotline') {
            $reader = new ConfigXMLReader($site_url);
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

//            $reader->parseCats($this->input->post());
            $reader->parse1($this->input->post());
            $parse_result = $reader->resultOfferAlfa;
        } else {
            $reader = new ConfigXMLReaderUrl($site_url);
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

            $reader->parseCats($this->input->post());
            $parse_result = $reader->resultOfferAlfa;
        }


        if ($this->input->post('cats') && !$this->input->post('products')) {

            if (count($parse_result['categories']) > 0) {
                $vendors = new \parse_yandex_xml\DataCreators\BrandsDataCreator($parse_result['vendors']);
            }

            if (count($parse_result['categories']) > 0) {

                $cat_cr = new \parse_yandex_xml\DataCreators\CategoriesDataCreator($parse_result['categories']);
                $count_cats_cr = $cat_cr->updateData();

                showMessage(lang('Создано ', '') . $count_cats_cr . lang(' категорий, в файле было  ', '') . count($parse_result['categories']));
                return json_encode(array(lang('Создано ', '') . $count_cats_cr . lang(' категорий, в файле было  ', '') . count($parse_result['categories']), 'g', count($parse_result['all_offers'])));
            }
        } elseif (!$this->input->post('products') && count($parse_result['categories']) <= 0) {
            return json_encode(array(lang('Создано 0 категорий, в файле было  0', ''), 'g', count($parse_result['all_offers'])));
        }

        if ($this->input->post('products')) {

            if ($this->settings['main_set']['url_oasis'] == 'http://rn-sport.com.ua/index.php?route=feed/hotline') {
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
                $parse_result1 = $reader1->resultOfferAlfa;
            } else {


                $reader1 = new ConfigXMLReaderUrl($site_url);
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

                $reader1->parseOfferses($this->input->post());
                $parse_result1 = $reader1->resultOfferAlfa;
            }

            if (count($parse_result1['vendors']) > 0) {
                new \parse_yandex_xml\DataCreators\BrandsDataCreator($parse_result1['vendors']);
            }

            $prods_cr = new \parse_yandex_xml\DataCreators\ProductsDataCreator($parse_result1/*['all_offers']*/);
            $count_prods_cr = $prods_cr->updateData($parse_result['all_offers'], $this->input->post('products'), $this->currensy_shop_codeId);

            $this->db->query('DELETE FROM  shop_product_properties_data WHERE  value_id IS NULL');


            $count_new_offers = count($count_prods_cr);
            showMessage(lang('Создано ', '') . $count_new_offers . lang(' идет обработка файла  ', ''), 'g');

            return $count_new_offers;
            // return  json_encode(array(lang('Создано ', '').count($parse_result['new_offers']).lang(' идет обработка файла  ', ''), 'g', count($parse_result['all_offers'])));
        }
//        $reader->close(); //???????????

        $created_prods = $reader->resultOfferAlfa;
        $created_prods = count($created_prods['offers']);
        showMessage(lang('Создано ', '') . $created_prods . lang(' товаров ', ''));
        return json_encode(array(lang('Создано ', '') . $created_prods . lang(' товаров ', ''), 'g'));
        return $created_prods;

    }


    public function _install()
    {
        $this->load->dbforge();
        ($this->dx_auth->is_admin()) OR exit;
        $this->db
            ->where('identif', 'parse_yandex_xml')
            ->update(
                'components', [
                    'settings' => '',
                    'enabled' => 1,
                    'autoload' => 1
                ]
            );

        return TRUE;
    }

    public function _deinstall()
    {

        $this->load->dbforge();
        ($this->dx_auth->is_admin()) OR exit;
    }

}

/*
  Родительский класс для XML обработчиков.
 */




