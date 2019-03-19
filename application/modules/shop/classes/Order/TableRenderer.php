<?php
namespace Order;

use Propel\Runtime\Collection\ObjectCollection;
use SDeliveryMethodsQuery;
use ShopCore;
use SOrderProducts;
use SOrders;
use Template;


/**
 * Class TableRenderer
 * @package Order
 */
class TableRenderer
{

    /**
     * @var SOrders
     */
    private $orderModel;

    /**
     * @var Template
     */
    private $engine;

    /**
     * @var string
     */
    private $templatePath;

    /**
     * @var int
     */
    private $pageNumber = 0;

    /**
     * @var int
     */
    private $countPages = 0;

    /**
     * @var int
     */
    private $countIterator = 1;

    /**
     * TableRenderer constructor.
     * @param SOrders $orderModel
     * @param Template $engine
     */
    public function __construct(SOrders $orderModel, Template $engine) {
        $this->engine = $engine;
        $this->orderModel = $orderModel;
        $this->templatePath = 'file:' . APPPATH . 'modules/shop/admin/templates/orders/products_table.tpl';
    }

    /**
     * @param string $templatePath
     * @return TableRenderer
     */
    public function setTemplatePath($templatePath) {
        $this->templatePath = $templatePath;
        return $this;
    }

    /**
     * @param int $pageNumber
     * @return TableRenderer
     */
    public function setPageNumber($pageNumber) {
        $this->pageNumber = $pageNumber;
        return $this;
    }

    /**
     * @param int $countPage
     * @return TableRenderer
     */
    public function setCountPages($countPage) {
        $this->countPages = $countPage;
        return $this;
    }

    /**
     * @param int $countIterator
     * @return TableRenderer
     */
    public function setCountIterator($countIterator) {
        $this->countIterator = $countIterator;
        return $this;
    }

    /**
     * @param ObjectCollection|null $products
     * @return string
     */
    public function render($products = null) {

        $model = $this->orderModel;

        $products = $products ?: $model->getSOrderProductss();

        if ($model->getDeliveryMethod()) {
            $freeFrom = SDeliveryMethodsQuery::create()->setComment(__METHOD__)->findPk($model->getDeliveryMethod())->getFreeFrom();
            if ($freeFrom > 0) {
                $deliveryPrice = ($model->getTotalPrice() < $freeFrom) ? $model->getDeliveryPrice() : 0;
            } else {
                $deliveryPrice = $model->getDeliveryPrice();
            }
        } else {
            $deliveryPrice = 0;
        }

        $totalPrice = $model->getTotalPrice() + $deliveryPrice;

        if ($deliveryPrice > 0) {
            $delivery = new SOrderProducts();
            $delivery->setProductName(lang('Delivery', 'admin'));
            $delivery->setQuantity(1);
            $delivery->setPrice($deliveryPrice);
            $delivery->setOriginPrice($deliveryPrice);
        }

        $gift = round($model->getGiftCertPrice(), ShopCore::app()->SSettings->getPricePrecision(), PHP_ROUND_HALF_DOWN);

        $data = [
                 'model'      => $model,
                 'products'   => $products,
                 'totalPrice' => $totalPrice,
                 'pageNumber' => $this->pageNumber,
                 'countPage'  => $this->countPages,
                 'iterator'   => $this->countIterator,
                 'delivery'   => $delivery,
                 'gift'       => $gift,
                ];

        $fetched = $this->engine->fetch($this->templatePath, $data);

        return $fetched;
    }

}