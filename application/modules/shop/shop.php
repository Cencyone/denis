<?php

use callbacks\Exceptions\ValidationException;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Shop Controller
 *
 * @uses ShopController
 * @package Shop
 * @version 0.1
 * @copyright 2010 Siteimage
 * @author <dev@imagecms.net>
 * @todo remove all callbacks logic
 */
class Shop extends ShopController
{

    /**
     * Display shop main page
     */
    public function index() {
        if ($this->uri->uri_string() === 'shop') {
            redirect('/', 'location', 301);
        }

        $this->core->set_meta_tags();

        $this->render('start_page');
    }

    /**
     * Save user callback
     * @deprecated since 4.9 use module callback
     *
     */
    public function callback() {

        $data = $this->createCallBack();
        $this->render_min('callback', $data);
    }

    /**
     * @deprecated since 4.9 use module callback
     */
    public function callbackBottom() {
        $data = $this->createCallBack();
        $this->render_min('callbackBottom', $data);
    }

    /**
     * @deprecated since 4.9 use module callback
     * @return string
     */
    public function callbackApi() {
        $response = [
                     'msg'    => 'Ошибка, не достаточно данных',
                     'status' => false,
                    ];

        if ($this->input->post()) {
            try {
                $success = $this->load->module('callbacks')->createFromPost();
                $response = [
                             'msg'      => $success,
                             'status'   => true,
                             'close'    => true,
                             'refresh'  => $this->input->post('refresh') ? $this->input->post('refresh') : FALSE,
                             'redirect' => $this->input->post('redirect') ? $this->input->post('redirect') : FALSE,
                            ];
            } catch (ValidationException $e) {
                $response = [
                             'msg'         => $e->getMessage(),
                             'status'      => false,
                             'validations' => $e->getValidationErrors(),
                            ];
            }
        }
        return json_encode($response);
    }

    /**
     * @deprecated since 4.9 use module callback
     * @return array
     */
    protected function createCallBack() {
        $data = [];
        if ($this->input->post()) {
            try {
                $data['success'] = $this->load->module('callbacks')->createFromPost();
            } catch (ValidationException $e) {
                $data = [
                         'success' => false,
                         'errors'  => $e->getValidationErrors(),
                        ];
            }
        }
        return $data;
    }

}

/* End of file shop.php */