<?php

namespace parse_yandex_xml\classes\DataGetter;

use Currency\Currency;
use SCurrenciesQuery;
use CMSFactory\ModuleSettings;
use XMLReader;

(defined('BASEPATH')) OR exit('No direct script access allowed');
require_once('AbstractAdvertisementXMLReader.php');

class ConfigXMLReader extends AbstractAdvertisementXMLReader
{

    /*
      Парсит наценки
     */
    /**
     * @param CI_Upload $upload
     */
    public function parse1($post)
    {

        $xml = simplexml_load_file($this->xl_file);

        try {
            // IMPORT PROPERTIES
            if($post['cats'] && !empty($post['cats'])) {
                if (isset($xml->categories)) {
                    self::parseCategory($xml->categories, $post);
                    self::parseVendorss($xml->items, $post);
                }
            }

                if (isset($xml->items)) {
                    self::parseItems($xml->items, $post);
                }


        } catch (Exception $e) {
            $this->error_log(lang('Import error', 'exchange') . ': ' . $e->getMessage() . $e->getFile() . $e->getLine());
            echo $e->getMessage() . $e->getFile() . $e->getLine();
            echo 'failure';
            exit;
        }
    }
    public function get_settings()
    {
        return ModuleSettings::ofModule('parse_yandex_xml')->get();
    }


    protected function parseVendorss($xmlitems, $post)
    {

        $product = [];
        foreach ($xmlitems->item as $item) {
            if ($item->vendor) {
                $product['vendor'] = (string)$item->vendor;
                $product['vendor'] = str_replace('<![CDATA[', '', $product['vendor']);
                $product['vendor'] = str_replace(']]>','',$product['vendor']);
                $this->resultOfferAlfa['vendors'][translit_url(trim($product['vendor']))] = $product['vendor'];
            }
        }
    }

    protected function parseCategory($xmlcategories, $post)
    {

            $category = [];
            foreach ($xmlcategories->category as $cats) {

                if ($cats->id) {
                    $category['id'] = (int)$cats->id;
                }
                if ($cats->name) {
                    $category['name'] = (string)$cats->name;
                }
                if ($cats->parentId) {
                    $category['parent_id'] = (int)$cats->parentId;
                }
                $this->resultOfferAlfa['categories'][] = $category;
            }

    }/*не будет разбивки по категориям из файлов*/
    protected function parseItems($xmlitems, $post)
    {
        unset($product);
        $product = [];
            foreach ($xmlitems->item as $item) {

                if ($item->id) {
                    $product['id'] = (int)$item->id;
                }
                if ($item->name) {
                    $product['name'] = (string)$item->name;
                    $product['name'] = str_replace('<![CDATA[', '', $product['name']);
                    $product['name'] = str_replace(']]>','',$product['name']);
                }
                if ($item->categoryId) {
                    $product['categoryId'] = (int)$item->categoryId;
                }
                if ($item->code) {
                    $product['vendorCode'] = (string)$item->code;
                }
                if ($item->vendor) {
                    $product['vendor'] = (string)$item->vendor;
                    $product['vendor'] = str_replace('<![CDATA[', '', $product['vendor']);
                    $product['vendor'] = str_replace(']]>','',$product['vendor']);
//                    $this->resultOfferAlfa['vendors'][translit_url(trim($product['vendor']))] = $product['vendor'];
                }
                if ($item->description) {
                    $product['description'] = (string)$item->description;
                    $product['description'] = str_replace('<![CDATA[', '', $product['description']);
                    $product['description'] = str_replace(']]>','',$product['description']);
                    $product['description'] = str_replace('nbsp;','',$product['description']);
                }
                if ($item->priceRUAH) {
                    $product['price'] = (string)$item->priceRUAH;
                }
                if ($item->stock) {
                    $product['available'] = (string)$item->stock;
                    $product['available'] =$product['available']=='В наличии'?$product['available']:false;
                }
                if ($item->image) {
                    $product['pictures'] = [(string)$item->image];
                }
                if ($item->priceRUAH) {
                    $product['url'] = (string)$item->url;
                }

                if($product['vendorCode'] =='' || $product['vendorCode'] ===null || $product['vendorCode'] ===false){
                    $product['vendorCode']=$product['id'];
                }
                $product['number']=$product['vendorCode'];
                $this->resultOfferAlfa['all_offers'][] = $product;
            }

    }


    protected function parseVendor($settings, $codeAndId, $post)
    {
        while ($this->reader->read()) {
            if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->name == 'vendor') {
                if($this->reader->readInnerXml() !='' || $this->reader->readInnerXml() != false || $this->reader->readInnerXml() !=null){
                    $vendor['name'] = $this->reader->readInnerXml();
                    $vendor['name'] = str_replace('<![CDATA[', '', $vendor['name']);
                    $vendor['name'] = str_replace(']]>','',$vendor['name']);
                    if($vendor['name'] !=''){
                        $this->resultOfferAlfa['vendors'][translit_url(trim($vendor['name']))] = $vendor;
                    }

                }
            }
        }

    }



    protected function parseItem($settings, $codeAndId, $post)
    {

        if ($this->reader->nodeType == XMLREADER::ELEMENT && $this->reader->localName == 'item') {
            $product = [];
//            $product = array(
//                'available' => $this->reader->getAttribute('available'),
//                'id' => $this->reader->getAttribute('id')
//            );

            while ($this->reader->read()) {

                if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->name == 'id') {
                    $product['id'] = $this->reader->readInnerXml();
                }
                if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->name == 'categoryId') {
                    $product['categoryId'] = $this->reader->readInnerXml();
                }
                if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->name == 'code') {
                    $product['vendorCode'] = $this->reader->readInnerXml();
                }
                if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->name == 'vendor') {
                    $product['vendor'] = $this->reader->readInnerXml();
                    $product['vendor'] = str_replace('<![CDATA[', '', $product['vendor']);
                    $product['vendor'] = str_replace(']]>','',$product['vendor']);
                }
                if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->name == 'model') {

                    $product['name'] = $this->reader->readInnerXml();
                    $product['name'] = str_replace('<![CDATA[', '', $product['name']);
                    $product['name'] = str_replace(']]>','',$product['name']);
                }
                if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->name == 'name') {
                    if($product['name'] == null | $product['name']== false){
                        $product['name'] = $this->reader->readInnerXml();
                        $product['name'] = str_replace('<![CDATA[', '', $product['name']);
                        $product['name'] = str_replace(']]>','',$product['name']);
                    }
                }
                if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->name == 'description') {

                    $product['description'] = $this->reader->readInnerXml();
                    $product['description'] = str_replace('<![CDATA[', '', $product['description']);
                    $product['description'] = str_replace(']]>','',$product['description']);
                }





                if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->name == 'priceRUAH') {
                    $product['price'] = $this->reader->readInnerXml();
                }

                if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->name == 'currencyId') {
                    $product['currencyId'] = $this->reader->readInnerXml();
                }
                if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->name == 'stock') {
                    $product['stock'] = $this->reader->readInnerXml();
                }


                // if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->name == 'picture') {

                //     while ($this->reader->name == 'picture') {
                //         $product['pictures'][] = $this->parsePicture($this->reader);
                //     }

                // }
                $product['number']=$product['vendorCode'];
                if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->name == 'image') {

                    $product['pictures'][] = $this->reader->readInnerXml();

                }


                if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->name == 'param') {
                    while ($this->reader->name == 'param') {
                        $product['params'][$this->reader->getAttribute('name')] = $this->parseProperties($this->reader);
                    }

                }
                if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->name == 'country_of_origin'/*'country_of_origin'*/) {
                    //                        $product['params']['country_of_origin'] = $this->reader->readInnerXml();
                    if($this->reader->readInnerXml() !='' || $this->reader->readInnerXml() != false || $this->reader->readInnerXml() !=null){
                        $product['params']['Страна'] = $this->reader->readInnerXml();
                    }
                }



                if($product['vendorCode'] =='' || $product['vendorCode'] ===null || $product['vendorCode'] ===false){
                    $product['vendorCode']=$product['id'];
                }
                $product['number']=$product['vendorCode'];

                if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->name == 'url') {
                    $product['url'] = $this->reader->readInnerXml();
                } else if ($this->reader->nodeType == XMLReader::END_ELEMENT && $this->reader->name == 'offer') {
                    break;
                }

            }

            // if($post['products'] /*&& !empty($post['cats'])*/){
            //    $this->resultOfferAlfa['new_offers'][] = $product;
            // }
            $this->resultOfferAlfa['all_offers'][] = $product;
        }
    }


    /**--------------------------------------------------------------------------------------------*/





}
