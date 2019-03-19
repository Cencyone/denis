<?php

namespace VariantPriceType;

use Cart\CartItem;
use CMSFactory\DependencyInjection\DependencyInjectionProvider;
use Currency\Currency;
use Doctrine\Common\Cache\CacheProvider;
use DX_Auth;
use Map\SProductVariantPriceTypeTableMap;
use MY_Controller;
use Propel\Runtime\Exception\PropelException;
use SProductVariantPrice;
use SProductVariantPriceQuery;
use SProductVariantPriceTypeQuery;
use SProductVariants;

/**
 * Class BaseVariantPriceType
 * @package VariantPriceType
 */
class BaseVariantPriceType
{

    const PRICE_TYPE_PERCENT = 2;

    /**
     * @var BaseVariantPriceType
     */
    private static $_instance;

    /**
     * @var bool
     */
    private $login = false;

    /**
     * @var null|int
     */
    private static $user_role_id = null;

    /**
     * @var int
     */
    private $variant_id;

    /**
     * @var null|int
     */
    private $type_id = null;

    /**
     * @var bool
     */
    private $useConsiderDiscount = true;

    /**
     * @var float
     */
    public $price;

    /**
     * BaseVariantPriceType constructor.
     */
    private function __construct() {

    }

    /**
     * @return BaseVariantPriceType
     */
    public static function create() {

        if (self::$_instance === null) {

            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * @param int $variant_id
     * @param null|int $add_price_id
     * @return $this
     */
    public function setConfig($variant_id, $add_price_id = null) {

        $this->setVariantId($variant_id);

        /** Если передан тип цены используем только ее */
        if ($add_price_id != null) {
            $this->setTypeId($add_price_id);
            return $this;

        }

        if ($this->login = $this->getDxAuth()->is_logged_in()) {

            /** Set */
            $this->setUserRoleId($this->getUserRoleId() ?: $this->getDxAuth()->get_role_id());
        } else {

            $this->setUserRoleId('-1');
        }

        return $this;

    }

    /**
     * @return $this
     */
    public function setPriceType() {

        $price = false;

        if ($this->getTypeId()) {

            $price = $this->getPriceForParameters('type_id');

        } elseif ($this->isLogin() == false) {

            $price = $this->getPriceForParameters('not_authorized');

        } elseif ($this->getUserRoleId() != null) {

            $price = $this->getPriceForParameters('role_group');

        }

        $this->setPrice($price);

        return $this;
    }

    /**
     * @return float|bool
     */
    public function getPrice() {

        return $this->price;
    }

    /**
     * @param string $param
     * @return bool|float
     */
    private function getPriceForParameters($param) {

        $cache_key = $this->generateCacheKey($this->getVariantId(), $this->getUserRoleId());

        /** @var CacheProvider $cache */
        $cache = DependencyInjectionProvider::getContainer()->get('cache');

        if ($cache->contains($cache_key)) {

            $data = $cache->fetch($cache_key);

        } else {

            $data = SProductVariantPriceQuery::create()
                ->useSProductVariantPriceTypeQuery()
                ->filterByStatus(1)
                ->_if($param == 'type_id')
                ->filterById($this->getTypeId())
                ->_else()
                ->useSProductVariantPriceTypeValueQuery()
                ->_if($param == 'role_group')
                ->filterByValue($this->getUserRoleId())
                ->_else()
                ->filterByValue('-1')
                ->_endif()
                ->endUse()
                ->_endif()
                ->endUse()
                ->findOneByVarId($this->getVariantId());

            $cache->save($cache_key, $data, config_item('cache_ttl'));

        }

        if ($data) {

            /** учитывать или не учитывать скидку */
            $this->setUseConsiderDiscount($data->getSProductVariantPriceType()->getConsiderDiscount());

            $main_price = $this->getPriceToCurrency($data);

            return $main_price;
        }

        return false;
    }

    /**
     * @param int $var_id
     * @param int $role_id
     * @return string
     */
    private function generateCacheKey($var_id, $role_id) {

        $data = [
                 'id'      => $var_id,
                 'role'    => $role_id,
                 'locale'  => MY_Controller::getCurrentLocale(),
                 'type_id' => $this->getTypeId() ?: '',
                ];

        $key = md5(implode('_', $data));

        return $key;
    }

    /**
     * @param SProductVariantPrice $data
     * @return float
     */
    private function getPriceToCurrency(SProductVariantPrice $data) {

        if ($data->getSProductVariantPriceType()->getPriceType() == self::PRICE_TYPE_PERCENT) {

            $price = $data->getFinalPrice();

        } else {

            $price = Currency::create()->toMain($data->getPrice(), $data->getSProductVariantPriceType()->getCurrencyId());
        }

        return $price;
    }

    /**
     * @param SProductVariants $variant
     */
    public static function recountFinalPriceForVariant(SProductVariants $variant) {

        if (count($variant->getSProductVariantPrices()) > 0 ) {

            $variantPrice = $variant->getSProductVariantPrices();

            /** @var SProductVariantPrice $item */
            foreach ($variantPrice as $item) {

                if ($item->getSProductVariantPriceType()->getPriceType() == self::PRICE_TYPE_PERCENT) {

                    $final = self::getPercent($item->getPrice(), $variant->getPriceInMain());
                    $item->setFinalPrice($final);
                    $item->save();
                }

            }

        }
    }

    /**
     * Делает пересчет цены товара в процентном соотношении к главной валюте
     *
     * @return void
     */
    public static function recountCurrency() {

        $prices = SProductVariantPriceQuery::create()
            ->useSProductVariantPriceTypeQuery()
                ->filterByPriceType(BaseVariantPriceType::PRICE_TYPE_PERCENT)
            ->endUse()
            ->find();

        foreach ($prices as $price) {

            $var = \SProductVariantsQuery::create()
                ->findOneById($price->getVarId());

            $price->setFinalPrice(
                Currency::create()
                ->toMain(BaseVariantPriceType::getPercent((string) $price->getPrice(), $var->getPriceInMain()), $var->getSCurrencies()->getId())
            );

            $price->save();
        }

        MY_Controller::dropCache();

    }

    /**
     * @param string $userRole
     * @param CartItem $item
     * @return bool
     * @throws PropelException
     */
    public static function checkUsedDiscount($userRole, $item) {

        $price_type = SProductVariantPriceTypeQuery::create()
            ->setComment(__METHOD__)
            ->withColumn(SProductVariantPriceTypeTableMap::COL_CONSIDER_DISCOUNT, 'consider_discount')
            ->select(['consider_discount'])
            ->filterByStatus(1)
            ->useSProductVariantPriceTypeValueQuery()
                ->filterByValue($userRole)
            ->endUse()
            ->useSProductVariantPriceQuery()
             ->filterByVarId($item->data['id'])
            ->endUse()
            ->findOne();

        return !($price_type === '0');
    }

    /**
     * @param float $percent
     * @param int $origin_price
     * @return float
     */
    public static function getPercent($percent, $origin_price) {
        $module_percent = abs($percent);
        $sum_percent = $origin_price * $module_percent / 100;
        $sum_percent = $percent < 0 ? -$sum_percent : $sum_percent;

        return $origin_price + $sum_percent;
    }

    /**
     * @return DX_Auth
     */
    private function getDxAuth() {
        $ci = &get_instance();

        return $ci->dx_auth;
    }

    /**
     * @return int
     */
    private function getVariantId() {

        return $this->variant_id;
    }

    /**
     * @param int $variant_id
     */
    private function setVariantId($variant_id) {

        $this->variant_id = $variant_id;
    }

    /**
     * @return int|null
     */
    private function getUserRoleId() {

        return self::$user_role_id;
    }

    /**
     * @param int|null $user_role_id
     */
    public function setUserRoleId($user_role_id) {

        self::$user_role_id = $user_role_id;
    }

    /**
     * @return boolean
     */
    private function isLogin() {

        return $this->login;
    }

    /**
     * @return null|int
     */
    private function getTypeId() {

        return $this->type_id;
    }

    /**
     * @param null|int $type_id
     */
    private function setTypeId($type_id) {

        $this->type_id = $type_id;
    }

    /**
     * @param float $price
     */
    public function setPrice($price) {
        $this->price = $price;
    }

    /**
     * @return boolean
     */
    public function isUseConsiderDiscount() {
        return $this->useConsiderDiscount;
    }

    /**
     * @param boolean $useConsiderDiscount
     */
    public function setUseConsiderDiscount($useConsiderDiscount) {
        $this->useConsiderDiscount = (boolean) $useConsiderDiscount;
    }

}