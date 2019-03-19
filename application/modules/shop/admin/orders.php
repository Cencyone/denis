<?php

use cmsemail\email;
use CMSFactory\assetManager;
use CMSFactory\Events;
use Currency\Currency;
use Map\SOrderProductsTableMap;
use Map\SOrdersTableMap;
use mod_discount\Discount_product;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Propel;

/**
 * ShopAdminOrders
 *
 * @property Lib_admin lib_admin
 * @property Users users
 * @uses ShopController
 * @package
 * @version $id$
 * @copyright 2010 Siteimage
 * @author <dev@imagecms.net>
 * @license
 */
class ShopAdminOrders extends ShopAdminController
{

    /**
     *
     * @var array
     */
    public $defaultLanguage;

    /**
     *
     * @var TCPDF
     */
    public $pdf;

    /**
     *
     * @var integer
     */
    protected $checkPerPage = 10;

    /**
     *
     * @var integer
     */
    protected $countIterator = 1;

    /**
     *
     * @var integer
     */
    protected $countPage = 0;

    /**
     *
     * @var integer
     */
    protected $perPage = 12;

    /**
     * @var string
     */
    private $pageNumber;

    public function __construct()
    {

        parent::__construct();
        $lang = new MY_Lang();
        $lang->load();
        ShopController::checkVar();
        ShopAdminController::checkVarAdmin();

        $this->load->helper('Form');
        $this->load->library('form_validation');

        $this->defaultLanguage = getDefaultLanguage();
    }

    /**
     * @param bool $paid
     */
    public function ajaxChangeOrdersPaid($paid)
    {

        if (count($this->input->post('ids')) > 0) {
            $model = SOrdersQuery::create()
                ->findPks($this->input->post('ids'));
            if (!empty($model)) {
                foreach ($model as $order) {
                    $_SESSION['recount'] = true;
                    if ($order->getPaid() == $paid) {
                        continue;
                    }
                    $order->setPaid($paid);
                    $order->save();
                    $order->reload();
                    $this->recountUserAmount($order, !$paid);

                    if ($paid) {
                        $message = lang('Order paid status changed to Paid', 'admin') . '. ' . lang('Id:', 'admin') . ' ' . $order->getId();
                    } else {
                        $message = lang('Order paid status changed to Not paid', 'admin') . '. ' . lang('Id:', 'admin') . ' ' . $order->getId();
                    }
                    $this->lib_admin->log($message);
                }

                Events::create()->registerEvent(['model' => $model, 'paid' => $paid]);
                Events::runFactory();

                showMessage(lang('Payment status of orders changed', 'admin'), lang('Saved', 'admin'));
            }
        }
    }

    /**
     * Recounts amount of user
     * @param SOrders $orderModel
     * @param int|null $statusOldPaid
     */
    private function recountUserAmount(SOrders $orderModel, $statusOldPaid = NULL)
    {

        if (!((int)$orderModel->getUserId() > 0)) {
            return;
        }

        $amount = $this->db->select('amout')
            ->get_where('users', ['id' => $orderModel->getUserId()])
            ->row()->amout;

        $orderTotalPrice = $this->db
            ->select('total_price')
            ->where(['id' => $orderModel->getId()])
            ->get('shop_orders')
            ->row()->total_price;

        if ($statusOldPaid != $orderModel->getPaid()) {
            if ($orderModel->getPaid() == 1) {
                $amount += $orderTotalPrice;
            } else {
                $amount -= $orderTotalPrice;
            }
        }

        $this->db
            ->where('id', $orderModel->getUserId())
            ->limit(1)
            ->update(
                'users',
                [
                    'amout' => str_replace(',', '.', $amount),
                ]
            );
    }

    public function ajaxChangeOrdersStatus($status)
    {

        if (count($this->input->post('ids')) > 0) {
            $model = SOrdersQuery::create()
                ->findPks($this->input->post('ids'));
            $newStatusId = SOrderStatusesQuery::create()->setComment(__METHOD__)->findPk((int)$status);

            $statusEmail = SOrderStatusesI18nQuery::create()
                ->filterByLocale(MY_Controller::defaultLocale())
                ->filterById($status)
                ->findOne();

            if (!empty($newStatusId) and !empty($model)) {
                foreach ($model as $order) {
                    $order->setStatus((int)$status);
                    $order->save();

                    $modelOrderStatusHistory = new SOrderStatusHistory();
                    $modelOrderStatusHistory->setOrderId($order->getId())
                        ->setStatusId($status)
                        ->setUserId($this->dx_auth->get_user_id())
                        ->setDateCreated(time());

                    $modelOrderStatusHistory->save();

                    $statusModel = SOrderStatusesI18nQuery::create()
                        ->filterByLocale(MY_Controller::defaultLocale())
                        ->filterById($status)
                        ->findOne();

                    $message = lang('Order status changed to', 'admin') . ' ' . $statusModel->getName() . '. ' . lang('Id:', 'admin') . $order->getId();
                    $this->lib_admin->log($message);

                    email::getInstance()->sendEmail(
                        $order->getUserEmail(),
                        'change_order_status',
                        [
                            'status' => $statusEmail->getName(),
                            'userName' => $order->getUserFullName(),
                            'userEmail' => $order->getUserEmail(),
                            'userPhone' => $order->getUserPhone(),
                            'userDeliver' => $order->getUserDeliverTo(),
                            'orderLink' => shop_url('order/view/' . $order->getKey()),
                        ]
                    );
                }

                Events::create()->registerEvent(['model' => $model]);
                Events::runFactory();

                showMessage(lang('Order Status changed', 'admin'), lang('The operation was successful', 'admin'));
            }
        }
    }

    public function ajaxDeleteOrders()
    {

        if (count($this->input->post('ids')) > 0) {
            $model = SOrdersQuery::create()
                ->findPks($this->input->post('ids'));

            if (!empty($model)) {
                foreach ($model as $order) {

                    Events::create()->registerEvent(['order' => $order], 'ShopAdminOrder:ajaxDeleteOrders');
                    Events::runFactory();

                    $order->delete();
                    $amount_total = $this->recount_amount($order->getUserId());
                    $data = ['amout' => $amount_total];
                    $this->db->where('id', $order->getUserId());
                    $this->db->update('users', $data);

                    $this->lib_admin->log(lang('Order deleted', 'admin') . '. Id: ' . $order->getId());
                }

                showMessage(lang('Orders are removed', 'admin'), lang('The operation was successful', 'admin'));
            }
        }
    }

    /**
     * Send email to user.
     *
     * @param null|int $user_id
     * @return int
     */
    public function recount_amount($user_id = null)
    {

        $this->db->select('total_price');
        $this->db->where('user_id', $user_id);
        $res = $this->db->get('shop_orders')->result_array();
        $sum = 0;
        foreach ($res as $value) {
            $sum += $value['total_price'];
        }
        return $sum;
    }

    /**
     * @param int $Id
     * @throws PropelException
     */
    public function ajaxDeleteProduct($Id)
    {

        $orderedProduct = SOrderProductsQuery::create()
            ->filterById((int)$Id)
            ->findOne();
        if ($orderedProduct == null) {
            return;
        }

        //check if it's not a last product in order
        $countProducts = $this->db->select('*, IF (kit_id IS NOT NULL, kit_id, id) AS forgroup', false)
            ->where('order_id', $orderedProduct->getOrderId())
            ->group_by('forgroup')
            ->get('shop_orders_products')
            ->num_rows();

        if ($countProducts <= 1) {
            showMessage(lang('You can not delete the last item from the order', 'admin'), '', 'r');
            return;
        }

        if ($orderedProduct->getKitId() != null) {
            $kitProducts = SOrderProductsQuery::create()
                ->filterByKitId($orderedProduct->getKitId())
                ->filterByOrderId($orderedProduct->getOrderId())
                ->find();
            $kitProducts->delete();

            $oId = $orderedProduct->getOrderId();
            $order = SOrdersQuery::create()->setComment(__METHOD__)->findPk($oId);

            $order->updateTotalPrice();
            $order->save();
            $order->updateDeliveryPrice();
            $order->save();

            showMessage(lang('Product is removed from the Order', 'admin'));
            pjax('/admin/components/run/shop/orders/edit/' . $order->getId() . '#productsInCart');
            return;
        }
        if ($orderedProduct != null) {
            $oId = $orderedProduct->getOrderId();
            $orderedProduct->delete();

            $order = SOrdersQuery::create()->setComment(__METHOD__)->findPk($oId);

            $order->updateTotalPrice();
            $order->updateOriginPrice();
            $order->save();
            $order->updateDiscount();
            $order->updateDeliveryPrice();
            $order->save();

            showMessage(lang('Product is removed from the Order', 'admin'));
            pjax('/admin/components/run/shop/orders/edit/' . $order->getId() . '#productsInCart');
        }
    }

    /**
     * @param int $orderId
     */
    public function ajaxEditAddToCartWindow($orderId)
    {

        $this->render(
            '_editAddToCartWindow',
            [
                'order' => SOrdersQuery::create()->setComment(__METHOD__)->filterById($orderId)->findOne(),
            ]
        );
    }

    /**
     * ------------------------------------
     * Called by form#addToCartForm
     * $_POST['newProductId']
     * $_POST['newVariantId']
     * $_POST['newQuantity']
     * ------------------------------------
     *
     * @param integer $orderId
     * @todo also check gift sertificate
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function ajaxEditOrderAddToCart($orderId)
    {

        $productId = (int)$this->input->post('newProductId');
        $variantId = (int)$this->input->post('newVariantId');
        $quantity = (int)$this->input->post('newQuantity');
        $order = SOrdersQuery::create()
            ->filterById((int)$orderId)
            ->findOne();

        if ($order != null) {
            $product = SProductsQuery::create()
                ->filterById($productId)
                ->findOne();
            $variant = SProductVariantsQuery::create()
                ->filterById($variantId)
                ->findOne();
            $originVariantPrice = $variant->getPrice();

            if ($product != NULL && $variant != NULL) {
                $quantity = ($quantity < 1) ? 1 : $quantity;
                //add new Product to SOrderProducts
                $orderP = new SOrderProducts();
                $orderP->setOrderId((int)$orderId)
                    ->setProductId($product->getId())
                    ->setVariantId($variant->getId())
                    ->setProductName($product->getName())
                    ->setVariantName($variant->getName())
                    ->setPrice($variant->getPrice())
                    ->setOriginPrice($originVariantPrice)
                    ->setQuantity($quantity)
                    ->save();

                $order->updateOriginPrice();
                $order->save();
                $product->setAddedToCartCount($product->getAddedToCartCount() + 1);
                $product->save();
                //                $this->updateAllPrices($orderId); //new
                $order->updateTotalPrice();
                if ($order->getDiscountInfo() != 'product' && ($discount = $order->getDiscount()) > 0) {
                    $order->setTotalPrice($order->getTotalPrice() - $discount);
                }
                $order->updateDeliveryPrice();
                $order->save();
                showMessage(lang('Item has been added to the order', 'admin'));
                pjax('/admin/components/run/shop/orders/edit/' . $order->getId() . '#productsInCart');
            } else {
                showMessage(lang('This product does not exist', 'admin'));
            }
        }
    }

    /**
     *
     * @param integer $Id
     * @return string|null
     */
    public function ajaxEditOrderCart($Id)
    {

        $order = SOrderProductsQuery::create()
            ->filterById((int)$Id)
            ->findOne();

        //if it's product from kit
        if ($order->getKitId() != null) {
            $orderKit = SOrderProductsQuery::create()
                ->filterByOrderId($order->getOrderId())
                ->filterByKitId($order->getKitId())
                ->find();
            if ($this->input->post('newQuantity')) {
                foreach ($orderKit as $item) {
                    $item->setQuantity((int)$this->input->post('newQuantity'));
                    $item->save();
                }
            }
            $order = SOrdersQuery::create()->setComment(__METHOD__)->findPk($order->getOrderId());
            $order->updateTotalPrice();
            pjax('');
            return;
        }

        $product = SProductsQuery::create()
            ->filterById((int)$this->input->post('newProductId'))->findOne();
        $variant = SProductVariantsQuery::create()
            ->filterByProductId((int)$this->input->post('newProductId'))
            ->filterById((int)$this->input->post('newVariantId'))
            ->findOne();
        if ($order === null) {
            return;
        } elseif ($product === null || $variant === null) {
            if ($this->input->post('newQuantity')) {
                $order->setQuantity((int)$this->input->post('newQuantity'));
            }
            if ($this->input->post('newPrice')) {
                $order->setPrice($this->input->post('newPrice'));
            }
            $order->save();
            showMessage(lang('Product updated', 'admin'));
        } else {
            if ((int)$this->input->post('newProductId') != $order->getProductId()) {
                $order->setProductId((int)$this->input->post('newProductId'));
                $order->setVariantId((int)$this->input->post('newVariantId'));
                $order->setProductName($product->getName());
                $order->setVariantName($variant->getName());
                $order->setPrice($variant->getPrice());
                $order->setQuantity((int)$this->input->post('newQuantity'));
            } else {
                if ((int)$this->input->post('newVariantId') != $order->getVariantId() && $this->input->post('SavePrice')[0] != 'yes') {
                    $order->setVariantId((int)$this->input->post('newVariantId'));
                    $order->setVariantName($variant->getName());
                    $order->setPrice($variant->getPrice());
                } else {
                    if ($this->input->post('SavePrice')[0] != 'yes') {
                        $order->setPrice($variant->getPrice());
                    }
                }
                if ((int)$this->input->post('newQuantity') != $order->getQuantity()) {
                    if ((int)$this->input->post('newQuantity') < 1) {
                        $_POST['newQuantity'] = 1;
                    }
                    $order->setQuantity((int)$this->input->post('newQuantity'));
                }
            }
            $order->save();

            showMessage(lang('Product updated', 'admin'));
        }
        $order = SOrdersQuery::create()->setComment(__METHOD__)->findPk($order->getOrderId());
        $order->updateTotalPrice();
        $order->save();
        $order->updateDeliveryPrice();
        $order->save();

        pjax('');
    }

    /**
     * @param int $Id
     * @return bool
     * @throws PropelException
     */
    public function ajaxEditOrderCartNew($Id)
    {

        $new_quan = $this->input->post('newQuantity');
        $new_price = $this->input->post('newPrice');
        if ((int)$new_quan >= 100000000) {
            showMessage(lang('Very high price, please set smaller', 'admin'), lang('Error', 'admin'), 'r');
            return FALSE;
        }
        if (false !== $new_quan && (int)$new_quan < 1) {
            showMessage(lang('Quantity can not be less then 1', 'admin'), lang('Error', 'admin'), 'r');
            return FALSE;
        }

        $orderProduct = SOrderProductsQuery::create()->setComment(__METHOD__)->findPk($Id);
        $order = SOrdersQuery::create()->setComment(__METHOD__)->filterById($orderProduct->getOrderId())->findOne();

        if ($new_quan) {
            if ($kitId = $orderProduct->getKitId()) {
                $orderProducts = SOrderProductsQuery::create()->setComment(__METHOD__)->filterByKitId($kitId)->filterByOrderId($orderProduct->getOrderId())->find();
                foreach ($orderProducts as $product) {

                    $priceOldTotal = $product->getPrice() * $product->getQuantity();
                    $priceOldTotalOrig = $product->getOriginPrice() * $product->getQuantity();
                    $product->setQuantity($new_quan);
                    $product->save();
                    $diff += $product->getPrice() * $product->getQuantity() - $priceOldTotal;
                    $diffOrig += $product->getOriginPrice() * $product->getQuantity() - $priceOldTotalOrig;
                }
            } else {

                $priceOldTotal = $orderProduct->getPrice() * $orderProduct->getQuantity();
                $priceOldTotalOrig = $orderProduct->getOriginPrice() * $orderProduct->getQuantity();
                $orderProduct->setQuantity($new_quan);
                $orderProduct->save();
                $diff = $orderProduct->getPrice() * $orderProduct->getQuantity() - $priceOldTotal;
                $diffOrig = $orderProduct->getOriginPrice() * $orderProduct->getQuantity() - $priceOldTotalOrig;
            }
        } else {
            if (!$orderProduct->getKitId()) {

                $priceOldTotal = $orderProduct->getPrice() * $orderProduct->getQuantity();
                $priceOldTotalOrig = $orderProduct->getOriginPrice() * $orderProduct->getQuantity();
                $orderProduct->setPrice($new_price);
                $orderProduct->save();

                $diff = $orderProduct->getPrice() * $orderProduct->getQuantity() - $priceOldTotal;
                $diffOrig = $orderProduct->getOriginPrice() * $orderProduct->getQuantity() - $priceOldTotalOrig;
            }
        }
        $diff = str_replace(',', '.', $diff);
        $diffOrig = str_replace(',', '.', $diffOrig);

        $orderDisc = $order->getDiscountInfo();

        if ($order->getDiscountInfo() == 'product' || empty($orderDisc)) {
            $this->db->query("update shop_orders set total_price = total_price + '$diff' where id = '" . $orderProduct->getOrderId() . "'");
        } else {
            $discount = ($order->getTotalPrice() + $order->getGiftCertPrice()) / $order->getOriginPrice();
            $diffUserPrice = $diffOrig * $discount;

            $diffUserPrice = str_replace(',', '.', $diffUserPrice);
            $this->db->query("update shop_orders set total_price = total_price + '$diffUserPrice' where id = '" . $orderProduct->getOrderId() . "'");
        }
        $this->db->query("update shop_orders set origin_price = origin_price + '$diffOrig' where id = '{$orderProduct->getOrderId()}'");
        $this->db->query("update shop_orders set discount =  origin_price - COALESCE(gift_cert_price,0) - total_price where id = '{$orderProduct->getOrderId()}'");

        $order->reload();
        $order->updateTotalPrice();
        $order->updateOriginPrice();
        $order->updateDeliveryPrice();
        $order->updateDiscount();
        $order->save();

        $this->recoutUserOrdersAmount($order->getUserId());

        pjax('');
    }

    /**
     * Recount user paid orders amount price
     * @param integer $userId - User id
     * @return bool
     * @throws PropelException
     */
    private function recoutUserOrdersAmount($userId)
    {

        if ($userId) {
            $userOrders = SOrdersQuery::create()->setComment(__METHOD__)->filterByPaid(1)->filterByUserId($userId)->find();

            if (!$userOrders) {
                return FALSE;
            }

            $amount = 0;
            foreach ($userOrders as $order) {
                $amount += $order->getTotalPrice();
            }

            $user = SUserProfileQuery::create()->setComment(__METHOD__)->filterById((int)$userId)
                ->findOne();

            if ($user) {
                $user->setAmout($amount);
                $user->save();
            }
        }
    }

    /**
     * @param int $Id
     */
    public function ajaxEditWindow($Id)
    {

        $orderedProduct = SOrderProductsQuery::create()->setComment(__METHOD__)->filterById((int)$Id)->findOne();
        $this->render(
            '_editWindow',
            [
                'product' => SProductsQuery::create()->setComment(__METHOD__)->filterById($orderedProduct->getProductId())->findOne(),
                'orderedProduct' => $orderedProduct,
            ]
        );
    }

    /**
     * @param int $orderId
     */
    public function ajaxGetOrderCart($orderId)
    {

        $criteria = SOrderProductsQuery::create()->setComment(__METHOD__)->orderById(Criteria::ASC);
        $model = SOrdersQuery::create()
            ->findPk((int)$orderId);
        foreach ($model->getSOrderProductss($criteria) as $sOrderProduct) {
            if ($sOrderProduct->getKitId() > 0) {
                if (!isset($kits[$sOrderProduct->getKitId()]['total'])) {
                    $kits[$sOrderProduct->getKitId()]['total'] = 0;
                }

                if (!isset($kits[$sOrderProduct->getKitId()]['price'])) {
                    $kits[$sOrderProduct->getKitId()]['price'] = 0;
                }

                $kits[$sOrderProduct->getKitId()]['total']++;
                $kits[$sOrderProduct->getKitId()]['price'] = $kits[$sOrderProduct->getKitId()]['price'] + $sOrderProduct->toCurrency();
            }
        }

        $this->render(
            'cart_list',
            [
                'model' => SOrdersQuery::create()->setComment(__METHOD__)->filterById($orderId)->findOne(),
                'kits' => $kits,
                'deliveryMethods' => SDeliveryMethodsQuery::create()->setComment(__METHOD__)->useI18nQuery(MY_Controller::getCurrentLocale())->orderByName()->endUse()->find(),
                'paymentMethods' => SPaymentMethodsQuery::create()->setComment(__METHOD__)->useI18nQuery(MY_Controller::getCurrentLocale())->orderByName()->endUse()->find(),
            ]
        );
    }

    /**
     * @param null|string $type
     * @throws PropelException
     */
    public function ajaxGetProductList($type = NULL)
    {

        $products = new SProductsQuery();

        if (!empty(ShopCore::$_GET['term'])) {
            $text = ShopCore::$_GET['term'];
            if (!strpos($text, '%')) {
                $text = '%' . $text . '%';
            }
            if ($type != 'number') {
                $products
                    ->joinWithI18n(MY_Controller::defaultLocale())
                    ->filterById(ShopCore::$_GET['term'])
                    ->_or()
                    ->useI18nQuery(MY_Controller::defaultLocale())
                    ->filterByName('%' . ShopCore::$_GET['term'] . '%', Criteria::LIKE)
                    ->endUse()
                    ->_or()
                    ->useProductVariantQuery()
                    ->filterByNumber('%' . ShopCore::$_GET['term'] . '%', Criteria::LIKE)
                    ->endUse();
            } else {
                $products
                    ->useProductVariantQuery()
                    ->filterByNumber('%' . ShopCore::$_GET['term'] . '%', Criteria::LIKE)
                    ->endUse();
            }
        }

        if (!empty(ShopCore::$_GET['noids'])) {
            $products->filterById(ShopCore::$_GET['noids'], Criteria::NOT_IN);
        }

        $products = $products
            ->distinct()
            ->find();

        $variants = SProductVariantsQuery::create()
            ->joinWithI18n(MY_Controller::defaultLocale())
            ->filterBySProducts($products)
            ->orderById(Criteria::DESC)
            ->find();

        foreach ($variants as $variant) {
            $pVariants[$variant->getProductId()][$variant->getId()]['name'] = ShopCore::encode($variant->getName());
            $pVariants[$variant->getProductId()][$variant->getId()]['price'] = $variant->getPrice();
            $pVariants[$variant->getProductId()][$variant->getId()]['number'] = $variant->getNumber();
        }

        foreach ($products as $key => $product) {
            if ($pVariants[$product->getId()]) {
                foreach ($pVariants[$product->getId()] as $variant) {

                    $name = $variant['name'] ?: $product->getName();
                    $lable = ShopCore::encode($product->getId() . ' - ' . $name . ' (' . $variant['number'] . ')');
                    $response[] = [
                        'number' => $variant['number'] ?: '',
                        'label' => $lable,
                        'name' => ShopCore::encode($product->getName()),
                        'id' => $product->getId(),
                        'value' => $product->getId(),
                        'category' => $product->getCategoryId(),
                        'variants' => $pVariants[$product->getId()],
                        'cs' => Currency::create()->getSymbol(),
                    ];
                }
            }
        }

        echo json_encode($response);
    }

    /**
     * Get products in category id and children categories
     */
    public function ajaxGetProductVariants()
    {

        $productId = $this->input->post('productId');

        $product = $this->db->where('id', $productId)->get('shop_products')->row();
        $categoryId = $product->category_id;
        $brandId = $product->brand_id;

        $productVariants = $this->db->select('shop_product_variants.id, shop_product_variants_i18n.name, shop_product_variants.price,shop_currencies.symbol, shop_product_variants.stock, shop_product_variants.number')
            ->from('shop_products')
            ->join('shop_product_variants', 'shop_products.id = shop_product_variants.product_id')
            ->join('shop_product_variants_i18n', 'shop_product_variants.id = shop_product_variants_i18n.id')
            ->join('shop_currencies', 'shop_product_variants.currency = shop_currencies.id')
            ->where('shop_product_variants_i18n.locale', MY_Controller::getCurrentLocale())
            ->where('shop_product_variants.product_id', $productId)
            ->get()
            ->result_array();

        foreach ($productVariants as $key => $variants) {
            $arr_for_discount = [
                'product_id' => $productId,
                'category_id' => $categoryId,
                'brand_id' => $brandId,
                'vid' => $variants['id'],
                'id' => $productId,
            ];
            if (count($this->db->where('name', 'mod_discount')->get('components')->result_array()) != 0) {
                Discount_product::create()->getProductDiscount($arr_for_discount);
            }
            if ($discount = assetManager::create()->discount) {

                //ціна без знижки
                $price = $discount['price'];
                //знижка
                $discount_val = ($discount['discount_value'] < 1) ? 0 : $discount['discount_value'];
                //ціна зі знижкою
                $dif_price = (float)$price - (float)$discount_val;
                ($dif_price < 0) ? $price_new = 1 : $price_new = $dif_price;

                $productVariants[$key]['price'] = $price_new;
                $productVariants[$key]['origPrice'] = $price;
                $productVariants[$key]['numDiscount'] = $discount_val;
            } else {

                $price = round($variants['price'], ShopCore::app()->SSettings->getPricePrecision());
                $productVariants[$key]['origPrice'] = $productVariants[$key]['price'] = $price;
            }
        }

        echo json_encode($productVariants);
    }

    /**
     * Get products in category id and children categories
     */
    public function ajaxGetProductsInCategory()
    {

        $categoryId = $this->input->post('categoryId');

        $route = \core\models\RouteQuery::create()
            ->filterByEntityId($categoryId)
            ->filterByType(\core\models\Route::TYPE_SHOP_CATEGORY)
            ->findOne();

        $query = $this->db->select('entity_id as id')
            ->where('type', \core\models\Route::TYPE_SHOP_CATEGORY)
            ->like("concat(`parent_url` , '/', `url`)", $route->getFullUrl())
            ->get('route')
            ->result_array();

        $categoriesIds = [];
        foreach ($query as $q) {
            $categoriesIds[] .= $q['id'];
        }

        $products = $this->db->select('shop_products.id, shop_products_i18n.name')
            ->from('shop_products')
            ->join('shop_products_i18n', 'shop_products.id = shop_products_i18n.id')
            ->where('shop_products_i18n.locale', MY_Controller::getCurrentLocale())
            ->where_in('shop_products.category_id', $categoriesIds)
            ->get()
            ->result_array();
        echo json_encode($products);
    }

    /**
     * NEW
     *
     * Return products without variants
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function ajaxGetProductsList()
    {

        $search_word = trim($this->input->get('term'));

        $products = SProductsQuery::create()
            ->joinWithI18n($this->getCurrentLocale());

        if ($search_word) {

            /** @var SProductsQuery $products */
            $products = $products
                ->joinWithProductVariant()
                ->condition('numberCondition', 'ProductVariant.Number LIKE ?', '%' . $search_word . '%')
                ->condition('nameCondition', 'SProductsI18n.Name LIKE ?', '%' . $search_word . '%')
                ->condition('idtCondition', 'SProducts.Id LIKE ?', '%' . $search_word . '%')
                ->where(['numberCondition', 'nameCondition', 'idtCondition'], Criteria::LOGICAL_OR);

        }

        if (!empty($this->input->get('noids'))) {
            $products->filterById($this->input->get('noids'), Criteria::NOT_IN);
        }

        $products = $products
            ->distinct()
            ->find();

        $variants = SProductVariantsQuery::create()
            ->joinWithI18n(MY_Controller::defaultLocale())
            ->filterBySProducts($products)
            ->orderById(Criteria::DESC)
            ->find();

        $pVariants = [];
        $productsArray = $products->toKeyIndex('id');
        foreach ($variants as $variant) {
            $name = $variant->getName() ?: $productsArray[$variant->getProductId()]->getName();
            $pVariants[$variant->getProductId()][$variant->getId()]['name'] = $name;
            $pVariants[$variant->getProductId()][$variant->getId()]['price'] = $variant->getPrice();
            $pVariants[$variant->getProductId()][$variant->getId()]['number'] = $variant->getNumber();
        }

        $response = [];
        /** @var SProducts $product */
        foreach ($products as $product) {
            $label = $product->getId() . ' - ' . $product->getName();
            $response[] = [
                'label' => $label,
                'name' => ShopCore::encode($product->getName()),
                'id' => $product->getId(),
                'value' => $product->getId(),
                'category' => $product->getCategoryId(),
                'variants' => $pVariants[$product->getId()],
                'cs' => Currency::create()->getSymbol(),
            ];
        }
        echo json_encode($response);
    }

    /**
     * Get discount for user by id
     */
    public function ajaxGetUserDiscount()
    {

        $userId = $this->input->post('userId');
        if ($userId != null) {
            $query = $this->db->select('discount')->from('users')->where('id', $userId)->get()->row_array();
        }
        if ($query != null) {
            echo $query['discount'];
        }
    }

    public function ajaxMergeOrders()
    {

        $ids = $this->input->post('ids');
        if ($ids) {

            //check more then one id---------------------------------------check
            if (count($ids) < 2) {
                showMessage(lang('Choose more then one order', 'admin'), '', 'r');
                return;
            }

            //select orders
            $sOrders = SOrdersQuery::create()
                ->orderById(Criteria::DESC)
                ->findPks($ids);

            //get oldest order
            $newestOrder = $sOrders->getFirst();
            $oldestOrder = $sOrders->getLast();

            foreach ($sOrders as $one) {
                //check same payment status------------------------------------check
                if ($one->getPaid() != $newestOrder->getPaid()) {
                    showMessage(lang('Selected orders must have the same payment status', 'admin'), '', 'r');
                    return;
                }

                //check same user----------------------------------------------check
                if ($one->getUserId() !== $newestOrder->getUserId()) {
                    showMessage(lang('Choose orders with the same users', 'admin'), '', 'r');
                    return;
                }
            }
            $megaOrder = new SOrders();
            $megaOrder->setKey(self::createCode());
            $megaOrder->setStatus(1);
            $megaOrder->setPaid($newestOrder->getPaid());
            $megaOrder->setDateCreated($oldestOrder->getDateCreated());
            $megaOrder->setDateUpdated(time());

            $deliveryMethod = false;
            $deliveryPrice = 0;
            $paymentMethod = false;
            $userDeliverTo = false;
            $userEmail = false;
            $userId = false;
            $userIp = false;
            $userPhone = false;
            $userSurname = false;
            $userComment = false;
            $userFullname = false;

            $totalPrice = 0;
            $originPrice = 0;
            $customFieldsData = [];

            /* @var $order SOrders */
            foreach ($sOrders as $order) {
                //get delivery && paiment data from first order wich has some delivery method
                if (!$deliveryMethod && $order->getDeliveryMethod()) {
                    $deliveryMethod = $order->getDeliveryMethod();
                    $deliveryPrice = $order->getDeliveryPrice();
                    $paymentMethod = $order->getPaymentMethod();
                }

                $userDeliverTo = $userDeliverTo ?: $order->getUserDeliverTo();
                $userEmail = $userEmail ?: $order->getUserEmail();
                $userId = $userId ?: $order->getUserId();
                $userIp = $userIp ?: $order->getUserIp();
                $userPhone = $userPhone ?: $order->getUserPhone();
                $userSurname = $userSurname ?: $order->getUserSurname();
                $userFullname = $userFullname ?: $order->getUserFullName();
                $userComment = $userComment ?: $order->getUserComment();

                $totalPrice += $order->getTotalPrice();
                $originPrice += $order->getOriginPrice();

                foreach ($order->getCustomFields() as $field) {
                    $field->getCustomFieldValue() && !isset($customFieldsData[$field->getId()]) && $customFieldsData[$field->getId()] = $field->getCustomFieldData();
                }
            }

            $megaOrder->setDeliveryMethod($deliveryMethod);
            $megaOrder->setDeliveryPrice($deliveryPrice);
            $megaOrder->setPaymentMethod($paymentMethod);
            $megaOrder->setUserDeliverTo($userDeliverTo);
            $megaOrder->setUserEmail($userEmail);
            $megaOrder->setUserId($userId);
            $megaOrder->setUserIp($userIp);
            $megaOrder->setUserPhone($userPhone);
            $megaOrder->setUserSurname($userSurname);
            $megaOrder->setUserFullName($userFullname);
            $megaOrder->setUserComment($userComment);
            $megaOrder->setTotalPrice($totalPrice);
            $megaOrder->setOriginPrice($originPrice);
            $megaOrder->save();

            $orderStatus = new SOrderStatusHistory();
            $orderStatus->setOrderId($megaOrder->getId())
                ->setStatusId(1)
                ->setUserId($megaOrder->getUserId())
                ->setDateCreated(time())
                ->setComment('')
                ->save();

            foreach ($customFieldsData as $data) {
                $data instanceof CustomFieldsData && $data->setentityId($megaOrder->getId());
                $data->save();
            }

            //get all products from selected orders
            $ordersProducts = SOrderProductsQuery::create()
                ->filterBySOrders($sOrders)
                ->find();

            //change selected products order id to new order id
            foreach ($ordersProducts as $one) {
                $one->setOrderId($megaOrder->getId());
                $one->save();
            }

            //delete old orders
            foreach ($sOrders as $one) {
                $one->delete();
            }

            showMessage(lang('Orders successfully merged', 'admin'), lang('Success', 'admin'));
        }
    }

    /**
     * Create random code.
     * @return string
     * @static
     * @access public
     */
    public static function createCode()
    {

        return createOrderCode();
    }

    /**
     * Print orders check
     * @return boolean|string
     */
    public function ajaxPrint()
    {

        $ordersIds = func_get_args();

        if (!count($ordersIds)) {
            return false;
        }

        $this->load->helper('download');
        $this->pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $this->pdf->setFontSubsetting(false);
        $this->pdf->cms_cache_key = 'check' . time();
        $this->pdf->setPDFVersion('1.6');
        $this->pdf->SetFont('dejavusanscondensed', '', 10);
        $this->pdf->setPrintHeader(false);
        $this->pdf->setPrintFooter(false);
        $this->pdf->SetTextColor(0, 0, 0);

        foreach ($ordersIds as $id) {
            $this->pageNumber = '';
            $model = SOrdersQuery::create()->setComment(__METHOD__)->findPk($id);
            $products = $model->getSOrderProductss();

            // Print product 15 per page
            if ($products->count() >= 15) {
                $products = array_chunk((array)$products, 15);

                $n = 1;
                foreach ($products as $product) {
                    $this->pageNumber = '/ ' . $n;
                    $this->createPDFPage($model, $product, true);
                    $n++;
                }
            } elseif ($products->count() > 5) {
                // Print product >5 on two pages.
                $this->createPDFPage($model, $products, true);
            } else {
                $this->createPDFPage($model, $products, true);
            }
        }

        $title = count($ordersIds) > 1 ? lang('Orders_check', 'admin') : lang('Order_No_', 'admin') . $id;
        $this->pdf->Output("$title.pdf", 'D');
    }

    /**
     * Create order check and display PDF file.
     *
     * @param SOrders $model
     * @param SProducts $products
     * @param bool $duplicate
     * @return PDF file
     * @access public
     */
    public function createPDFPage(SOrders $model, $products, $duplicate = false)
    {

        if ($model->getDeliveryMethod()) {
            $freeFrom = SDeliveryMethodsQuery::create()->setComment(__METHOD__)->findPk($model->getDeliveryMethod())->getFreeFrom();
            if ($freeFrom > 0) {
                $deliveryPrice = ($model->getTotalPrice() < $freeFrom) ? $model->getDeliveryPrice() : 0;
            } elseif ($freeFrom <= 0) {
                $deliveryPrice = $model->getDeliveryPrice();
            }
        } else {
            $deliveryPrice = 0;
        }
        $totalPrice = $model->getTotalPrice() + $deliveryPrice;

        //if ($freeFrom > $totalPrice)
        if ($deliveryPrice > 0) {
            $delivery = new SOrderProducts();
            $delivery->setProductName(lang('Delivery', 'admin'));
            $delivery->setQuantity(1);
            $delivery->setPrice($deliveryPrice);
            $delivery->setOriginPrice($deliveryPrice);
            $deliver = $delivery;
        }

        //        /** Init Payment Method for order */
        //        if ($model->getSDeliveryMethods() instanceof SDeliveryMethods) {
        //
        //            $cr = new Criteria();
        //            $cr->add('active', TRUE, Criteria::EQUAL);
        //            $cr->add('shop_delivery_methods.id', $model->getPaymentMethod(), Criteria::EQUAL);
        //            $paymentMethods = $model->getSDeliveryMethods()->getPaymentMethodss($cr);
        //        }

        $man = Currency::create();
        $places = $man->getCurrencyDecimalPlaces($man->getMainCurrency()->getId());

        /** @var SOrderProducts $product */
        foreach ($products as $product) {
            $product->setOriginPrice(round($product->getOriginPrice(), $places));
            $product->setPrice($product->getPrice(), $places);
        }

        if ($model->getDeliveryMethod()) {
            $deliverMethod = SDeliveryMethodsQuery::create()
                ->joinWithI18n(MY_Controller::getCurrentLocale(), Criteria::LEFT_JOIN)
                ->findOneById($model->getSDeliveryMethods()->getId());
        }

        $gift = round($model->getGiftCertPrice(), ShopCore::app()->SSettings->getPricePrecision(), PHP_ROUND_HALF_DOWN);

        $productsTable = (new \Order\TableRenderer($model, $this->template))
            ->setCountIterator($this->countIterator)
            ->setCountPages($this->countPage)
            ->setPageNumber(ltrim($this->pageNumber, ' /'))
            ->render($products);

        $html = $this->render(
            'check',
            [
                'productsTable' => $productsTable,
                'deliverMethod' => $deliverMethod,
                'model' => $model,
                'products' => $products,
                'totalPrice' => round($totalPrice, $places),
                'paymentMethod' => $model->getSPaymentMethods(),
                'pageNumber' => $this->pageNumber,
                'countPage' => $this->countPage,
                'iterator' => $this->countIterator,
                'delivery' => $deliver,
                'gift' => $gift,
            ],
            true,
            true
        );

        if ($duplicate === false) {
            $resultHtml = $html . '<p>&nbsp;</p><p><hr></p><p>&nbsp;</p>' . $html;
        } else {
            $resultHtml = $html;
        }

        $this->pdf->AddPage();
        $this->pdf->writeHTML($resultHtml, true, false, true, false, '');
    }

    /**
     * Autocomlite users by name, email, id for orders create
     */
    public function autoComplite()
    {

        $s_limit = $this->input->get('limit');
        $s_coef = $this->input->get('term');

        $model = SUserProfileQuery::create();
        $model = $model->where('SUserProfile.Name LIKE "%' . $s_coef . '%"')
            ->_or()
            ->where('SUserProfile.UserEmail LIKE "%' . $s_coef . '%"')
            ->_or()
            ->where('SUserProfile.Id LIKE "%' . $s_coef . '%"')
            ->limit($s_limit)
            ->find();

        /** @var SUserProfile $val */
        foreach ($model as $val) {
            $response[] = [
                'value' => $val->getId() . ' - ' . $val->getName() . ' - ' . $val->getUserEmail(),
                'id' => $val->getId(),
                'name' => $val->getName(),
                'email' => $val->getUserEmail(),
                'phone' => $val->getPhone(),
                'address' => $val->getAddress(),
            ];
        }
        echo json_encode($response);
    }

    /**
     * @throws PropelException
     */
    public function changePaid()
    {

        $orderId = (int)$this->input->post('orderId');

        $model = SOrdersQuery::create()
            ->findPk($orderId);

        if ($model !== null) {
            if ($model->getPaid() == true) {
                $model->setPaid(false);
            } else {
                $model->setPaid(true);
            }

            $model->save();
            echo (int)$model->getPaid();

            $ordersesQuery = $this->db->query('SELECT amout FROM shop_user_profile WHERE user_id = ' . $model->getUserId());
            $orderses = $ordersesQuery->row();

            if ($model->getPaid() == 1) {

                $summAdd = $orderses->amout + $model->getTotalPrice() - Currency::create()->convert($model->getDiscount());
            } else {

                $summAdd = $orderses->amout - ($model->getTotalPrice() - Currency::create()->convert($model->getDiscount()));
            }

            $data = ['amout' => $summAdd];

            $this->db->where('user_id', $model->getUserId());
            $this->db->update('shop_user_profile', $data);
        }
    }

    /**
     * @throws PropelException
     */
    public function changeStatus()
    {

        $orderId = (int)$this->input->post('OrderId');
        $statusId = (int)$this->input->post('StatusId');

        $model = SOrdersQuery::create()
            ->findPk($orderId);

        $newStatusId = SOrderStatusesQuery::create()->setComment(__METHOD__)->findPk((int)$statusId);
        if (!empty($newStatusId)) {
            if ($model !== null) {
                $model->setStatus($statusId);

                $model->save();

                if ($this->input->post()) {
                    $model = new SOrderStatusHistory;
                    $this->form_validation->set_rules($model->rules());

                    if ($this->form_validation->run($this) == FALSE) {
                        showMessage(validation_errors());
                    } else {
                        $model->setOrderId($orderId)
                            ->setStatusId($statusId)
                            ->setUserId($this->dx_auth->get_user_id())
                            ->setDateCreated(time())
                            ->setComment($this->input->post('Comment'));

                        $model->save();

                        showMessage(lang('Order Status changed', 'admin'));
                    }
                }
            }
        }
    }

    /**
     * Create new order
     * @throws PropelException
     */
    public function create()
    {

        if (!$this->input->post()) {
            $this->render(
                'create',
                [
                    'categories' => ShopCore::app()->SCategoryTree->getTree(SCategoryTree::MODE_SINGLE),
                    'deliveryMethods' => SDeliveryMethodsQuery::create()->setComment(__METHOD__)->joinWithI18n($this->defaultLanguage['identif'], Criteria::INNER_JOIN)->orderBy('SDeliveryMethodsI18n.Name', Criteria::ASC)->find(),
                    'paymentMethods' => SPaymentMethodsQuery::create()->setComment(__METHOD__)->useI18nQuery($this->defaultLanguage['identif'])->orderByName(Criteria::ASC)->endUse()->find(),
                ]
            );
            exit;
        }

        if (!$this->input->post('shop_orders_products')) {
            showMessage(lang('Items not selected ', 'admin'), lang('Error', 'admin'), 'r');
            exit;
        }
        $this->form_validation->set_message('greater_than', lang('%s can not be less then 1', 'admin'));
        $this->form_validation->set_rules('shop_orders_products[quantity]', lang('Quantity', 'admin'), 'numeric|integer|greater_than[0]');

        if ($this->form_validation->run($this) == FALSE) {
            showMessage(validation_errors(), '', 'r');
            exit;
        }
        $shopOrder = $this->input->post('shop_orders');

        if ($shopOrder['user_id'] == null) {
            showMessage(lang('User is not selected', 'admin'), lang('Error', 'admin'), 'r');
            exit;
        }
        $this->input->post('action') == 'close' ? $action = $this->input->post('action') : $action = 'edit';
        $model = new SOrders;
        $model->setUserId($shopOrder['user_id'])
            ->setUserFullName($shopOrder['user_full_name'])
            ->setUserSurname($shopOrder['user_surname'])
            ->setUserEmail($shopOrder['user_email'])
            ->setUserPhone($shopOrder['user_phone'])
            ->setUserDeliverTo($shopOrder['user_delivery_to'])
            ->setTotalPrice($shopOrder['total_price'])
            ->setKey(self::createCode())
            ->setStatus(1);

        // Check if delivery method exists.
        $deliveryMethod = SDeliveryMethodsQuery::create()
            ->findPk((int)$shopOrder['delivery_method']);

        if ($deliveryMethod === null) {
            $deliveryMethod = 0;
            $deliveryPrice = 0;
        } else {
            $deliveryPrice = $deliveryMethod->getPrice();
            $deliveryMethod = $deliveryMethod->getId();
        }

        // Check if payment method exists.
        $paymentMethod = SPaymentMethodsQuery::create()
            ->findPk((int)$shopOrder['payment_method']);

        if ($paymentMethod === null) {
            $paymentMethod = 0;
        } else {
            $paymentMethod = $paymentMethod->getId();
        }

        $model->setDeliveryMethod($deliveryMethod)
            ->setDeliveryPrice($deliveryPrice)
            ->setPaymentMethod($paymentMethod)
            ->setDateCreated(time())
            ->setDateUpdated(time())
            ->save();

        $shopOrderProducts = $this->input->post('shop_orders_products');

        /** Collect order products data */
        $totalProducts = count($shopOrderProducts['product_id']);

        $orderId = $model->getId();

        $orderProducts = [];
        $orderProducts['order_id'] = $orderId;

        //тут рахується оригінальна ціна і ціна зі знижкою  і записуються дані в shop_order_products
        $origPrice = 0;
        $price = 0;
        for ($i = 0; $i < $totalProducts; $i++) {
            if ($shopOrderProducts['variant_name'][$i] == '-') {
                $shopOrderProducts['variant_name'][$i] = '';
            }

            $product = $this->db->where('id', $shopOrderProducts['variant_id'][$i])->get('shop_product_variants')->row();

            $data = [
                'order_id' => $orderId,
                'product_id' => $shopOrderProducts['product_id'][$i],
                'product_name' => $shopOrderProducts['product_name'][$i],
                'variant_id' => $shopOrderProducts['variant_id'][$i],
                'variant_name' => $shopOrderProducts['variant_name'][$i],
                'price' => $shopOrderProducts['price'][$i],
                'quantity' => $shopOrderProducts['quantity'][$i],
                'origin_price' => number_format($product->price, ShopCore::app()->SSettings->getPricePrecision(), '.', ''),
            ];

            $orderProducts['products'][] = $shopOrderProducts['product_id'][$i];
            $this->db->insert('shop_orders_products', $data);

            $man = Currency::create();
            $places = $man->getCurrencyDecimalPlaces($man->getMainCurrency()->getId());

            $origPrice += round($product->price, $places) * $shopOrderProducts['quantity'][$i];
            $price += $shopOrderProducts['price'][$i] * $shopOrderProducts['quantity'][$i]; //price with discount
        }

        $products = SProductsQuery::create()->setComment(__METHOD__)->filterById($orderProducts['products'], Criteria::IN)->find();

        foreach ($products as $product) {
            $product->setAddedToCartCount($product->getAddedToCartCount() + 1);
            $product->save();
        }

        $origPrice = strtr($origPrice, [',' => '.']);
        $price = strtr($price, [',' => '.']);

        //тут рахується знижка на не продукт і визначається яка більша
        $option = [
            'price' => $origPrice,
            'userId' => $shopOrder['user_id'],
            'ignoreCart' => 1,
            'reBuild' => 1,
        ];

        $discount = $this->load->module('mod_discount/discount_api')->getDiscount($option, TRUE);

        $discountProduct = $origPrice - $price;

        if ($discount['sum_discount_no_product'] > $discountProduct) {

            $model->setOriginPrice($origPrice)
                ->setTotalPrice($origPrice - $discount['sum_discount_no_product'])
                ->setDiscountInfo('user')
                ->setDiscount(strtr($discount['sum_discount_no_product'], [',' => '.']))
                ->save();

            if (isset($discount['max_discount']['key'])) {
                $query = $this->db->where('key', $discount['max_discount']['key'])->get('mod_shop_discounts');
                if ($query->num_rows() == 1) {
                    $res = $query->row_array();
                    $count_apply = $res['count_apply'] + 1;
                    $this->db->where('key', $discount['max_discount']['key'])->update('mod_shop_discounts', ['count_apply' => $count_apply]);
                }
            };
        } elseif ($discount['sum_discount_no_product'] <= $discountProduct) {

            $model->setOriginPrice($origPrice)
                ->setTotalPrice($price)
                ->setDiscountInfo('product')
                ->setDiscount(strtr($discountProduct, [',' => '.']))
                ->save();
        }
        $keyGift = $this->input->post('gift');
        if ($keyGift) {
            $model->reload();
            $priceForGift = $model->getTotalPrice(); //($dataOrderUpdate['total_price']) ? $dataOrderUpdate['total_price'] : $price;
            $gift = json_decode($this->load->module('mod_discount/discount_api')->getGiftCertificate($keyGift, $priceForGift));

            if (!$gift->error) {

                $model->setGiftCertKey($gift->key)
                    ->setTotalPrice($priceForGift - $gift->val_orig)
                    ->setOriginPrice($origPrice)
                    ->setGiftCertPrice($gift->val_orig)
                    ->save();

                $this->db->where('key', $gift->key)->update('mod_shop_discounts', ['active' => 0, 'count_apply' => 1]);
            }
        }

        $orderStatus = new SOrderStatusHistory();
        $orderStatus->setOrderId($orderId)
            ->setStatusId(1)
            ->setUserId($shopOrder['user_id'])
            ->setDateCreated(time())
            ->setComment('')
            ->save();

        if (!$orderStatus->getId()) {
            showMessage(lang('Order bad ', 'admin'), lang('Error', 'admin'), 'r');
        }

        $last_order = $this->db->order_by('id', 'desc')->get('shop_orders')->row();
        $last_order_id = $last_order->id;
        $totalPrice = $last_order->total_price;
        //Повторяется, потому что нужно значение total_price а на момент сохранения его нет.
        $deliveryMethod = SDeliveryMethodsQuery::create()
            ->findPk((int)$shopOrder['delivery_method']);

        if ($deliveryMethod === null) {
            $deliveryPrice = 0;
        } else {
            $freeFrom = (float)$deliveryMethod->getFreeFrom();

            if ($freeFrom > 0) {
                $deliveryPrice = $totalPrice > $freeFrom ? 0 : $deliveryMethod->getPrice();
            } elseif ($freeFrom == 0) {
                $deliveryPrice = $deliveryMethod->getPrice();
            }
        }
        $this->db->where('id', $last_order_id)->update('shop_orders', ['delivery_price' => $deliveryPrice]);

        /** Init Event. Create Shop order */
        Events::create()->registerEvent(
            [
                'order' => $model,
                'order_products' => $orderProducts,
            ],
            'ShopAdminOrder:create'
        );
        Events::runFactory();

        /** Prepare email data * */
        $checkLink = site_url("admin/components/run/shop/orders/createPdf/$orderId");
        $defaultCurrency = Currency::create()->getSymbol();

        $productsTable = (new Order\TableRenderer($model, $this->template))->render();
        $emailData = [
            'userName' => $model->getUserFullName(),
            'userEmail' => $model->getUserEmail(),
            'userPhone' => $model->getUserPhone(),
            'userDeliver' => $model->getUserDeliverTo(),
            'orderLink' => shop_url('order/view/' . $model->getKey()),
            'products' => $productsTable,
            'deliveryPrice' => $model->getDeliveryPrice() . ' ' . $defaultCurrency,
            'checkLink' => $checkLink,
            'totalPrice' => $totalPrice . ' ' . $defaultCurrency,
        ];

        /** Send email * */
        email::getInstance()->sendEmail($model->getUserEmail(), 'make_order', $emailData);

        $this->lib_admin->log(lang('Order is created', 'admin') . '. Id: ' . $last_order_id);

        showMessage(lang('Order was successfully created', 'admin'));

        switch ($action) {
            case 'edit':
                pjax('/admin/components/run/shop/orders/edit/' . $model->getId());
                break;

            case 'close':
                pjax('/admin/components/run/shop/orders');
                break;
        }
    }

    public function createNewUser()
    {

        $data = [
            'name' => $this->input->post('name'),
            'password' => $this->dx_auth->_gen_pass(),
            'email' => $this->input->post('email'),
            'phone' => $this->input->post('phone'),
            'address' => $this->input->post('address'),
        ];
        if (!$this->dx_auth->is_email_available($data['email'])) {
            echo 'email';
        } elseif ($this->dx_auth->register($data['name'], $data['password'], $data['email'], $data['address'], '', $data['phone'], false)) {

            Events::create()->registerEvent($data, 'ShopAdminOrder:createUser');
            Events::create()->runFactory();
            echo $this->getLastUserInfo();
        } else {
            echo 'false';
        }
    }

    /**
     * Return info about last created user
     */
    public function getLastUserInfo()
    {

        $response = $this->db->order_by('id', 'desc')->get('users')->row_array();

        if ($response) {
            echo json_encode($response);
        } else {
            echo 'false';
        }
    }

    /**
     * @param int $id
     */
    public function createPdf($id)
    {

        $this->printChecks([0 => $id]);
    }

    /**
     *
     * @param array $pks
     */
    public function printChecks(array $pks = [1])
    {

        $this->pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $this->pdf->setFontSubsetting(false);
        $this->pdf->cms_cache_key = 'check' . time();
        $this->pdf->SetFont('dejavusanscondensed', '', 10);
        $this->pdf->setPrintHeader(false);
        $this->pdf->setPrintFooter(false);
        $this->pdf->SetTextColor(0, 0, 0);

        foreach ($pks as $id) {
            $this->pageNumber = '';
            $model = SOrdersQuery::create()->setComment(__METHOD__)->findPk($id);
            $products = $model->getSOrderProductss();

            // Print product per page
            if ($products->count() >= $this->checkPerPage) {
                $products = array_chunk((array)$products->getData(), $this->checkPerPage);

                $this->countPage = count($products);
                $n = 1;
                foreach ($products as $p) {
                    $this->pageNumber = '/ ' . $n;
                    $this->createPDFPage($model, $p, true);
                    $this->countIterator = count($p) * $n + 1;
                    $n++;
                }
            } elseif ($products->count() > 5) {// Print product >5 on two pages.
                $this->createPDFPage($model, $products, true);
            } else {
                $this->createPDFPage($model, $products, true);
            }
        }

        $this->pdf->Output("Order_No_$id.pdf");
    }

    /**
     * @throws PropelException
     */
    public function delete()
    {

        $model = SOrdersQuery::create()
            ->findPk((int)$this->input->post('orderId'));

        if ($model) {
            $model->delete();
        }
    }

    /**
     * Edit order info
     * @param integer $id
     * @access public
     */
    public function edit($id)
    {

        $model = SOrdersQuery::create()
            ->findPk((int)$id);

        if (null === $model) {
            $this->error404(lang('Order not found', 'admin'));
        }
        $statusOldPaid = $model->getPaid();
        $paginationOrder = $this->session->userdata('order_url') ?: null;

        //in this case products count will be recounted
        $this->session->set_userdata(['recount' => true]);

        $statusHistory = SOrderStatusHistoryQuery::create()
            ->filterByOrderId((int)$id)
            ->orderByDateCreated(Criteria::ASC)
            ->find();

        $usersName = [];

        $this->load->model('dx_auth/users', 'users');

        foreach ($statusHistory as $status) {
            $query = $this->users->get_user_by_id($status->getUserId());
            if ($query AND $query->num_rows() == 1) {
                $row = $query->row();

                $usersName[$status->getId()] = ['name' => $row->username, 'role' => $row->role_id];
            }
        }

        $oldStatusId = SOrdersQuery::create()->setComment(__METHOD__)->filterById($id)->findOne()->getStatus();
        if ($this->input->post()) {

            $_POST['Paid'] = (bool)$this->input->post('Paid');
            $_POST['DateUpdated'] = time();
            $_POST['StatusId'] = $this->input->post('Status');

            $validation = $this->form_validation->set_rules('UserEmail');
            $validation = $model->validateCustomData($validation);
            if ($validation->run()) {
                $model->fromArray($this->input->post());

                // Check if delivery method exists.
                $deliveryMethod = SDeliveryMethodsQuery::create()
                    ->findPk((int)$this->input->post('shop_orders')['delivery_method']);
                if ($deliveryMethod === null) {
                    $deliveryMethodId = 0;
                    $deliveryPrice = 0;
                } else {
                    $freeFrom = (float)$deliveryMethod->getFreeFrom();
                    $deliveryPrice = ($model->getTotalPrice() >= $freeFrom && $freeFrom > 0) ? 0 : $deliveryMethod->getPrice();
                    $deliveryMethodId = $deliveryMethod->getId();
                }

                // Check if payment method exists.
                $paymentMethod = SPaymentMethodsQuery::create()
                    ->findPk((int)$this->input->post('shop_orders')['payment_method']);

                if ($paymentMethod === null) {
                    $paymentMethod = 0;
                } else {
                    $paymentMethod = $paymentMethod->getId();
                }

                $model->setDeliveryMethod($deliveryMethodId);
                $model->setDeliveryPrice($deliveryPrice);
                $model->setPaymentMethod($paymentMethod);

                $model->save();

                $this->recountUserAmount($model, $statusOldPaid);

                if ($oldStatusId != (int)$this->input->post('Status')) {
                    $modelOrder = new SOrderStatusHistory;
                    $this->form_validation->set_rules($modelOrder->rules());

                    if ($this->form_validation->run($this) == FALSE) {
                        showMessage(validation_errors(), '', 'r');
                    } else {
                        $modelOrder->setOrderId($id)
                            ->setStatusId($this->input->post('Status'))
                            ->setUserId($this->dx_auth->get_user_id())
                            ->setDateCreated(time())
                            ->setComment($this->input->post('Comment'));

                        $modelOrder->save();

                        $statusEmail = SOrderStatusesI18nQuery::create()
                            ->filterByLocale(MY_Controller::defaultLocale())
                            ->filterById((int)$this->input->post('Status'))
                            ->findOne();

                        if ($this->input->post('Notify')) {
                            email::getInstance()->sendEmail(
                                $model->getUserEmail(),
                                'change_order_status',
                                [
                                    'status' => $statusEmail->getName(),
                                    'comment' => $this->input->post('Comment'),
                                    'userName' => $model->getUserFullName(),
                                    'userEmail' => $model->getUserEmail(),
                                    'userPhone' => $model->getUserPhone(),
                                    'userDeliver' => $model->getUserDeliverTo(),
                                    'orderLink' => shop_url('order/view/' . $model->getKey()),
                                ]
                            );
                        }
                        showMessage(lang('Order status changed', 'admin'));
                    }
                }

                Events::create()->registerEvent(['model' => $model, 'old_paid' => $statusOldPaid]);
                Events::runFactory();

                $this->lib_admin->log(lang('Order edited', 'admin') . '. Id: ' . $id);

                showMessage(lang('Changes saved', 'admin'));

                if ($this->input->post('action') == 'edit') {
                    pjax('/admin/components/run/shop/orders/edit/' . $model->getId());
                } else {
                    pjax('/admin/components/run/shop/orders/' . $paginationOrder);
                }
            } else {
                showMessage(validation_errors(), '', 'r');
            }
        } else {
            $criteria = SOrderProductsQuery::create()->setComment(__METHOD__)->orderById(Criteria::ASC);
            foreach ($model->getSOrderProductss($criteria) as $sOrderProduct) {
                if ($sOrderProduct->getKitId() > 0) {
                    if (!isset($kits[$sOrderProduct->getKitId()]['total'])) {
                        $kits[$sOrderProduct->getKitId()]['total'] = 0;
                    }

                    if (!isset($kits[$sOrderProduct->getKitId()]['price'])) {
                        $kits[$sOrderProduct->getKitId()]['price'] = 0;
                    }

                    $kits[$sOrderProduct->getKitId()]['total']++;
                    $kits[$sOrderProduct->getKitId()]['price'] = $kits[$sOrderProduct->getKitId()]['price'] + $sOrderProduct->toCurrency();
                }
            }

            foreach ($model->getSOrderProductss() as $number) {
                $products = SProductVariantsQuery::create()->setComment(__METHOD__)->filterById($number->getVariantId())->find();
                if ($products[0]) {
                    $number->setVirtualColumn('number', $products[0]->getNumber());
                    $images[$number->getVariantId()] = $products[0]->getSmallPhoto();
                }
            }

            if ($model->getDeliveryMethod() != 0) {
                $freeFrom = SDeliveryMethodsQuery::create()->setComment(__METHOD__)->findPk($model->getDeliveryMethod())->getFreeFrom();
            } else {
                $freeFrom = 0;
            }

            $paymentMethods = ShopDeliveryMethodsSystemsQuery::create()->setComment(__METHOD__)->filterByDeliveryMethodId($model->getDeliveryMethod())->find();

            foreach ($paymentMethods as $paymentMethod) {
                $paymentMethodsId[] = $paymentMethod->getPaymentMethodId();
            }
            $paymentMethod = SPaymentMethodsQuery::create()->setComment(__METHOD__)->filterByActive(true)->joinWithI18n(MY_Controller::defaultLocale())->where('SPaymentMethods.Id IN ?', $paymentMethodsId)->orderByPosition()->find();

            $deliveryMethods = SDeliveryMethodsQuery::create()->setComment(__METHOD__)->joinWithI18n($this->defaultLanguage['identif'], Criteria::INNER_JOIN)->orderBy('SDeliveryMethodsI18n.Name', Criteria::ASC)->find();

            $this->render(
                'edit',
                [
                    'images' => $images,
                    'model' => $model,
                    'discount' => $model->getDiscount(),
                    'freeFrom' => $freeFrom,
                    'kits' => $kits,
                    'deliveryMethods' => $deliveryMethods,
                    'paymentMethods' => $paymentMethod,
                    'statusHistory' => $statusHistory,
                    'usersName' => $usersName,
                    'orderPagination' => $paginationOrder,
                    'allStatuses' => SOrders::getStatuses(),
                    'addField' => ShopCore::app()->CustomFieldsHelper->getCustomFields('order', $model->getId())->asAdminHtml(),
                ]
            );
        }
    }

    /**
     * @param int $orderId
     * @param int $kitId
     */
    public function editKit($orderId, $kitId)
    {

        $model = SOrdersQuery::create()
            ->findPk((int)$orderId);

        if (!$this->input->post()) {
            if ($model) {
                $criteria = SOrderProductsQuery::create()
                    ->filterByKitId((int)$kitId);
                $sOrderProducts = $model->getSOrderProductss($criteria);
            }

            $this->render(
                'editKitWindow',
                [
                    'sOrderProducts' => $sOrderProducts,
                    'orderId' => $orderId,
                    'kitId' => $kitId,
                ]
            );
        }
    }

    public function getImageName()
    {

        $variantId = $this->input->post('variantId');

        $variant = SProductVariantsQuery::create()->setComment(__METHOD__)->findPk($variantId);
        echo $variant->getSmallPhoto();
    }

    /**
     * Get Payment methods by delivery method id
     * @param integer $deliveryId
     */
    public function getPaymentsMethods($deliveryId)
    {

        $paymentMethods = ShopDeliveryMethodsSystemsQuery::create()->setComment(__METHOD__)->filterByDeliveryMethodId($deliveryId)->find();
        foreach ($paymentMethods as $paymentMethod) {
            $paymentMethodsId[] = $paymentMethod->getPaymentMethodId();
        }
        $paymentMethod = SPaymentMethodsQuery::create()->setComment(__METHOD__)->joinWithI18n(MY_Controller::defaultLocale(), Criteria::INNER_JOIN)->where('SPaymentMethods.Id IN ?', $paymentMethodsId)->orderByPosition()->find();
        $jsonData = [];

        /** @var SPaymentMethods $pm */
        foreach ($paymentMethod->getData() as $pm) {
            $jsonData[] = [
                'id' => $pm->getId(),
                'name' => $pm->getName(),
            ];
        }

        echo json_encode($jsonData);
    }

    public function index()
    {

        //////**********  Pagination pages **********\\\\\\\
        if ($this->input->get('per_page')) {
            $orderSession = [
                'order_url' => '?per_page=' . $this->input->get('per_page'),
            ];
            $this->session->set_userdata($orderSession);
        } else {
            $this->session->unset_userdata('order_url');
        }
        $offset = 0;

        $oldest_date_created = SOrdersQuery::create()
            ->setComment(__METHOD__)
            ->select(['oldest_date', 'newest_date'])
            ->withColumn('MIN(date_created)', 'oldest_date')
            ->withColumn('MAX(date_created)', 'newest_date')
            ->findOne();

        unset($pids);

        /** @var SOrdersQuery $model */
        $model = SOrdersQuery::create()
            ->setComment(__METHOD__)
            ->addSelectModifier('SQL_CALC_FOUND_ROWS');

        //--------------------------status_id----------------------//
        $model
            ->_if(is_numeric(ShopCore::$_GET['status_id']) && (ShopCore::$_GET['status_id'] !== '-- none --'))
            ->filterByStatus(ShopCore::$_GET['status_id'])
            ->_endif();

        //--------------------------order_id----------------------//
        $model
            ->_if(ShopCore::$_GET['order_id'])
            ->filterById(ShopCore::$_GET['order_id'])
            ->_endif();

        //--------------------------created_from----------------------//
        $model
            ->_if(ShopCore::$_GET['created_from'])
            ->where('FROM_UNIXTIME(' . SOrdersTableMap::COL_DATE_CREATED . ", '%Y-%m-%d') >= ?", date('Y-m-d', strtotime(ShopCore::$_GET['created_from'])))
            ->_endif();

        //--------------------------created_to----------------------//
        $model
            ->_if(ShopCore::$_GET['created_to'])
            ->where('FROM_UNIXTIME(' . SOrdersTableMap::COL_DATE_CREATED . ", '%Y-%m-%d') <= ?", date('Y-m-d', strtotime(ShopCore::$_GET['created_to'])))
            ->_endif();

        //--------------------------product_id----------------------//
        $model
            ->_if($this->input->get('product_id'))
            ->useSOrderProductsQuery()
            ->filterByProductId($this->input->get('product_id'))
            ->endUse()
            ->_endif();

        //--------------------------product----------------------//
        $model
            ->_if($this->input->get('product'))
            ->useSOrderProductsQuery()
            ->filterByProductName('%' . $this->input->get('product') . '%', Criteria::LIKE)
            ->_or()
            ->filterByVariantName('%' . $this->input->get('product') . '%', Criteria::LIKE)
            ->endUse()
            ->_endif();

        //--------------------------customer----------------------//
        $model
            ->_if($this->input->get('customer'))
            ->filterByUserFullName('%' . $this->input->get('customer') . '%', Criteria::LIKE)
            ->_or()
            ->filterByUserEmail('%' . $this->input->get('customer') . '%', Criteria::LIKE)
            ->_or()
            ->filterByUserPhone('%' . $this->input->get('customer') . '%', Criteria::LIKE)
            ->_endif();

        //--------------------------amount_from----------------------//
        $model
            ->_if($this->input->get('amount_from'))
            ->filterByTotalPrice($this->input->get('amount_from'), Criteria::GREATER_EQUAL)
            ->_endif();

        //--------------------------amount_to----------------------//
        $model
            ->_if($this->input->get('amount_to'))
            ->filterByTotalPrice($this->input->get('amount_to'), Criteria::LESS_EQUAL)
            ->_endif();

        //--------------------------paid----------------------//
        $model
            ->_if($this->input->get('paid') === '0')
            ->filterByPaid(null, Criteria::ISNULL)
            ->_or()
            ->filterByPaid(0)
            ->_elseif($this->input->get('paid') === '1')
            ->filterByPaid(1)
            ->_endif();

        // Count total orders
        if ((ShopCore::$_GET['orderMethod'] !== ''
                && ShopCore::$_GET['orderCriteria'] !== ''
                && method_exists($model, 'filterBy' . ShopCore::$_GET['orderMethod']))
            or ShopCore::$_GET['orderMethod'] === 'Id'
        ) {

            switch (ShopCore::$_GET['orderCriteria']) {
                case 'ASC':
                    $nextOrderCriteria = 'DESC';
                    $model->orderBy(ShopCore::$_GET['orderMethod'], Criteria::ASC);
                    break;

                case 'DESC':
                    $nextOrderCriteria = 'ASC';
                    $model->orderBy(ShopCore::$_GET['orderMethod'], Criteria::DESC);
                    break;

                default:
                    $model->orderById(Criteria::DESC);
            }
        } else {
            $model->orderById(Criteria::DESC);
        }

        $this->perPage = $this->paginationVariant($this->session->userdata('pagination'));

        try {
            $model = $model
                ->limit($this->perPage)
                ->offset((int)ShopCore::$_GET['per_page'])
                ->distinct()
                ->find();

        } catch (PropelException $e) {
            $this->lib_admin->log($e->getPrevious()->getMessage());
        } catch (Exception $e) {
            $this->lib_admin->log($e->getMessage());
        }

        $totalOrders = $this->getTotalRow();

        $usersDatas = [];
        $productDatas = [];
        $pids = [];

        /** @var SOrders $o */
        foreach ($model as $o) {
            $usersDatas[] = $o->getUserFullName();
            $usersDatas[] = $o->getUserEmail();
            $usersDatas[] = $o->getUserPhone();

            if ($o->getSOrderProductss()) {
                foreach ($o->getSOrderProductss() as $p) {
                    if (is_object($p)) {
                        if (!in_array($p->getProductId(), $pids)) {
                            $pids[] = $p->getProductId();
                            $productDatas[] = [
                                'v' => $p->getProductId(),
                                'label' => $p->getProductName(),
                            ];
                        }
                    }
                }
            }
        }

        $getData = ShopCore::$_GET ?: [];
        unset($getData['per_page']);
        $queryString = '?' . http_build_query($getData);

        $orderStatuses = SOrderStatusesQuery::create()
            ->joinWithI18n(MY_Controller::defaultLocale(), Criteria::LEFT_JOIN)
            ->orderByPosition(Criteria::ASC)
            ->find();

        $this->load->library('Pagination');
        $config['base_url'] = site_url('admin/components/run/shop/orders/index/?') . http_build_query(ShopCore::$_GET ?: []);

        $config['container'] = 'shopAdminPage';
        $config['uri_segment'] = $this->uri->total_segments();
        $config['page_query_string'] = true;
        $config['total_rows'] = $totalOrders;
        $config['per_page'] = $this->perPage;

        $config['separate_controls'] = true;
        $config['full_tag_open'] = '<div class="pagination pull-left"><ul>';
        $config['full_tag_close'] = '</ul></div>';
        $config['controls_tag_open'] = '<div class="pagination pull-right"><ul>';
        $config['controls_tag_close'] = '</ul></div>';
        $config['next_link'] = lang('Forward&nbsp;', 'admin');
        $config['prev_link'] = lang('&nbsp;Back', 'admin');

        $config['cur_tag_open'] = '<li class="btn-primary active"><span>';
        $config['cur_tag_close'] = '</span></li>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['num_tag_close'] = '</li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';

        $this->pagination->num_links = 5;
        $this->pagination->initialize($config);
        $this->template->assign('pagination', $this->pagination->create_links_ajax());

        ShopCore::$_GET['status'] = -1;
        $this->render(
            'list',
            [
                'oldest_order_date' => $oldest_date_created,
                'model' => $model,
                'totalOrders' => $totalOrders,
                'perPage' => $this->perPage,
                'nextOrderCriteria' => $nextOrderCriteria,
                'paginationVariant' => $this->paginationVariant($this->session->userdata('pagination')),
                'queryString' => $queryString,
                'deliveryMethods' => SDeliveryMethodsQuery::create()->setComment(__METHOD__)->find(),
                'paymentMethods' => SPaymentMethodsQuery::create()->setComment(__METHOD__)->find(),
                'orderStatuses' => $orderStatuses,
                'usersDatas' => array_unique($usersDatas),
                'productsDatas' => $productDatas,
                'offset' => $offset,
            ]
        );
    }

    /**
     *
     * @param boolean|integer $int
     * @param boolean $ref
     * @return integer
     */
    public function paginationVariant($int = FALSE, $ref = FALSE)
    {

        if ($int == FALSE OR $int == NULL) {
            $this->session->set_userdata(['pagination' => 12]);
        } else {
            $this->session->set_userdata(['pagination' => $int]);
        }

        if ($ref == TRUE) {
            pjax('/admin/components/run/shop/orders');
        }

        return $this->session->userdata('pagination');
    }

    /**
     * @return mixed
     */
    private function getTotalRow()
    {

        $connection = Propel::getConnection('Shop');
        $statement = $connection->prepare('SELECT FOUND_ROWS() as `number`');
        $statement->execute();
        $resultset = $statement->fetchAll();
        return $resultset[0]['number'];
    }

    /**
     * Count total orders in the list
     *
     * @param SOrdersQuery $object
     * @return int
     */
    protected function _count(SOrdersQuery $object)
    {

        $object = clone $object;
        return $object->count();
    }

}