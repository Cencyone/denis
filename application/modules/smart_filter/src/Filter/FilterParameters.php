<?php

namespace smart_filter\src\Filter;

/**
 * Class FilterParameters
 * Stores all query data used in filter
 * @package smart_filter\classes
 */
class FilterParameters
{

    /**
     * @var array
     */
    protected $brands;

    /**
     * @var array
     */
    protected $properties;

    /**
     * @var
     */
    protected $propertyValueIds;

    /**
     * @var int
     */
    protected $lowPrice;

    /**
     * @var int
     */
    protected $highPrice;

    /**
     * @var int
     */
    protected $categoryId;

    /**
     * @var string
     */
    protected $locale;

    const PARAM_BRAND = 'brand';
    const PARAM_PROPERTY = 'p';
    const PARAM_PROPERTY_ID = 'pv';
    const PARAM_PRICE_LOW = 'lp';
    const PARAM_PRICE_HIGH = 'rp';

    public function __construct($params, $categoryId, $locale) {

        $params = is_array($params) ? $params : [];
        $this->categoryId = $categoryId;
        $this->locale = $locale;
        $this->fetchFilters($params);
    }

    /**
     * @return mixed
     */
    public function getPropertyValueIds() {
        return $this->propertyValueIds;
    }

    /**
     * @param mixed $propertyValueIds
     */
    public function setPropertyValueIds($propertyValueIds) {
        $this->propertyValueIds = $propertyValueIds;
    }

    /**
     * Fetch allowed filters from array
     * @param array $params
     */
    public function fetchFilters(array $params) {
        $initialFilters = [
                           self::PARAM_BRAND       => null,
                           self::PARAM_PROPERTY    => null,
                           self::PARAM_PRICE_LOW   => null,
                           self::PARAM_PRICE_HIGH  => null,
                           self::PARAM_PROPERTY_ID => null,
                          ];

        $params = array_filter($params);
        $filters = array_replace($initialFilters, $params);

        list($this->brands, $this->properties, $this->lowPrice, $this->highPrice, $this->propertyValueIds) = array_values($filters);

        $this->brands !== null && $this->clearBrands();
        $this->properties !== null && $this->properties = $this->convertSpecialCharsInProperties($this->properties);
        $this->highPrice !== null && $this->highPrice = (int) ($this->highPrice + 1);
        $this->lowPrice !== null && $this->lowPrice = (int) $this->lowPrice - 1;

    }

    protected function clearBrands() {
        foreach ($this->brands as $key => $brand) {
            $this->brands[$key] = (int) $brand;
        }
    }

    /**
     * @return string
     */
    public function getLocale() {
        return $this->locale;
    }

    /**
     * @return int
     */
    public function getCategoryId() {
        return $this->categoryId;
    }

    /**
     * @param array $brands
     */
    public function setBrands($brands) {
        $this->brands = $brands;
    }

    /**
     * @return array|null
     */
    public function getBrands() {
        return $this->brands;
    }

    /**
     * @param array $properties
     */
    public function setProperties($properties) {
        $this->properties = $properties;
    }

    /**
     * @return array|null
     */
    public function getProperties() {
        return $this->properties;
    }

    /**
     * @return int|null
     */
    public function getLowPrice() {
        return $this->lowPrice;
    }

    /**
     * @return int|null
     */
    public function getHighPrice() {
        return $this->highPrice;
    }

    /**
     * @return bool
     */
    public function hasBrands() {
        return count($this->brands) > 0;
    }

    /**
     * @return bool
     */
    public function hasProperties() {
        return count($this->properties) > 0;
    }

    /**
     * @return bool
     */
    public function hasPropertyValueIds() {
        return count($this->propertyValueIds) > 0;
    }

    /**
     * @return bool
     */
    public function hasLowPrice() {
        return $this->lowPrice !== null;
    }

    /**
     * @return bool
     */
    public function hasHighPrice() {
        return $this->highPrice !== null;
    }

    /**
     * @return bool
     */
    public function hasPrice() {
        return $this->hasLowPrice() || $this->hasHighPrice();
    }

    /**
     * Perform htmlspecialchars to each property value
     *
     * @param array $inputProperties
     * @param bool|false $decode set true to decode and true to encode
     * @return array
     */
    protected function convertSpecialCharsInProperties(array $inputProperties, $decode = false) {
        $outputProperties = [];
        foreach ($inputProperties as $kFirst => $arrProp) {
            foreach ($arrProp as $kLast => $prop) {
                $outputProperties[$kFirst][$kLast] = $decode ? htmlspecialchars_decode($prop) : htmlspecialchars($prop);
            }
        }
        return $outputProperties;

    }

    /**
     * @return array
     */
    public function toArray() {
        $array = [];
        if ($this->hasLowPrice()) {
            $array[self::PARAM_PRICE_LOW] = (string) ($this->getLowPrice() + 1);
        }
        if ($this->hasHighPrice()) {
            $array[self::PARAM_PRICE_HIGH] = (string) ($this->getHighPrice() - 1);
        }
        if ($this->hasBrands()) {
            $array[self::PARAM_BRAND] = $this->getBrands();
        }
        if ($this->hasProperties()) {
            $array[self::PARAM_PROPERTY] = $this->convertSpecialCharsInProperties($this->getProperties(), true);
        }
        if ($this->hasPropertyValueIds()) {
            $array[self::PARAM_PROPERTY_ID] = $this->getPropertyValueIds();
        }
        return $array;
    }

}