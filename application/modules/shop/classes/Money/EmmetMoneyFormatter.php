<?php
namespace shop\classes\Money;

use SCurrencies;

/**
 * Usage && Test data
 * All possible variations of usage + 2 positions of symbol (left|right)
 *
 * dump($model->firstVariant->getFormatPrice('div.prDiv>span.price', 'div#coi>span.coins', 'div.SMB>span.symbol'))
 * dump($model->firstVariant->getFormatPrice('div#price>span.price', 'p>span.coins'))
 * dump($model->firstVariant->getFormatPrice('div#price>span.price'))
 * dump($model->firstVariant->getFormatPrice())}
 * dump($model->firstVariant->getFormatPrice('div.prDiv>span.price', '', 'div.SMB>span.symbol'))}
 * dump($model->firstVariant->getFormatPrice('', 'div#coi>span.coins', 'div.SMB>span.symbol'))}
 * dump($model->firstVariant->getFormatPrice('', '', 'div.SMB>span.symbol'))}
 */
class EmmetMoneyFormatter
{

    /**
     * @var int
     */
    protected $price;

    /**
     * @var int
     */
    protected $coins;

    /**
     * @var string
     */
    protected $priceWrapper;

    /**
     * @var string
     */
    protected $coinsWrapper;

    /**
     * @var string
     */
    protected $symbolWrapper;

    /**
     * @var EmmetRenderer
     */
    protected $emmetRenderer;

    /**
     * @var SCurrencies
     */
    protected $currency;

    protected $thousandsSeparator;

    protected $tensSeparator;

    protected $decimalPlaces;

    protected $hideZero;

    protected $format;

    const VAR_COINS = '`coins`';
    const VAR_PRICE = '`price`';
    const VAR_SYMBOL = '`symbol`';

    public function __construct($price, SCurrencies $currency) {

        $this->currency = $currency;
        $this->emmetRenderer = EmmetRenderer::getInstance();
        $currencyTemplate = unserialize($currency->getCurrencyTemplate());

        $this->thousandsSeparator = $currencyTemplate['Thousands_separator'];
        $this->tensSeparator = $currencyTemplate['Separator_tens'];
        $this->decimalPlaces = $currencyTemplate['Decimal_places'];
        $this->hideZero = $currencyTemplate['Zero'];
        $this->format = $currencyTemplate['Format'];

        $price = number_format($price, $this->decimalPlaces, $this->tensSeparator, $this->thousandsSeparator);
        list($this->price, $this->coins) = explode($this->tensSeparator, $price);

        if ($this->hideZero) {
            $this->coins = rtrim($this->coins, '0');
        }
        $coinsIsNotVisible = (($this->hideZero && (int) $this->coins == 0) || (int) $this->decimalPlaces == 0);

        $this->coins = $coinsIsNotVisible ? '' : $this->tensSeparator . $this->coins;

    }

    /**
     * @return string
     *
     * span.price spa
     *
     */
    public function render() {

        $currencyIsFirst = (0 === strpos(trim($this->format), trim($this->currency->getSymbol())));
        $emmet = $currencyIsFirst ? $this->currencyFirstFormat() : $this->currencyLastFormat();

        return $this->emmetRenderer->render(
            $emmet,
            [
             'price'  => $this->price,
             'coins'  => $this->coins,
             'symbol' => $this->currency->getSymbol(),
            ]
        );
    }

    protected function currencyFirstFormat() {

        $emmet = '';
        $price = self::VAR_PRICE;
        $this->symbolWrapper ? $emmet .= '(' . $this->symbolWrapper . '{' . self::VAR_SYMBOL . '})+' : $price = self::VAR_SYMBOL . ' ' . $price;

        $price = '(' . $this->priceWrapper . '{' . $price;

        $emmet .= $this->coinsWrapper ? $price . '})+(' . $this->coinsWrapper . '{' . self::VAR_COINS . '})' : $price . self::VAR_COINS . '})';

        return $emmet;
    }

    protected function currencyLastFormat() {

        $emmet = '';
        $coins = self::VAR_COINS;
        $emmet .= '(' . $this->priceWrapper . '{' . self::VAR_PRICE;

        $this->symbolWrapper ? $coins .= '})+(' . $this->symbolWrapper . '{' . self::VAR_SYMBOL : $coins = $coins . ' ' . self::VAR_SYMBOL;

        $emmet .= $this->coinsWrapper ? '})+(' . $this->coinsWrapper . '{' . $coins . '})' : $coins . '})';

        return $emmet;
    }

    public function __toString() {

        return $this->render();
    }

    public function setWrappers($priceWrapper = null, $coinsWrapper = null, $symbolWrapper = null) {

        $this->setPriceWrapper($priceWrapper);
        $this->setCoinsWrapper($coinsWrapper);
        $this->setSymbolWrapper($symbolWrapper);
    }

    /**
     * @param string $priceWrapper
     * @return EmmetMoneyFormatter
     */
    public function setPriceWrapper($priceWrapper) {

        $this->priceWrapper = $priceWrapper;
        return $this;
    }

    /**
     * @param string $coinsWrapper
     * @return EmmetMoneyFormatter
     */
    public function setCoinsWrapper($coinsWrapper) {

        if ($this->coins !== '') {
            $this->coinsWrapper = $coinsWrapper;
        }
        return $this;
    }

    /**
     * @param string $symbolWrapper
     * @return EmmetMoneyFormatter
     */
    public function setSymbolWrapper($symbolWrapper) {

        $this->symbolWrapper = $symbolWrapper;
        return $this;
    }

    /**
     * @return int
     */
    public function getPrice() {

        return $this->price;
    }

    /**
     * @return int
     */
    public function getCoins() {

        return $this->coins;
    }

}