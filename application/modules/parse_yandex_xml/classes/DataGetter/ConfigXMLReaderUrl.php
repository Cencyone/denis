<?php

namespace parse_yandex_xml\classes\DataGetter;

use Currency\Currency;
use SCurrenciesQuery;
use CMSFactory\ModuleSettings;
use XMLReader;

(defined('BASEPATH')) OR exit('No direct script access allowed');
require_once('AbstractAdvertisementXMLReader.php');

class ConfigXMLReaderUrl extends AbstractAdvertisementXMLReader
{
    /*
      Парсит наценки
     */

    public function get_settings()
    {
        return ModuleSettings::ofModule('parse_yandex_xml')->get();
    }

    protected function parseCategory()
    {

        if ($this->reader->nodeType == XMLREADER::ELEMENT && $this->reader->localName == 'category') {
            // объект для сохранения
            $category = array(
                'id' => $this->reader->getAttribute('id'),
                'parent_id' => $this->reader->getAttribute('parentId'),
                'active' => $this->reader->getAttribute('active') && $this->reader->getAttribute('active') != null ? $this->reader->getAttribute('active') : 1,
                //                'value' => $this->reader->getAttribute('value')
            );
            // читаем глубже, для получения текстового
            $this->reader->read();
            if ($this->reader->nodeType == XMLREADER::TEXT) {
                $category['name'] = trim($this->reader->value);
            }
            $this->resultOfferAlfa['categories'][] = $category;
        }
        self::parseCurrency();
//        self::parseVendor();
        self::parseOffer();
    }/* */


    protected function parseVendor()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->name == 'vendor') {
            if ($this->reader->readInnerXml() != '' || $this->reader->readInnerXml() != false || $this->reader->readInnerXml() != null) {
                $vendor['name'] = htmlspecialchars_decode($this->reader->readInnerXml());
                $vendor['name'] = str_replace('<![CDATA[', '', $vendor['name']);
                $vendor['name'] = str_replace(']]>', '', $vendor['name']);
                if ($vendor['name'] != '') {
                    $this->resultOfferAlfa['vendors'][translit_url(trim($vendor['name']))] = $vendor;
                }
            }
        }
    }

    protected function parseCurrency()
    {
        if ($this->reader->nodeType == XMLREADER::ELEMENT && $this->reader->localName == 'currency') {
            // объект для сохранения
            $currency = array(
                'id' => $this->reader->getAttribute('id'),
                'rate' => $this->reader->getAttribute('rate'),
            );
            $cur = \Currency\Currency::create()->getCurrencies();
            $arr_cur_ids = [];
            $array_xml_curs = [];

            foreach ($cur as $oneCur) {
                if ($oneCur->getCode() == (string)$currency['id']) {
                    $model = SCurrenciesQuery::create()->findPk($oneCur->id);
                } else {
                    $arr_cur_ids[] = $oneCur->id;
                }
            }

            if ($model == null) {

                \Currency\AdminCurrency::create()->createCurrency((string)$currency['id'], (string)$currency['id'], (string)$currency['id'], (string)$currency['rate']);

//                if ((string)$currency['rate'] === '1') {
//                    $model0 = SCurrenciesQuery::create()->findByName((string)$currency['id']);
//                    SCurrenciesQuery::create()->update(['Main' => false]);
//                    SCurrenciesQuery::create()->update(['IsDefault' => false]);
//                    $model0['0']->setIsDefault(true);
//                    $model0['0']->setMain(true);
//                    $model0['0']->save();
//                }
            }
            $this->resultOfferAlfa['currencies'][] = $currency;
        }

    }

    protected function parseOffer($settings, $codeAndId, $post)
    {
        if ($this->reader->nodeType == XMLREADER::ELEMENT && $this->reader->localName == 'offer') {

            $product = [
                'available' => $this->reader->getAttribute('available'),
                'id' => $id = $this->reader->getAttribute('id'),
                'number' => trim($id)
            ];

            while ($this->reader->read()) {

                if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->name == 'price') {
                    $product['price'] = $this->reader->readInnerXml();
                }

                if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->name == 'currencyId') {
                    $product['currencyId'] = $this->reader->readInnerXml();
                }
                if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->name == 'categoryId') {
                    $product['categoryId'] = $this->reader->readInnerXml();
                }

                // if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->name == 'picture') {

                //     while ($this->reader->name == 'picture') {
                //         $product['pictures'][] = $this->parsePicture($this->reader);
                //     }

                // }
                if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->name == 'picture') {
                    $product['pictures'][] = $this->reader->readInnerXml();
                }


                if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->name == 'name') {
                    $product['name'] = htmlspecialchars_decode($this->reader->readInnerXml());
                    $product['name'] = str_replace('<![CDATA[', '', $product['name']);
                    $product['name'] = str_replace(']]>', '', $product['name']);
                }

                if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->name == 'model') {
                    if ($product['name'] == null || $product['name'] == false) {
                        $product['name'] = htmlspecialchars_decode($this->reader->readInnerXml());
                        $product['name'] = str_replace('<![CDATA[', '', $product['name']);
                        $product['name'] = str_replace(']]>', '', $product['name']);
                    }
                }


                if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->name == 'shortname') {
                    $product['shortname'] = htmlspecialchars_decode($this->reader->readInnerXml());
                }

                if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->name == 'vendor') {
                    $product['vendor'] = htmlspecialchars_decode($this->reader->readInnerXml());
                    $product['vendor'] = str_replace('<![CDATA[', '', $product['vendor']);
                    $product['vendor'] = str_replace(']]>', '', $product['vendor']);

//                    $this->resultOfferAlfa['vendors'][translit_url(trim($product['vendor']))] = $product['vendor'];
                }
                // if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->name == 'vendor') {
                //     $product['vendor'] = $this->reader->readInnerXml();
                // }


                if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->name == 'description') {
                    // $product['description'] = htmlspecialchars_decode($this->reader->readInnerXml());

                    $product['description'] = $this->reader->readInnerXml();
                    $product['description'] = htmlspecialchars_decode(str_replace('<![CDATA[', '', $product['description']));
                    $product['description'] = htmlspecialchars_decode(str_replace(']]>', '', $product['description']));
                }
                if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->name == 'prod') {
                    $product['pr_id_main'] = $this->reader->readInnerXml();
                }

                if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->name == 'cpa') {
                    $product['stock'] = $this->reader->readInnerXml();
                }
                if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->name == 'active') {
                    $product['active'] = $this->reader->readInnerXml();
                }
                if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->name == 'var_name') {
                    $product['var_name'] = htmlspecialchars_decode($this->reader->readInnerXml());
                }
                if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->name == 'prod_name') {
                    $product['prod_name'] = htmlspecialchars_decode($this->reader->readInnerXml());
                }


                if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->name == 'vendorCode') {

                    $product['vendorCode'] = $vendorCode = htmlspecialchars_decode($this->reader->readInnerXml());
                    $product['number'] = trim(htmlspecialchars_decode($vendorCode));
                    $product['vendorCode'] = trim(htmlspecialchars_decode($product['vendorCode']));
                }

                if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->name == 'param') {
                    while ($this->reader->name == 'param') {
//                             $attr_name = $this->reader->getAttribute('name');
                        unset($val);
                        unset($key);
                        $product['params'][$key = $this->reader->getAttribute('name')] = $val = $this->parseProperties($this->reader);

                        if ($key == 'Артикул') {
                            $product['number'] = trim($val);
                        }
                    }
                }
                if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->name == 'country_of_origin'/*'country_of_origin'*/) {
                    //                        $product['params']['country_of_origin'] = $this->reader->readInnerXml();
                    if ($this->reader->readInnerXml() != '' || $this->reader->readInnerXml() != false || $this->reader->readInnerXml() != null) {
                        $product['params']['Страна'] = $this->reader->readInnerXml();
                    }
                }


                 if($product['vendorCode'] =='' || $product['vendorCode'] ===null || $product['vendorCode'] ===false){
                     $product['vendorCode']=$product['id'];
                     if($product['number'] =='' || $product['number'] ==null || $product['number'] ==false){
                         $product['number']=$product['vendorCode'];
                     }

                 }


                if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->name == 'url') {
                    $product['url'] = $this->reader->readInnerXml();
                } else if ($this->reader->nodeType == XMLReader::END_ELEMENT && $this->reader->name == 'offer') {
                    break;
                }

            }

            $this->resultOfferAlfa['all_offers'][] = $product;
            $this->resultOfferAlfa['vendors'][translit_url($product['vendor'])] = $product['vendor'];
        }
    }


    /**--------------------------------------------------------------------------------------------*/
    protected function parsePicture()
    {
        $this->reader->read();
        if ($this->reader->nodeType == XMLREADER::TEXT) {
            $picture = $this->reader->value;
            return $picture;
        } else {
            return null;
        }
        return $picture;
    }

    protected function parseProperties()
    {
        $this->reader->read();
        if ($this->reader->nodeType == XMLREADER::TEXT) {
            $picture = $this->reader->value;
            return $picture;
        } else {
            return null;
        }
        return $picture;
    }


}
