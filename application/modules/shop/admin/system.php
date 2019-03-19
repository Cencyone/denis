<?php

/**
 * ShopCore class file
 *
 * @package Shop
 * @copyright 2010 Siteimage
 * @author <dev@imagecms.net>
 */
use ImportCSV\ImportBootstrap as Import;

class ShopAdminSystem extends ShopAdminController
{

    /**
     * Fields in export that are marked by default
     * @var array
     */
    private $checkedFields = [
                              'name',
                              'url',
                              'prc',
                              'var',
                              'cat',
                              'num',
                             ];

    private $languages = null;

    private $uploadDir = BACKUPFOLDER;

    private $csvFileName = 'product_csv_1.csv';

    private $uplaodedFileInfo = [];

    public function __construct() {
        parent::__construct();

        \ShopController::checkVar();
        \ShopAdminController::checkVarAdmin();

        $this->languages = $this->db->get('languages')->result();
        $this->load->helper('file');
        ini_set('max_execution_time', 9000000);
        set_time_limit(9000000);
    }

    /**
     * Import products from CSV file
     * @return void
     */
    public function import() {

        if (count($_FILES)) {
            $this->saveCSVFile();
        }
        if (count($this->input->post('attributes')) && $this->input->post('csvfile')) {
            $importSettings = $this->cache->fetch('ImportExportCache');
            if (empty($importSettings) || $importSettings['withBackup'] != $this->input->post('withBackup')) {
                $this->cache->store('ImportExportCache', ['withBackup' => $this->input->post('withBackup')], '25920000');
            }
            $result = Import::create()->withBackup()->startProcess()->resultAsString();
            echo (json_encode($result));
        } else {
            if (!$_FILES) {

                $customFields = SPropertiesQuery::create()->setComment(__METHOD__)->orderByPosition()->find();
                $cFieldsTemp = $customFields->toArray();
                $cFields = [];
                foreach ($cFieldsTemp as $f) {
                    $cFields[] = $f['CsvName'];
                }

                $importSettings = $this->cache->fetch('ImportExportCache');
                $this->template->assign('withBackup', $importSettings['withBackup']);
                $this->configureImportProcess();
                $this->template->registerJsFile(getModulePath('shop') . 'admin/templates/system/importExportAdmin.js', 'after');
                $this->render(
                    'import',
                    [
                     'customFields'  => SPropertiesQuery::create()->setComment(__METHOD__)->orderByPosition()->find(),
                     'languages'     => $this->languages,
                     'cFields'       => $cFields,
                     'currencies'    => SCurrenciesQuery::create()->setComment(__METHOD__)->orderByIsDefault()->find(),
                     'attributes'    => ImportCSV\BaseImport::create()->makeAttributesList()->possibleAttributes,
                     'checkedFields' => $this->checkedFields,
                    ]
                );
            }
        }

        $this->cache->delete_all();

        if ($this->input->post('withResize')) {
            $result[content] = explode('/', trim($result['content'][0]));
            \MediaManager\Image::create()
                    ->resizeById($result['content'])
                    ->resizeByIdAdditional($result['content'], TRUE);
        }

        if ($this->input->post('withCurUpdate')) {
            \Currency\Currency::create()->checkPrices();
        }
    }

    public function getCategoryProperties() {
        $cats = $this->input->post('selectedCats');
        if (count($cats) > 0) {
            $properties = SPropertiesQuery::create()
                    ->join('ShopProductPropertiesCategories')
                    ->where('ShopProductPropertiesCategories.CategoryId IN ?', $cats)
                    ->joinWithI18n(\MY_Controller::getCurrentLocale())
                    ->distinct()
                    ->orderByPosition()
                    ->find();
        } else {
            $properties = SPropertiesQuery::create()
                    ->joinWithI18n(\MY_Controller::getCurrentLocale())
                    ->orderByPosition()
                    ->find();
        }

        $result = '';
        foreach ($properties as $p) {
            $result .= '<div class="serverResponse">
            <span class="frame_label no_connection eattrcol">
            <span class="niceCheck b_n">
            <input type="checkbox" value="1" class="eattr" name="attribute[' . $p->getCsvName() . ']" />
            </span>
            ' . $p->getName() . '
            </span>
            </div>';
        }
        if (empty($result)) {
            $result = '<p class="serverResponse">' . lang('Could not find any properties', 'admin') . '</p>';
        }

        echo $result;
        return;
    }

    public function export() {
        $export = new ShopExportDataBase(
            [
             'attributes'   => $this->input->post('attribute'),
             'attributesCF' => $this->input->post('cf'),
             'import_type'  => trim($this->input->post('import_type')),
             'delimiter'    => trim($this->input->post('delimiter')),
             'enclosure'    => trim($this->input->post('enclosure')),
             'encoding'     => trim($this->input->post('encoding')),
             'currency'     => trim($this->input->post('currency')),
             'languages'    => trim($this->input->post('language')),
             'selectedCats' => $this->input->post('selectedCats'),
            ]
        );

        $export->getDataArray();
        if ($export->hasErrors() == FALSE) {
            if (!$this->input->is_ajax_request()) {
                // if the request is from Ajax, then start file download
                if (trim($this->input->post('formed_file_type')) != '0') {
                    // was already been formed - just start downloading
                    $this->downloadFile($this->input->post('formed_file_type'));
                    return;
                }

                // file forming
                $this->createFile($this->input->post('type'), $export);
                // then start downloading
                $this->downloadFile($this->input->post('type'));
                return;
            }

            // ajax request - only forming and output file type
            if (FALSE !== $this->createFile($this->input->post('type'), $export)) {
                echo $this->input->post('type');
                return;
            }

            echo 'Error';
        } else {
            echo $this->processErrors($export->getErrors());
        }
    }

    /**
     * Start file downloading
     * @param string $type file type csv|xls|xlsx
     */
    protected function downloadFile($type = 'csv') {
        if (!in_array($type, ['csv', 'xls', 'xlsx'])) {
            return;
        }

        $file = 'products.' . $type;
        $path = $this->uploadDir . $file;
        if (file_exists($path)) {
            $this->load->helper('download');
            $data = file_get_contents($path);
            if ($type == 'csv') {
                // for some reason downloaded file has text/x-c++ mime-type
                header('Content-type: text/csv');
            }
            force_download($file, $data);
        }
    }

    /**
     * File creating
     * @param string $type file type
     * @param ShopExportDataBase $export
     * @return string file name
     */
    protected function createFile($type, $export) {
        switch ($type) {
            case 'xls':
                return $export->saveToExcelFile($this->uploadDir, 'Excel5');
                break;
            case 'xlsx':
                return $export->saveToExcelFile($this->uploadDir, 'Excel2007');
                break;
            default: // csv
                return $export->saveToCsvFile($this->uploadDir);
        }
    }

    /**
     *
     */
    private function saveCSVFile() {
        $this->takeFileName();

        $this->load->library(
            'upload',
            [
             'overwrite'     => true,
             'upload_path'   => $this->uploadDir,
             'allowed_types' => '*',
            ]
        );

        $fileExt = pathinfo($_FILES['userfile']['name'], PATHINFO_EXTENSION);
        if (!in_array($fileExt, ['csv', 'xls', 'xlsx'])) {
            echo json_encode(['error' => lang('Wrong file type. Only csv|xls|xlsx')]);
            return;
        }

        if ($this->upload->do_upload('userfile')) {
            $data = $this->upload->data();

            if (($data['file_ext'] === '.xls') || ($data['file_ext'] === '.xlsx')) {
                $this->convertXLStoCSV($data['full_path']);
                unlink(BACKUPFOLDER . $data['client_name']);
            } else {
                rename(BACKUPFOLDER . str_replace(' ', '_', $data['client_name']), BACKUPFOLDER . $this->csvFileName);
            }

            $this->configureImportProcess();
        } else {
            echo json_encode(['error' => $this->upload->display_errors()]);
        }
    }

    /**
     *
     * @param type $fileKey
     * @return boolean
     */
    private function mimePreFilter($fileKey) {
        $mimes = & get_mimes();

        $fileName = $_FILES[$fileKey]['name'];
        $ext = pathinfo($fileName, PATHINFO_EXTENSION);
        $fileTmpPath = $_FILES[$fileKey]['tmp_name'];
        $mimeType = mime_content_type($_FILES[$fileKey]['tmp_name']);

        $neededMimes = FALSE;
        foreach ($mimes as $ext_ => $possibleMimes) {
            if ($ext == $ext_) {
                $neededMimes = $possibleMimes;
            }
        }

        if ($neededMimes == FALSE) {
            echo json_encode(['error' => lang('System error - uknown extention', 'admin')]);
            return false;
        }

        if (is_array($neededMimes) && in_array($mimeType, $neededMimes, TRUE)) {
            return TRUE;
        } elseif ($mimeType === $neededMimes) {
            return TRUE;
        }

        echo json_encode(['error' => lang('File cannot be uploaded, because file with such extention <br /> can not have those mime-type. Mime: ', 'admin') . $mimeType]);

        return FALSE;
    }

    private function convertXLStoCSV($excel_file = '') {
        $shopDir = getModulePath('shop');
        include $shopDir . 'classes/PHPExcel.php';
        include $shopDir . 'classes/PHPExcel/IOFactory.php';
        include $shopDir . 'classes/PHPExcel/Writer/Excel2007.php';
        $objReader = PHPExcel_IOFactory::createReaderForFile($excel_file);
        $objReader->setReadDataOnly(true);
        $objPHPExcel = $objReader->load($excel_file);
        $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);

        foreach ($sheetData as $i) {
            foreach ($i as $j) {
                $toPrint .= '"' . str_replace('"', '""', $j) . '";';
            }
            $toPrint = rtrim($toPrint, ';') . PHP_EOL;
        }
        $filename = $this->csvFileName;
        fopen(BACKUPFOLDER . $filename, 'w+');
        if (is_writable(BACKUPFOLDER . $filename)) {
            if (!$handle = fopen(BACKUPFOLDER . $filename, 'w+')) {
                echo json_encode(['error' => \ImportCSV\Factor::ErrorFolderPermission]);
                exit;
            }

            write_file(BACKUPFOLDER . $filename, $toPrint);

            fclose($handle);
        } else {
            showMessage(lang("The file {$filename} is not writable", 'admin'));
        }
    }

    private function configureImportProcess($vector = true) {
        if (file_exists($this->uploadDir . $this->csvFileName)) {
            $file = fopen($this->uploadDir . $this->csvFileName, 'r');
            $row = array_diff(fgetcsv($file, 10000, ';', '"'), [null]);
            fclose($file);
            $this->getFilesInfo();
            foreach ($this->uplaodedFileInfo as $file) {
                $uploadedFiles[str_replace('.', '', $file['name'])] = date('d.m.y H:i', $file['date']);
            }
            if ($vector && $this->input->is_ajax_request() && $_FILES) {
                echo json_encode(
                    [
                     'success'    => true,
                     'row'        => $row,
                     'attributes' => \ImportCSV\BaseImport::create()->attributes,
                     'filesInfo'  => $uploadedFiles,
                    ]
                );
            } else {
                $this->template->add_array(
                    [
                     'rows'       => $row,
                     'attributes' => \ImportCSV\BaseImport::create()->makeAttributesList()->possibleAttributes,
                     'filesInfo'  => $uploadedFiles,
                    ]
                );
            }
        }
    }

    private function takeFileName() {
        $fileNumber = (in_array($this->input->post('csvfile'), [1, 2, 3])) ? intval($this->input->post('csvfile')) : 1;
        $this->csvFileName = "product_csv_$fileNumber.csv";
    }

    public function getAttributes() {
        $this->takeFileName();
        $this->configureImportProcess(false);
        $this->render('import_attributes');
    }

    private function getFilesInfo($dir = null) {
        $dir = ($dir == null) ? $this->uploadDir : $dir;
        foreach (get_filenames($dir) as $file) {
            if (strpos($file, 'roduct_csv_')) {
                $this->uplaodedFileInfo[] = get_file_info($this->uploadDir . $file);
            }
        }
    }

    /**
     * Используется с exportUsers
     *
     */
    public function downExpUsers() {
        $this->load->helper('download');
        $data = file_get_contents('./application/backups/exportUsers.csv');
        force_download('exportUsers.csv', $data);
        redirect('/admin/components/run/shop/users/index#export');
    }

    /**
     *
     *
     */
    public function exportUsers() {
        if (!$this->input->post('export')) {
            showMessage(lang('You do not choose', 'admin'), '', 'r');
            exit;
        }
        if ($this->input->post('export') == 'csv') {
            $model = SUserProfileQuery::create()
                    ->find();
            $fp = fopen('./application/backups/exportUsers.csv', 'w');
            if ($fp === false) {
                showMessage(lang('Can not create file (No Rights)', 'admin'), '', 'r');
                exit;
            }
            foreach ($model as $u) {
                fwrite($fp, '"' . $u->getUserEmail() . '";"' . $u->getName() . "\"\n");
            }
            fseek($fp, 0);
            fclose($fp);

            $name = './application/backups/exportUsers.csv';
            if (file_exists($name)) {
                //изза того что пост приходит из js, не работают headers.
                //Для этого перегружаю через скрипты другую функцию downExpUsers().
                // на прямую не работает.
                echo "<script>document.location.href = '" . site_url('/admin/components/run/shop/system/downExpUsers') . "';sleep(4000);</script>";
            }
            showMessage(lang('Export successfully', 'admin'));
        }

    }

    /**
     * Create html box with errors.
     *
     * @param  array $errors Errors array
     * @return string
     */
    protected function processErrors(array $errors) {
        $result = '';
        foreach ($errors as $err) {
            $result .= $err . '<br/>';
        }

        return '<p class="errors">' . $result . '</p>';
    }

    /**
     * Check uploaded file extension
     *
     * @return boolean
     */
    protected function checkFileExtension($fileName) {
        $parts = explode('.', $fileName);

        if (end($parts) != 'csv') {
            return false;
        } else {
            return true;
        }
    }

    protected function downloadFile2($type = 'csv') {
        if (!in_array($type, ['csv', 'xls', 'xlsx'])) {
            return;
        }

        $file = 'exportUsers.' . $type;
        $path = './application/backups/exportUsers.csv';
        if (file_exists($path)) {
            $this->load->helper('download');
            $data = file_get_contents($path);
            if ($type == 'csv') {
                // for some reason downloaded file has text/x-c++ mime-type
                header('Content-type: text/csv');
            }
            force_download($file, $data);
        }
    }

}