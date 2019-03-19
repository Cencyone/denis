<?php

/**
 * Export to Yandex.Market
 * @link http://help.yandex.ru/partnermarket/yml/about-yml.xml
 */
class Yandex extends ShopController
{

    public function __construct() {
         parent::__construct();
    }

    public function genreYML() {
        $this->load->module('ymarket')->index();
    }

}