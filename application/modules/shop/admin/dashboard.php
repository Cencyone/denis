<?php

use CMSFactory\Events;
use Propel\Runtime\ActiveQuery\Criteria;

class ShopAdminDashboard extends ShopAdminController
{

    protected $perPage = 10;

    public $defaultLanguage = null;

    public function __construct() {
        parent::__construct();

        if ($this->dx_auth->is_admin() == TRUE and SHOP_INSTALLED) {
            pjax('/admin/components/run/shop/orders/index');
        }

        ShopController::checkVar();
        ShopAdminController::checkVarAdmin();

        $this->defaultLanguage = getDefaultLanguage();
    }

    public function index() {
        if ($this->dx_auth->is_admin() == TRUE and SHOP_INSTALLED) {
            redirect('/admin/components/run/shop/orders/index');
        }

        $model = SOrdersQuery::create()
                ->filterByStatus(1)
                ->orderByDateCreated(Criteria::DESC);

        $model = $model
            ->distinct()
            ->limit($this->perPage)
            ->find();

        $orderStatuses = SOrderStatusesQuery::create()->setComment(__METHOD__)->orderByPosition(Criteria::ASC)->find();

        $s_ip = substr($this->input->server('SERVER_ADDR'), 0, strrpos($this->input->server('SERVER_ADDR'), '.'));

        switch ($s_ip) {
            case '127.0.0':
            case '127.0.1':
            case '10.0.0':
            case '172.16.0':
            case '192.168.0':
                $on_local = TRUE;
                break;
        }

        //bestsellers - 10 top saled products
        $bestSellers = SProductsQuery::create()->setComment(__METHOD__)->orderByAddedToCartCount('desc')->filterBy('AddedToCartCount', 0, '>')->limit(10)->find();

        //new users
        $newUsers = SUserProfileQuery::create()->setComment(__METHOD__)->orderByDateCreated('desc')->limit(10)->find();

        $amountPurchases = [];
        foreach ($newUsers as $user) {
            $amountPurchases[$user->getId()] = 0;
            foreach (SOrdersQuery::create()->setComment(__METHOD__)->leftJoin('SOrderProducts')->distinct()->filterByUserId($user->getId())->find() as $order) {
                if ($order->getPaid() == TRUE) {
                    foreach ($order->getSOrderProductss() as $p) {
                        $amountPurchases[$user->getId()] += $p->getQuantity() * $p->getPrice();
                    }
                    $amountPurchases[$user->getId()] += $order->getDeliveryPrice();
                }
            }
        }

        //todo - user online or offline
        //last comments
        if ($this->db->get_where('components', ['name' => 'comments'])->row()) {
            $sql = 'SELECT * FROM `comments` ORDER BY `date` DESC LIMIT 10';
            $lastCommentsArray = $this->db->query($sql)->result_array();
        }

        //last news
        if (($api_news = $this->cache->fetch('api_news_cache')) !== FALSE) {
            $this->template->assign('api_news', $api_news);
        } else {
            if ($on_local !== TRUE) {
                $this->config->load('api');

                $options = [];
                $options[CURLOPT_HEADER] = FALSE;
                $options[CURLOPT_RETURNTRANSFER] = TRUE;
                $options[CURLOPT_POST] = FALSE;
                $options[CURLOPT_POSTFIELDS] = ['for' => IMAGECMS_NUMBER];
                $options[CURLOPT_REFERER] = base_url();

                $handler = curl_init($this->config->item('imagecms_latest_news'));

                curl_setopt_array($handler, $options);
                $resp = curl_exec($handler);

                $result['code'] = curl_getinfo($handler, CURLINFO_HTTP_CODE);
                $result['result'] = $resp;
                $result['error'] = curl_errno($handler);

                curl_close($handler);
                $api_news = $result;

                if (count(unserialize($api_news['result'])) > 1 AND $api_news['code'] == '200') {
                    $this->template->assign('api_news', unserialize($api_news['result']));
                    $this->cache->store('api_news_cache', unserialize($api_news['result']));
                } else {
                    $this->cache->store('api_news_cache', 'false');
                }
            }
        }

        Events::create()->registerEvent('', 'ShopDashboard:show');
        Events::runFactory();

        $this->render(
            'dashboard',
            [
             'model'             => $model,
             'totalOrders'       => $totalOrders,
             'deliveryMethods'   => SDeliveryMethodsQuery::create()->setComment(__METHOD__)->useI18nQuery($this->defaultLanguage['identif'])->orderByName(Criteria::ASC)->endUse()->find(),
             'paymentMethods'    => SPaymentMethodsQuery::create()->setComment(__METHOD__)->useI18nQuery($this->defaultLanguage['identif'])->orderByName(Criteria::ASC)->endUse()->find(),
             'orderStatuses'     => $orderStatuses,
             'bestSellers'       => $bestSellers,
             'newUsers'          => $newUsers,
             'amountPurchases'   => $amountPurchases,
             'lastCommentsArray' => $lastCommentsArray,
             'onLocal'           => $on_local,
            ]
        );
    }

}