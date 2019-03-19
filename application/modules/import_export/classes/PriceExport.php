<?php

namespace import_export\classes;

use import_export\classes\Logger as LOG;
use Map\SCurrenciesTableMap;
use Map\SProductVariantPriceTypeTableMap;
use PHPExcel;
use PHPExcel_IOFactory;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Exception\PropelException;
use SProducts;
use SProductsQuery;
use SProductVariantPriceQuery;
use SProductVariantPriceTypeQuery;

class PriceExport
{


    const BACKUP_FOLDER = './application/backups/';

    /**
     * @var array
     */
    private $categories = [];

    /**
     * @var array
     */
    private $price_type_data = [];

    /**
     * @var array
     */
    private $product_data = [];

    /**
     * @var string
     */
    private $locale;

    /**
     * @var array
     */
    private $price_types = [];

    public function __construct() {

        $this->setLocale();

    }

    /**
     * @return void
     */
    public function ExportVariantPrices() {
        $this->getProductInfo();
        $this->SaveToExelFile();
    }

    /**
     * @param string $type
     * @return void
     */
    public function downloadFile($type) {

        $file = 'product_price.' . $type;
        $path = self::BACKUP_FOLDER . $file;

        if (file_exists($path)) {
            \CI::$APP->load->helper('download');
            $data = file_get_contents($path);
            force_download($file, $data);

        } else {
            LOG::create()->set('Невозможно скачать файл!');
        }

    }

    /**
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     * @return void
     */
    private function SaveToExelFile() {

        $exelObj = new PHPExcel();

        /** Делаем активную первую страницу */
        $page = $exelObj->setActiveSheetIndex(0);

        $columns_name = array_keys(current($this->getProductData()));

        $additional_column = false;
        $column = 0;

        foreach ($columns_name as $column_name) {

            $page->setCellValueByColumnAndRow($column, 1, $column_name);

            if (is_array(current($this->getProductData())[$column_name])) {

                $page->setCellValueByColumnAndRow($column + 1, 1, 'symbol');
                $additional_column = true;
            }

            $row = 2;

            foreach ($this->getProductData() as $data) {

                if (is_array($data[$column_name])) {

                    $page->setCellValueByColumnAndRow($column, $row, $data[$column_name]['var_type_price']);
                    $page->setCellValueByColumnAndRow($column + 1, $row++, $data[$column_name]['symbol']);
                    continue;
                }

                $page->setCellValueByColumnAndRow($column, $row++, $data[$column_name]);

            }

            if ($additional_column == true) {
                ++$column;
                $additional_column = false;
            }

            ++$column;
        }

        $type = \CI::$APP->input->post('type');

        switch ($type) {

            case 'xlsx' :

                $objWriter = PHPExcel_IOFactory::createWriter($exelObj, 'Excel2007');

                break;

            case 'xls' :
                $objWriter = PHPExcel_IOFactory::createWriter($exelObj, 'Excel5');
                break;
        }

        if ($objWriter) {

            $objWriter->save(self::BACKUP_FOLDER . 'product_price.' . $type);
        }

        chmod(self::BACKUP_FOLDER . 'product_price.' . $type, 0777);

    }

    /**
     * @param ObjectCollection $products
     * @return void
     */
    private function setExportData(ObjectCollection $products) {

        /** @var SProducts $product */
        foreach ($products as $product) {

            $this->getVariantsDataForProduct($product);

        }

    }

    /**
     * @param SProducts $product
     * @return void
     */
    private function getVariantsDataForProduct(SProducts $product) {

        foreach ($product->getProductVariants() as $variant) {

            $this->product_data[$variant->getId()] = [
                                                      'var_id'       => $variant->getId(),
                                                      'prod_name'    => $product->getName(),
                                                      'var_name'     => $variant->getName(),
                                                      'origin_price' => $variant->getPriceInMain(),
                                                      'number'       => $variant->getNumber(),
                                                     ];

            foreach ($this->getPriceTypes() as $priceType) {

                $this->product_data[$variant->getId()][$priceType['name']] = [

                                                                              'var_type_price' => $this->price_type_data[$priceType['id']][$variant->getId()]['Price'],
                                                                              'symbol'         => $priceType['PriceType'] == 2 ? '%' : $priceType['code'],
                                                                             ];
            }

        }

    }

    /**
     * @throws PropelException
     * @return void
     */
    private function getProductInfo() {

        $variant_prices = SProductVariantPriceQuery::create()
            ->setComment(__METHOD__)
            ->find()->toArray();

        foreach ($variant_prices as $variant_price) {

            $this->price_type_data[$variant_price['TypeId']][$variant_price['VarId']] = $variant_price;
        }

        unset($variant_prices);

        $price_types = SProductVariantPriceTypeQuery::create()
            ->setComment(__METHOD__)
            ->leftJoinWithCurrency()
            ->withColumn(SProductVariantPriceTypeTableMap::COL_ID, 'id')
            ->withColumn(SProductVariantPriceTypeTableMap::COL_NAME_TYPE, 'name')
            ->withColumn(SProductVariantPriceTypeTableMap::COL_PRICE_TYPE, 'PriceType')
            ->withColumn(SCurrenciesTableMap::COL_CODE, 'code')
            ->select(['id', 'name', 'PriceType', 'code'])
            ->find()->toArray();

        $this->setPriceTypes($price_types);
        unset($price_types);

        $this->getProductByCategories();

    }

    /**
     * @return void
     */
    private function getProductByCategories() {

        foreach ($this->getCategories() as $category) {

            //@TODO: сделать так что-бы выбералось по тысяче товаров
            $products = SProductsQuery::create()
                ->setComment(__METHOD__)
                ->filterByCategoryId($category)
                ->find();

            $this->setExportData($products);

        }
    }

    /**
     * @return string
     */
    public function getLocale() {

        return $this->locale;
    }

    /**
     * @return void
     */
    public function setLocale() {

        $this->locale = \MY_Controller::getCurrentLocale();
    }

    /**
     * @return array
     */
    private function getProductData() {

        return $this->product_data;
    }

    /**
     * @param array $price_types
     */
    public function setPriceTypes(array $price_types) {

        $this->price_types = $price_types;
    }

    /**
     * @return array
     */
    public function getPriceTypes() {

        return $this->price_types;
    }

    /**
     * @return array
     */
    private function getCategories() {

        return $this->categories;
    }

    /**
     * @param array $categories
     */
    public function setCategories($categories) {

        $this->categories = $categories;
    }

}