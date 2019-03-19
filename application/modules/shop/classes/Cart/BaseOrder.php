<?php

namespace Cart;

use Currency\Currency;
use Exception;
use Propel\Runtime\Exception\PropelException;
use SDeliveryMethodsQuery;
use ShopController;
use ShopCore;
use SOrderProducts;
use SOrders;
use SOrderStatusHistory;

/**
 *
 *
 * @author
 */
class BaseOrder extends ShopController
{

    /**
     * Create new order
     *
     * @param null $data
     * @param null $products
     * @return SOrders
     * @throws Exception
     * @throws PropelException
     */
    public static function create($data = null, &$products = null) {

        if ($data == null) {
            throw new Exception("Can't create empty order");
        }

        $order = new SOrders;

        $order->setUserId($data['userId']);
        $order->setKey(createOrderCode());
        $order->setDeliveryMethod($data['deliveryMethodId']);
        $order->setDeliveryPrice($data['deliveryPrice']);
        $order->setPaymentMethod($data['paymentMethodId']);
        $order->setStatus(1);
        $order->setUserFullName($data['userFullName']);
        $order->setUserSurname($data['userSurname']);
        $order->setUserEmail($data['userEmail']);
        $order->setUserPhone($data['userPhone']);
        $order->setUserDeliverTo($data['userDeliverTo']);
        $order->setUserComment($data['userCommentText']);
        $order->setDateCreated(time());
        $order->setDateUpdated(time());
        $order->setUserIp($data['userIp']);

        /** Get cart items * */
        $cart = BaseCart::getInstance();

        $items = $cart->getItems();

        /** Add products * */
        foreach ($items['data'] as $cartItem) {

            $product = [
                        'quantity' => $cartItem->quantity,
                        'price'    => $cartItem->price,
                       ];

            if ($cartItem->instance == 'SProducts') {

                $model = $cartItem->getSProducts();

                $model->setAddedToCartCount($model->getAddedToCartCount() + 1);
                $model->save();

                $orderedItem = new SOrderProducts;

                //$product['variant_name'] = $cartItem->getName();

                $orderedItem->fromArray(
                    [
                     'ProductId'   => $model->getId(),
                     'VariantId'   => $cartItem->getId(),
                     'ProductName' => $model->getName(),
                     'VariantName' => $cartItem->getName(),
                     'Quantity'    => $cartItem->quantity,
                    ]
                );

                $orderedItem->fromArray(['Price' => $cartItem->price, 'OriginPrice' => $cartItem->originPrice]);

                $order->addSOrderProducts($orderedItem);
            } elseif ($cartItem->instance == 'ShopKit') {

                $model = $cartItem;

                /** Adding main product of kit to the order * */
                $mp = $model->getMainProduct();
                $mp->setAddedToCartCount($mp->getAddedToCartCount() + $cartItem->quantity);
                $mp->save();

                /** @var \SProductVariants $mpV */
                $mpV = $mp->getFirstVariant('kit');

                $product['variant_name'] = $mp->getName();

                $orderedItem = new SOrderProducts;
                $orderedItem->fromArray(
                    [
                     'KitId'       => $model->getId(),
                     'ProductId'   => $mp->getId(),
                     'VariantId'   => $mpV->getId(),
                     'ProductName' => $mp->getName(),
                     'VariantName' => $mpV->getName(),
                     'Quantity'    => $cartItem->quantity,
                     'IsMain'      => TRUE,
                    ]
                );

                $mpV->reload();
                $orderedItem->fromArray(['Price' => $mpV->getPrice(), 'OriginPrice' => $mpV->getOriginPrice()]);

                $order->addSOrderProducts($orderedItem);

                /** Adding atached products of kit to the order * */
                foreach ($model->getShopKitProducts() as $shopKitProduct) {
                    $ap = $shopKitProduct->getSProducts();
                    $ap->setAddedToCartCount($ap->getAddedToCartCount() + 1);
                    $ap->save();
                    $apV = $ap->getKitFirstVariant($shopKitProduct);

                    $orderedItem = new SOrderProducts;
                    $orderedItem->fromArray(
                        [
                         'KitId'       => $model->getId(),
                         'ProductId'   => $ap->getId(),
                         'VariantId'   => $apV->getId(),
                         'ProductName' => $ap->getName(),
                         'VariantName' => $apV->getName(),
                         'Quantity'    => $cartItem->quantity,
                         'IsMain'      => FALSE,
                        ]
                    );

                    /** @var \SProductVariants $apV */
                    $orderedItem->fromArray(['Price' => $apV->getPrice(), 'OriginPrice' => $apV->getVirtualColumn('origPrice')]);

                    $order->addSOrderProducts($orderedItem);
                }
            }

            $products[] = $product;
        }

        $order->setTotalPrice($cart->getTotalPrice());
        $order->setOriginPrice($cart->getOriginTotalPrice());
        if ($cart->gift_info) {
            $order->setGiftCertKey($cart->gift_info);
            $order->setGiftCertPrice($cart->gift_value);
        }
        if ($cart->getOriginTotalPrice() > $cart->getTotalPrice()) {
            $discount = $cart->getOriginTotalPrice() - $cart->getTotalPrice();
            if (!empty($cart->gift_value)) {
                $discount -= $cart->gift_value;
            }
            $order->setDiscount($discount > 0 ? $discount : null);
            $order->setDiscountInfo($cart->discount_type);
        }

        self::checkOrderMinPrice($order);

        /** Try to save order * */
        if ($order->save()) {
            /** Clear cart and return order* */
            $cart->removeAll();
            return $order;
        } else {
            throw new Exception('Error creating new order');
        }
    }

    /**
     * Check if order price do not exceed order min price
     * @param SOrders $order - order model
     * @return bool
     * @throws Exception
     */
    private static function checkOrderMinPrice($order) {

        $orderMinPrice = ShopCore::app()->SSettings->getOrdersMinimumPrice() ?: null;

        if (!$orderMinPrice) {
            return true;
        }

        if ($orderMinPrice > $order->getOriginPrice()) {
            throw new Exception(
                langf(
                    'Cannot create order with price less than |currencySymbol| |minPrice|',
                    'main',
                    [
                     'minPrice'       => $orderMinPrice,
                     'currencySymbol' => ShopCore::app()->SCurrencyHelper->getSymbol(),
                    ]
                )
            );
        }
    }

    /**
     * Get delivery methods
     * @deprecated 4.9 use SDeliveryMethodsQuery
     * @param null|int $id
     * @return array
     */
    public function getDeliveryMethods($id = null) {

        $deliveryMethods = !$id ? SDeliveryMethodsQuery::create()->setComment(__METHOD__)->getMethods() : SDeliveryMethodsQuery::create()->setComment(__METHOD__)->getMethod($id);

        return $deliveryMethods ?: [];
    }

    /**
     * Create random code.
     *
     * @deprecated since 4.9
     * @return string
     * @access public
     */
    public static function createCode() {
        return createOrderCode();
    }

    /**
     * Save order history
     *
     * @param $orderId
     * @param $userId
     * @param null $comment
     * @return SOrderStatusHistory
     * @throws Exception
     * @throws PropelException
     */
    public function saveOrdersHistory($orderId, $userId, $comment = null) {

        /** Save to order statuses history table * */
        $orderStatus = new SOrderStatusHistory;
        $orderStatus->setOrderId($orderId);
        $orderStatus->setStatusId(1);
        $orderStatus->setUserId($userId);
        $orderStatus->setDateCreated(time());
        $orderStatus->setComment($comment);

        if ($orderStatus->save()) {
            return $orderStatus;
        } else {
            throw new Exception('Error saving order history');
        }
    }

}