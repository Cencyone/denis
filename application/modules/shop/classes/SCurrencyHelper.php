<?php
use Currency\Currency;

/**
 * SCurrencyHelper
 *
 * @package
 * @version $id$
 * @copyright
 * @author <dev@imagecms.net>
 * @license
 * @deprecated since version 4.6.1
 */
class SCurrencyHelper
{

    public function __construct() {

    }

    /**
     * Convert price from default or selected currency to another currency
     *
     * @param integer $price Price to convert
     * @param null|integer $currencyId
     * @return int Converted price
     * @access public
     * @deprecated since version 4.6.1
     */
    public function convert($price, $currencyId = null) {
        return Currency::create()->convert($price, $currencyId);
    }

    public function convertnew($price, $currencyId = null) {
        return Currency::create()->convertnew($price, $currencyId);
    }

    public function convertFloor($price, $currencyId = null) {
        return Currency::create()->convertFloor($price, $currencyId);
    }

    /**
     * Convert sum from one currency to another
     * @deprecated since version 4.6.1
     * @param float $sum
     * @param int $from
     * @return float
     */
    public function convertToMain($sum, $from) {
        return Currency::create()->convertToMain($sum, $from);
    }

    /**
     * Get current currency symbol
     *
     * @param integer $id Currency id to get symbol.
     * @access public
     * @return string
     * @deprecated since version 4.6.1
     */
    public function getSymbol($id = null) {
        return Currency::create()->getSymbol();
    }

    /**
     * Get current currency symbol by id
     *
     * @param integer $id Currency id to get symbol.
     * @access public
     * @return string
     * @deprecated since version 4.6.1
     */
    public function getSymbolById($id = null) {
        return Currency::create()->getSymbolById($id);
    }

    /**
     * @deprecated since version 4.6.1
     *
     */
    public function getRateByfilter() {
        return Currency::create()->getRateByfilter();
    }

    /**
     * @deprecated since version 4.6.1
     *
     */
    public function getRateById($id = null) {
        return Currency::create()->getRateById($id);
    }

    /**
     * Get currencies array
     *
     * @access public
     * @return SCurrencies
     * @deprecated since version 4.6.1
     */
    public function getCurrencies() {
        return Currency::create()->getCurrencies();
    }

    /**
     * @deprecated since version 4.6.1
     *
     */
    public function initCurrentCurrency($id = null) {
        return Currency::create()->initCurrentCurrency($id);
    }

    /**
     * @deprecated since version 4.6.1
     *
     */
    public function initAdditionalCurrency($id = null) {
        return Currency::create()->initAdditionalCurrency($id);
    }

    /**
     * @deprecated since version 4.6.1
     *
     */
    public function getmaincurr() {
        return Currency::create()->getMainCurrency();
    }

    /**
     * @deprecated since version 4.6.1
     * @param bool $fix
     * @return bool
     */
    public function checkPrices($fix = false) {
        return Currency::create()->checkPrices($fix);
    }

    /**
     * @deprecated since version 4.6.1
     *
     */
    public function toMain($price, $id = null) {
        return Currency::create()->toMain($price, $id);
    }

}