<?php

use Base\SOrders as BaseSOrders;
use Currency\Currency;
use Map\SOrderProductsTableMap;
use Map\SOrdersTableMap;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Connection\ConnectionWrapper;
use Propel\Runtime\Exception\PropelException;

/**
 * Skeleton subclass for representing a row from the 'shop_orders' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.Shop
 */
class SOrders extends BaseSOrders
{

    /**
     * @var bool
     */
    private $changedPaidStatus;

    /**
     * @var string
     */
    public $entityName = 'order';

    /**
     * @var null
     */
    private $_kitsProducts = null;

    /**
     * @param string $filterField
     * @param int $filterValue
     * @return string
     */
    public static function getStatusName($filterField = '', $filterValue = 0) {

        $orderStatus = SOrderStatusesQuery::create()->setComment(__METHOD__)->orderByPosition(Criteria::ASC)->joinWithI18n(MY_Controller::getCurrentLocale());
        if ($filterField != '' && $filterValue !== 0) {
            $orderStatus = $orderStatus->filterBy($filterField, $filterValue);
        }
        $orderStatus = $orderStatus->findOne();
        return $orderStatus->getName();
    }

    /**
     * @return mixed
     */
    public static function getStatuses() {

        foreach (SOrderStatusesQuery::create()->setComment(__METHOD__)->joinWithI18n(MY_Controller::getCurrentLocale())->orderByPosition(Criteria::ASC)->find() as $orderStatus) {
            $orderStatuses[0][$orderStatus->getId()] = $orderStatus;
            $orderStatuses[1][$orderStatus->getId()] = $orderStatus->getName();
        }

        return $orderStatuses;
    }

    /**
     * @param MY_Form_validation $validator
     * @return MY_Form_validation
     */
    public function validateCustomData($validator) {

        if (!empty($this->entityName)) {
            $this->collectCustomData($this->entityName, $this->getId());
            if ($this->hasCustomData !== false) {
                foreach (CI::$APP->input->post('custom_field') as $key_post => $value_post) {
                    foreach ($this->customFields as $key => $value) {
                        if ((int) $key_post == $value['Id']) {

                            $validator_str = '';
                            if ($value['IsRequired'] && $this->curentPostEntitySave($key)) {
                                $validator_str = 'required';
                            }
                            if ($value['Validators'] && $this->curentPostEntitySave($key)) {
                                $validator_str .= '|' . $value['Validators'];
                            }
                            $validator->set_rules("custom_field[$key]", $value['CustomFieldsI18ns'][0]['FieldLabel'], $validator_str);
                        }
                    }
                }
            }
        }
        return $validator;
    }

    /**
     * @param string $entityName
     * @param int $id
     * @return bool
     */
    public function collectCustomData($entityName, $id) {

        $this->entityId = $id;
        $this->customFields = CustomFieldsQuery::create()->setComment(__METHOD__)->joinWithI18n(\MY_Controller::getCurrentLocale())->filterByIsActive(1)->filterByEntity($entityName)->find()->toArray($keyColumn = 'id');
        if (count($this->customFields)) {
            $this->hasCustomData = true;
        } else {
            return false;
        }
    }

    public function attributeLabels() {

        return [
                'Key'            => ShopCore::t('Ключ'),
                'DeliveryMethod' => ShopCore::t('Метод доставки'),
                'DeliveryPrice'  => ShopCore::t('Цена доставки'),
                'PaymentMethod'  => ShopCore::t('Метод оплаты'),
                'Status'         => ShopCore::t('Статус'),
                'StatusComment'  => ShopCore::t('Комментарий к изменению статуса'),
                'Paid'           => ShopCore::t('Оплачен'),
                'UserFullName'   => ShopCore::t('Полное Имя'),
                'UserEmail'      => ShopCore::t('Почта'),
                'UserPhone'      => ShopCore::t('Номер телефона'),
                'UserDeliverTo'  => ShopCore::t('Адрес доставки'),
                'UserComment'    => ShopCore::t('Комментарий'),
                'DateCreated'    => ShopCore::t('Дата создания'),
                'DateUpdated'    => ShopCore::t('Дата обновления'),
                'UserIp'         => ShopCore::t('Ip'),
               ];
    }

    public function getTotalPriceWithGift() {

        return ($this->getTotalPriceWithDelivery() - $this->getGiftCertPrice()) >= 0 ? $this->getTotalPriceWithDelivery() - $this->getGiftCertPrice() : 0;
    }

    public function getTotalPriceWithDelivery() {

        return Currency::create()->convert(($this->getTotalPrice() + $this->getDeliveryPrice()));
    }

    /**
     * Get total price for order
     *
     * @param null|string $CS
     * @return int
     */
    public function getTotalPrice($CS = null) {

        return Currency::create()->convert(parent::getTotalPrice(), $CS);
    }

    public function updateTotalPrice() {

        $totalPrice = 0;

        foreach ($this->getSOrderProductss() as $p) {
            $totalPrice = $totalPrice + $p->getPrice() * $p->getQuantity();
        }
        $this->setTotalPrice($totalPrice);
    }

    public function updateOriginPrice() {

        $originPrice = 0;

        foreach ($this->getSOrderProductss() as $p) {
            $originPrice = $originPrice + $p->getOriginPrice() * $p->getQuantity();
        }
        $this->setOriginPrice($originPrice);
    }

    public function updateDiscount() {

        $discount = $this->getOriginPrice() - $this->getTotalPrice();
        $discount = $discount > 0 ? $discount : 0;
        $this->setDiscount($discount);
    }

    public function incrementTotalPrice($price, $inc) {

        $this->setTotalPrice($price + $inc);
        $this->save();
    }

    public function getOrderKits() {

        if (!is_array($this->_kitsProducts)) {
            $this->getOrderProducts();
        }
        return $this->_kitsProducts;
    }

    public function getOrderProducts() {

        $this->_kitsProducts = [];
        $criteria = SOrderProductsQuery::create()->setComment(__METHOD__)->useSProductsQuery()->orderByViews(Criteria::ASC)->endUse();
        $model = $this->getSOrderProductss($criteria);

        foreach ($model as $key => $value) {
            $value->setVirtualColumn('productTotalPrice', Currency::create()->convert($value->quantity * $value->price));

            if (!$value->getKitId() && $this->getDiscountInfo() == 'user') {
                $value->setPrice($value->getOriginPrice());
            }

            if ($value->getKitId()) {
                if ($value->getIsMain()) {

                    $value->setVirtualColumn('Kit', ShopKitQuery::create()->findPk($value->getKitId()));

                    $value->setVirtualColumn(
                        'TotalKitPrice',
                        (float) SOrderProductsQuery::create()
                            ->withColumn('sum(' . SOrderProductsTableMap::COL_PRICE . ')', 'final_price')
                            ->filterByKitId($value->getKitId())
                            ->select(['final_price'])
                            ->findOneByOrderId($this->getId())
                    );

                    $this->_kitsProducts[] = $value;
                }
                $model->remove($key);
            }
        }
        return $model;
    }

    public function updateDeliveryPrice() {

        $delivery = $this->getDeliveryMethod();
        if (!empty($delivery)) {
            $totalPrice = (int) $this->getTotalPrice();
            $freeFrom = (int) $this->getSDeliveryMethods()->getFreeFrom();

            $deliveryPrice = ($totalPrice > $freeFrom && $freeFrom > 0) ? 0 : $this->getSDeliveryMethods()->getPrice();
            $this->setDeliveryPrice($deliveryPrice);
        }
    }

    public function postSave() {

        $this->hasCustomData = false;
        $this->customFields = false;
        if ($this->hasCustomData === false) {
            $this->collectCustomData($this->entityName, $this->getId());
        }
        $this->saveCustomData();

        parent::postSave();
    }

    /**
     * @return string
     * @throws PropelException
     */
    public function getPaymentMethodName() {

        return $this->getSPaymentMethods() ? $this
            ->getSPaymentMethods()
            ->setLocale(MY_Controller::getCurrentLocale())
            ->getName() : '';
    }

    /**
     * @return string
     * @throws PropelException
     */
    public function getDeliveryMethodName() {

        return $this->getSDeliveryMethods() ? $this
            ->getSDeliveryMethods()
            ->setLocale(MY_Controller::getCurrentLocale())
            ->getName() : '';
    }

    /**
     * if recount option is switched on
     * needs $_SESSION['recount'] variable to be set in controller
     * $_SESSION['recount'] is set when paid option is switched on in orders.php->edit method
     * @param ConnectionWrapper $con
     * @return bool
     */
    public function preUpdate(ConnectionWrapper $con = null) {
        $this->changedPaidStatus = in_array(SOrdersTableMap::COL_PAID, $this->getModifiedColumns(), true);

        return parent::preUpdate($con);
    }

    /**
     * @param ConnectionWrapper|null $con
     * @return bool|void
     */
    public function postUpdate(ConnectionWrapper $con = null) {

        if (ShopCore::app()->SSettings->getOrdersRecountGoods() == 1 && $this->changedPaidStatus) {
            $productsInCart = SOrderProductsQuery::create()->setComment(__METHOD__)->filterByOrderId($this->getId())->find();
            if (count($productsInCart) > 0) {
                foreach ($productsInCart as $productInCart) {
                    $variantToUpdate = SProductVariantsQuery::create()->setComment(__METHOD__)->findPk($productInCart->getVariantId());
                    if (!$variantToUpdate) {
                        continue;
                    }
                    if ($this->getPaid()) {
                        if ($variantToUpdate->getStock() <= $productInCart->getQuantity()) {
                            $stock = 0;
                            $variantToUpdate->setStock($stock);
                        } else {
                            $variantToUpdate->setStock($variantToUpdate->getStock() - $productInCart->getQuantity());
                        }
                    } else {
                        $variantToUpdate->setStock($variantToUpdate->getStock() + $productInCart->getQuantity());
                    }
                    $variantToUpdate->save();
                }
            }
        }
        return parent::postUpdate($con);
    }

    /**
     *
     * @param string|bool|false $locale
     * @return array CustomFields
     */
    public function getCustomFields($locale = false) {

        return CustomFieldsQuery::create()->setComment(__METHOD__)->getSOrderFields($this->getId(), $locale);
    }

    /**-----------------------------------------------------------------------------------------------------------------
     *                                                                                                        Addition
     *
     */

    /**
     * Price with discount, gift and delivery
     *
     * @return float
     */
    public function getFinalPrice() {

        return parent::getTotalPrice() + $this->getDeliveryPrice();
    }

    /**
     * gift + discount
     *
     * @return float
     */
    public function getTotalDiscountValue() {

        return $this->getDiscountValue() + $this->getGiftValue();
    }

    /**
     * only discount
     *
     * @return float
     */
    public function getDiscountValue() {

        return $this->getDiscount();
    }

    /**
     * only gift
     *
     * @return float
     */
    public function getGiftValue() {

        return $this->getGiftCertPrice();
    }

}