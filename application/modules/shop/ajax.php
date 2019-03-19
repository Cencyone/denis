<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Ajax extends ShopController
{

    public function __construct() {
        parent::__construct();
    }

    /**
     * Rate product
     *
     * @access public
     */
    public function rate() {
        $productId = (int) $this->input->post('pid');
        $rating = (int) $this->input->post('val');

        if (!in_array($rating, range(1, 5))) {
            exit;
        }

        // Check if product exists
        if (SProductsQuery::create()->setComment(__METHOD__)->findPk($productId) !== null && !$this->session->userdata('voted' . $productId) == true) {
            $model = SProductsRatingQuery::create()->setComment(__METHOD__)->findPk($productId);

            if ($model === null) {
                $model = new SProductsRating;
                $model->setProductId($productId);
            }

            $model->setVotes($model->getVotes() + 1);
            $model->setRating($model->getRating() + $rating);
            $model->save();

            //set product updated
            $p = SProductsQuery::create()
                    ->findOneById($productId);
            $p->setUpdated(date('U'));
            $p->save();

            $rating = round($model->getRating() / $model->getVotes());

            if ($rating == 1) {
                $rating = 'onestar';
            }
            if ($rating == 2) {
                $rating = 'twostar';
            }
            if ($rating == 3) {
                $rating = 'threestar';
            }
            if ($rating == 4) {
                $rating = 'fourstar';
            }
            if ($rating == 5) {
                $rating = 'fivestar';
            }

            // Store session vote block;
            $this->session->set_userdata('voted' . $productId, true);
            if ($this->input->is_ajax_request()) {
                return json_encode(['classrate' => "$rating"]);
            }
        }
    }

    public function widget($name) {
        echo widget($name);
    }

    public function getCartDataHtml() {
        return $this->render('cart_data', [], true);
    }

    public function getWishListDataHtml() {
        return $this->render('wish_list_data', [], true);
    }

    public function getCompareDataHtml() {
        return $this->render('compare_data', [], true);
    }

    public function getCategoryAttributes($catId) {
        $model = SCategoryQuery::create()
                ->filterById((int) $catId)
                ->filterByActive(TRUE)
                ->findOne();
        if (!$model) {
            return false;
        }
        return $this->render('category_attributes', ['model' => $model], true);
    }

    public function getNotifyingRequest() {
        $message = FALSE;
        $productId = $this->input->post('ProductId');
        $this->load->library('Form_validation');
        if ($this->input->post('notifme')) {
            $this->form_validation->set_rules('UserName', lang('Name', 'admin'), 'trim|xss_clean|required|max_length[50]');
            $this->form_validation->set_rules('UserEmail', lang('Email', 'admin'), 'valid_email|required|max_length[50]');
            //            $this->form_validation->set_rules('UserPhone', ShopCore::t('Мобільний телефон'), 'trim|xss_clean|required|max_length[50]');
            $this->form_validation->set_rules('UserComment', lang('Additional information'), 'trim|xss_clean|max_length[500]');
            if ($this->form_validation->run($this) != FALSE) {
                $notification = new SNotifications;
                $notification->fromArray($this->input->post());
                $notification->setStatus(SNotificationStatusesQuery::create()->setComment(__METHOD__)->orderById()->findOne()->getId());
                $notification->setDateCreated(time());
                $notification->setActiveTo($this->input->post('ActiveTo') ? strtotime($this->input->post('ActiveTo')) : time());
                $notification->setNotifiedByEmail(0);
                $notification->save();

                CMSFactory\Events::create()->raiseEvent(['model' => $notification], 'Shop:notifyingRequest');

                $locale = \MY_Controller::getCurrentLocale();
                $notif = $this->db->where('locale', $locale)->where('name', 'incoming')->get('answer_notifications')->row();
                $message = $notif->message;
                //$message = ShopSettingsQuery::create()->setComment(__METHOD__)->filterByName('adminMessageIncoming')->findOne()->getValue();
            }
        }

        return $this->render_min(
            'report_appearance',
            [
             'model'      => SProductsQuery::create()->setComment(__METHOD__)->filterById($productId)->limit(1)->findOne(),
             'message'    => $message,
             'user_name'  => $this->dx_auth->get_username(),
             'user_email' => $this->dx_auth->get_user_email(),
            ]
        );
    }

    public function getApiNotifyingRequest() {
        $message = FALSE;
        $this->load->library('Form_validation');
        $this->form_validation->set_error_delimiters(FALSE, FALSE);
        if ($this->input->post('notifme')) {
            $this->form_validation->set_rules('UserName', lang('Your name'), 'trim|xss_clean|required|max_length[50]');
            $this->form_validation->set_rules('UserEmail', lang('E-mail'), 'valid_email|required|max_length[50]');
            $this->form_validation->set_rules('UserPhone', lang('Phone'), 'phone|trim|xss_clean|max_length[50]');
            if ($this->form_validation->run($this) != FALSE) {
                $notification = new SNotifications;
                $notification->fromArray($this->input->post());
                $notification->setStatus(SNotificationStatusesQuery::create()->setComment(__METHOD__)->orderById()->findOne()->getId());
                $notification->setDateCreated(time());
                //                $notification->setActiveTo($this->input->post('ActiveTo') ? strtotime($this->input->post('ActiveTo')) : time());
                $notification->setNotifiedByEmail(0);
                $notification->save();
                $locale = \MY_Controller::getCurrentLocale();
                $notif = $this->db->where('locale', $locale)->where('name', 'incoming')->get('answer_notifications')->row();
                $message = $notif->message;

                CMSFactory\Events::create()->raiseEvent(['model' => $notification], 'Shop:notifyingRequest');

                echo json_encode(
                    [
                     'msg'      => $message,
                     'status'   => true,
                     'close'    => true,
                     'refresh'  => $this->input->post('refresh') ? $this->input->post('refresh') : FALSE,
                     'redirect' => $this->input->post('redirect') ? $this->input->post('redirect') : FALSE,
                    ]
                );
            } else {
                echo json_encode(
                    [
                     'msg'         => validation_errors(),
                     'status'      => false,
                     'refresh'     => $this->input->post('refresh') ? $this->input->post('refresh') : FALSE,
                     'redirect'    => $this->input->post('redirect') ? $this->input->post('redirect') : FALSE,
                     'validations' => [
                                       'UserEmail' => form_error('UserEmail'),
                                       'UserName'  => form_error('UserName'),
                                       'UserPhone' => form_error('UserPhone'),
                                      ],
                    ]
                );
            }
        }
    }

    public function getCarouselImages() {
        $coef = (int) ShopCore::app()->SSettings->topSalesBlockFormulaCoef;
        $offset = (int) $this->input->get('first') - 1;
        $limit = (int) $this->input->get('last') - (int) $this->input->get('first') + 1;

        $products = SProductsQuery::create()
                ->withColumn("SProducts.Views + SProducts.AddedToCartCount * $coef", 'Formula')
                ->orderBy('Formula', Criteria::DESC);

        $total = clone $products;
        $total = $total->count();

        $return = "<data>\r\n\t";
        $products = $products->limit($limit)->offset($offset)->find();

        if ($products) {
            $return .= "<total>$total</total>\r\n";
            foreach ($products as $product) {
                $url = $product->getUrl();
                $imageUrl = productImageUrl($product->getSmallImage());
                $return .= "\t<product>\r\n\t\t<url>$url</url>\r\n\t\t<image>$imageUrl</image>\r\n\t</product>\r\n";
            }
        } else {
            $return .= "<total>0</total>\r\n";
        }
        $return .= '</data>';

        header('Content-Type: text/xml');
        return $return;
    }

    public function checkEmail() {
        $email = $this->input->post('email');
        $this->load->library('DX_Auth');
        if ($this->dx_auth->is_email_available($email)) {
            echo json_encode(['result' => true]);
        } else {
            echo json_encode(['result' => false]);
        }
    }

    public function getPage($id) {
        $page = get_page($id);
        $this->template->display('ajax_static_page', ['page' => $page]);
    }

}

/* End of file shop.php */