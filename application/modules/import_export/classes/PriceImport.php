<?php

namespace import_export\classes;

use Exception;
use PHPExcel_IOFactory;
use Propel\Runtime\Collection\ObjectCollection;
use SProductVariantPrice;

class PriceImport
{

    /**
     * @var array
     */
    private $file_result = [];

    /**
     * @var array
     */
    private $files_info;

    /**
     * @var array
     */
    private $all_price_type = [];

    /**
     * @var array
     */
    private $final_result = [];

    /**
     * Upload selected files in the backup/tmp directory
     *
     * @throws Exception
     * @return void
     */
    public function upload_file() {

        $path_to_file = BACKUPFOLDER . 'tmp';

        $this->checkPathWritable($path_to_file);

        $config['upload_path']          = $path_to_file;
        $config['allowed_types']        = 'xls|xlsx';

        \CI::$APP->load->library('upload', $config);

        if (!\CI::$APP->upload->do_upload(0)) {

            throw new Exception(\CI::$APP->upload->display_errors());
        }
        $data = [
                 'upload_data' => \CI::$APP->upload->data(),
                ];

        chmod($data['upload_data']['full_path'], 0777);

        $this->setFilesInfo($data['upload_data']);

    }

    /**
     * Check directory exists, if not, create and chmod to writable
     * @param $path
     * @throws Exception
     */
    private function checkPathWritable($path) {

        if (file_exists($path)) {

            if (777 != substr(sprintf('%o', fileperms($path)), -4)) {

                chmod($path, 0777);
            }

        } else {

            if (false === mkdir($path) ) {

                throw new Exception('Don\'t make create directory in the path' . $path);

            }
            chmod($path, 0777);

        }
    }

    /**
     * @return void
     */
    public function startPriceImport() {

        $this->ParseDocument();

        $this->generateFinalResult();

        $this->setAllPriceType();

        $this->setImportContentInModel();

        $this->recursive_remove_directory(BACKUPFOLDER . 'tmp');
    }

    /**
     * @return void
     */
    private function setImportContentInModel() {

        foreach ($this->getFinalResult() as $variant) {

            $this->setOneVariantInfo($variant);

        }
    }

    /**
     *
     * @param $variant
     */
    private function setOneVariantInfo($variant) {

        $model = \SProductVariantsQuery::create()
            ->findPk($variant['var_id']);

        /// @todo: не сетает новую цену для самого товара
        $model->setPrice($variant['origin_price'] ?: $model->getPrice());

        if ($variant['number']) {
            $model->setNumber($variant['number']);
        }

        $model->save();
        $product_id = $model->getProductId();

        $price_type_id = $this->getPriceTypeId($variant);

        $variant = $this->cleanVariantPriceType($variant, $price_type_id);

        $variant_price = $model->getSProductVariantPrices();

        if (count($variant_price)) {

            $variant = $this->UpdateVariantPrice($variant_price, $variant, $price_type_id);

        }

        $this->CreateVariantPrice($variant, $price_type_id, $product_id);

    }

    /**
     * @param array $variant
     * @param array $price_type_id
     * @return array
     */
    private function cleanVariantPriceType(array $variant, $price_type_id) {

        foreach (array_keys($price_type_id) as $key) {

            if (array_key_exists($key, $variant)) {

                if (!$variant[$key]) {

                    unset($variant[$key]);
                }
            }
        }

        return $variant;

    }

    /**
     * @param $variant
     * @param $prices_type_id
     * @param $product_id
     */
    private function CreateVariantPrice($variant, $prices_type_id, $product_id) {

        $variant_keys = array_keys($variant);

        foreach ($prices_type_id as $key => $item) {

            if (in_array($key, $variant_keys)) {

                $variant_price = new SProductVariantPrice();

                $data = [
                         'var_id'  => $variant['var_id'],
                         'prod_id' => $product_id,
                         'type_id' => $item,
                         'price'   => str_replace(',', '.', $variant[$key]),

                        ];

                $variant_price->addToModel($data);

            }
        }
    }

    /**
     * @param ObjectCollection $variant_prices
     * @param array $variant
     * @param array $prices_type_id
     * @return array
     * @throws \Propel\Runtime\Exception\PropelException
     */
    private function UpdateVariantPrice(ObjectCollection $variant_prices, $variant, $prices_type_id) {

        /** @var SProductVariantPrice $variant_price */
        foreach ($variant_prices as $variant_price) {

            foreach ($prices_type_id as  $key => $item) {

                if ($variant_price->getTypeId() == $item) {

                    $variant_price->setPrice(str_replace(',', '.', $variant[$key]));

                    $variant_price->save();

                    unset($variant[$key]);
                }
            }

        }

        return $variant ?: [];
    }

    /**
     * @param array $variant
     * @return array
     */
    private function getPriceTypeId(array $variant) {

        $price_type_id = [];

        foreach (array_keys($variant) as $item) {

            if (array_key_exists($item, $this->all_price_type)) {

                $price_type_id[$item] = $this->all_price_type[$item]['Id'];
            }
        }
        return $price_type_id;
    }

    /**
     * generate final result array, where key name column in the Entity
     *
     * @return void
     */
    private function generateFinalResult() {

        $file_result = $this->getFileResult();

        $first_element = array_shift($file_result);

        $new_data = [];

        foreach ($file_result as $line) {

            $temp = array_combine($first_element, $line);

            unset($temp['symbol']);

            $new_data[] = $temp;
        }

        $this->setFinalResult($new_data);
    }

    /**
     * @throws \PHPExcel_Reader_Exception
     * @return void
     */
    private function ParseDocument() {

        $inputFileType = PHPExcel_IOFactory::identify($this->getFilesInfo('full_path'));

        $objReader = PHPExcel_IOFactory::createReader($inputFileType);

        $objPHPExcel = $objReader->load($this->getFilesInfo('full_path'));

        $ar = $objPHPExcel->getActiveSheet()->toArray();

        $this->setFileResult($ar);

    }

    /**
     * @param string|null $key
     * @return array
     */
    private function getFilesInfo($key = null) {

        if ($key != null && $this->files_info[$key]) {

            return $this->files_info[$key];

        }

        return $this->files_info;
    }

    /**
     * @param array $files_info
     */
    private function setFilesInfo($files_info) {
        $this->files_info = $files_info;
    }

    /**
     * @return array
     */
    private function getFileResult() {
        return $this->file_result;
    }

    /**
     * @param array $file_result
     */
    private function setFileResult(array $file_result) {
        $this->file_result = $file_result;
    }

    /**
     * @return array
     */
    public function getFinalResult() {
        return $this->final_result;
    }

    /**
     * @param array $final_result
     */
    private function setFinalResult($final_result) {
        $this->final_result = $final_result;
    }

    /**
     * @return void
     */
    public function setAllPriceType() {

        $price_type = \SProductVariantPriceTypeQuery::create()
            ->find()->toArray();

        if (count($price_type) > 0) {

            foreach ($price_type as $item) {

                $this->all_price_type[$item['NameType']] = $item;
            }
        }
    }

    private function recursive_remove_directory($directory) {
        $handle = opendir($directory);
        while (FALSE !== ($item = readdir($handle))) {
            if ($item != '.' && $item != '..') {
                $path = $directory.'/'.$item;
                if (is_dir($path)) {
                    $this->recursive_remove_directory($path);
                } else {
                    unlink($path);
                }
            }
        }
        closedir($handle);
    }
}