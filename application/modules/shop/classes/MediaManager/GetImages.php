<?php

namespace MediaManager;

/**
 * Class for searching images at google
 *
 * @author nikolia27 (15-08-2013)
 *
 *
 *
 */
class GetImages
{

    const DEFAULT_FILES_TYPES = 'jpg';

    /**
     * Saves what types of images you can download
     * @var array
     */
    private $allowedMimeTypes = [
                                 'image/jpeg',
                                 'image/png',
                                ];

    /**
     * Saves what types of images you can search
     * @var array
     */
    private $allowedFilesTypes = [
                                  'jpg',
                                  'png',
                                 ];

    /**
     *
     * @var GetImages
     */
    private static $instance = NULL;

    /**
     * Unchanged part of url
     * @var string
     */
    private $baseUrl = 'http://ajax.googleapis.com/ajax/services/search/images?v=1.0';

    /**
     * Param that can be
     * @var array
     */
    private $params = [
                       'upload_dir' => './uploads/shop/products/origin/',
                       'imgsz'      => 'large',
                      ];

    /**
     *
     * @param array $params
     */
    private function __construct($params) {
        if (is_array($params)) {
            foreach ($params as $key => $value) {
                if (array_key_exists($key, $this->params)) {
                    $this->params[$key] = $value;
                }
            }
        }
    }

    private function __clone() {

    }

    /**
     * Creating an instance
     * @param array $params (optional)
     * @return GetImages
     */
    public static function create($params = NULL) {
        // create an instance if is not created or params are presents
        if (null == self::$instance || null != $params) {
            self::$instance = new GetImages($params);
        }
        return self::$instance;
    }

    /**
     * @param $imagesUrl
     * @return bool
     */
    public function checkImage($imagesUrl) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $imagesUrl);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_NOBODY, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 0);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 2);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'HEAD');
        curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);
        if ($status !== 200) {
            return FALSE;
        }
        return TRUE;
    }

    /**
     *
     */
    public function getProgress() {
        $fileName = $this->getProgressFileName();

        if (!file_exists($fileName)) {
            return 0;
        }

        if (FALSE === $progressData = file_get_contents($fileName)) {
            return 0;
        }

        if (!is_numeric($progressData)) {
            return 0;
        }

        return (int) $progressData;
    }

    /**
     *
     * @return string
     */
    private function getProgressFileName() {
        $ci = &get_instance();
        $userId = $ci->dx_auth->get_user_id();
        return "uploads/g_img_pr_{$userId}.txt";
    }

    /**
     * Saves the image from url to specified folder (uploads/shop/products/origin/ by default)
     * @param string $url
     * @return string|false filename on success or FALSE
     */
    public function saveImage($url) {

        $urlArray = parse_url(urldecode($url));

        if ($urlArray['query']) {
            $url = ($urlArray['scheme'] ? $urlArray['scheme'] . '://' : '') . $urlArray['host'] . $urlArray['path'];
        }

        if (FALSE != $image = $this->getImage($url)) {
            $CI = &get_instance();
            $CI->load->helper('translit');
            $ext = substr($url, strrpos($url, '.') + 1); // Формат
            $name = substr(basename($url), 0, strrpos(basename($url), '.')); // имя
            $imgName = translit_url($name);
            $imgName = $imgName . '.' . $ext;

            $CI->load->helper('file_helper');
            $writeStatus = write_file($this->params['upload_dir'] . $imgName, $image);
            return $writeStatus === FALSE ? FALSE : urldecode($imgName);
        }
        return FALSE;
    }

    /**
     * Returns the image contents or FALSE
     * @param string $url
     * @param int $nobody
     * @return bool|string
     */
    public function getImage($url, $nobody = 0) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_NOBODY, $nobody);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 2);
        $res = curl_exec($curl);
        $mimeType = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);

        curl_close($curl);
        foreach ($this->allowedMimeTypes as $mimeType_) {
            if ($mimeType == $mimeType_) {

                return $res;
            }
        }

        return FALSE;
    }

    /**
     * Getting data form google api
     * @param string $url
     * @return array
     */
    public function getData($url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_NOBODY, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $res = curl_exec($curl);
        curl_close($curl);
        $res = json_decode($res);
        $imagesData = [];
        foreach ($res->responseData->results as $oneImageData) {
            $imagesData[] = (array) $oneImageData;
        }
        return $imagesData;
    }

}