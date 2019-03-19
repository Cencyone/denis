<?php namespace smart_filter\src\Admin;

use core\src\CoreConfiguration;
use core\src\CoreFactory;
use Map\SPropertyValueTableMap;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Exception\PropelException;
use SBrandsI18nQuery;
use SBrandsQuery;
use SCategoryQuery;
use smart_filter\models\SFilterPattern;
use smart_filter\models\SFilterPatternQuery;
use smart_filter\src\Admin\Exception\PatternValidationException;
use core\src\UrlParser;
use SPropertiesI18nQuery;
use SPropertiesQuery;
use SPropertyValueI18nQuery;
use SPropertyValueQuery;

class PatternHandler
{

    /**
     * @var array
     */
    private $propertyValuesCache = [];

    /**
     * @var DataProvider
     */
    private $provider;

    /**
     * PatternHandler constructor.
     * @param DataProvider $provider
     */
    public function __construct(DataProvider $provider) {

        $this->provider = $provider;
    }

    /**
     * @param array $data
     * @param string $locale
     * @throws PropelException
     * @return int
     */
    public function generatePatterns($data, $locale) {

        $sharedData = [];
        foreach (['active', 'h1', 'meta_title', 'meta_description', 'meta_keywords', 'seo_text', 'index', 'follow'] as $item) {
            $sharedData[$item] = $data[$item];
        }
        $category = $data['category_id'];
        $brands = $data['brand_id'];
        $properties = $data['property_id'];

        if (count($brands)) {
            $brands = $this->provider->getBrands($category, $locale, in_array('all', $brands) ? null : $brands);
        }

        if (count($properties)) {
            $properties = $this->provider->getProperties($category, $locale, in_array('all', $properties) ? null : $properties);

        }

        $count = 0;

        if (count($brands) && count($properties)) {

            foreach ($brands as $brand) {
                foreach ($properties as $property) {

                    $patternData = array_merge($sharedData, ['category_id' => $category, 'brand_id' => $brand['id'], 'property_id' => $property['id']]);
                    try {
                        $this->fillPattern(new SFilterPattern(), $patternData, $locale)->save();
                    } catch (PatternValidationException $e) {
                        continue;
                    }
                    $count++;
                }
            }
        } elseif (count($brands)) {

            foreach ($brands as $brand) {

                $patternData = array_merge($sharedData, ['category_id' => $category, 'brand_id' => $brand['id']]);
                try {
                    $this->fillPattern(new SFilterPattern(), $patternData, $locale)->save();
                } catch (PatternValidationException $e) {
                    continue;
                }
                $count++;

            }
        } elseif (count($properties)) {

            foreach ($properties as $property) {
                $patternData = array_merge($sharedData, ['category_id' => $category, 'property_id' => $property['id']]);
                try {
                    $this->fillPattern(new SFilterPattern(), $patternData, $locale)->save();
                } catch (PatternValidationException $e) {
                    continue;
                }
                $count++;
            }

        }
        return $count;

    }

    /**
     * Fill Pattern model with post data for create/edit pages post request
     * @param SFilterPattern $pattern
     * @param array $data
     * @param string $locale
     * @return SFilterPattern
     * @throws PropelException
     * @throws PatternValidationException
     */
    public function fillPattern(SFilterPattern $pattern, $data, $locale) {

        $this->checkUniquePattern($data, $pattern->getId());
        $this->checkProductsCount($data);

        $pattern->setCategoryId($data['category_id'])
            ->setLocale($locale)
            ->setActive($data['active'])
            ->setH1($data['h1'])
            ->setMetaTitle($data['meta_title'])
            ->setMetaKeywords($data['meta_keywords'])
            ->setMetaDescription($data['meta_description'])
            ->setSeoText($data['seo_text'])
            ->setUrlPattern($this->formUrlPattern($data))
            ->setData($this->formData($data))
            ->setName($this->formName($pattern, $locale));

        if (isset($data['index'])) {

            $pattern->setMetaIndex($data['index'] ?: null);
        }
        if (isset($data['follow'])) {
            $pattern->setMetaFollow($data['follow'] ?: null);
        }

        return $pattern;
    }

    /**
     * Create readable name for pattern
     * @param SFilterPattern $pattern
     * @param string $locale
     * @return string
     */
    private function formName(SFilterPattern $pattern, $locale) {

        $name = '';
        $brand = $pattern->getDataBrandId();
        if ($brand) {
            $brand = SBrandsI18nQuery::create()->setComment(__METHOD__)->filterByLocale($locale)->findOneById($brand);
            $brand && $name .= $brand->getName() . ', ';
        }
        $property = $pattern->getDataPropertyId();
        if ($property) {
            $property = SPropertiesI18nQuery::create()->setComment(__METHOD__)->filterByLocale($locale)->findOneById($property);
            if ($property) {
                $name .= $property->getName();
                $value = $pattern->getDataPropertyValueId();
                if ($value) {
                    $value = SPropertyValueI18nQuery::create()->setComment(__METHOD__)->filterByLocale($locale)->findOneById($value);
                    $value && $name .= ' (' . $value->getValue() . ')';
                }
            }

        }
        return trim($name, ', ');

    }

    /**
     * Creates additional data (SFilterPattern.data) for pattern from post array
     * @param array $input
     * @return array
     */
    private function formData($input) {

        $data = [];
        if (array_key_exists('category_id', $input) && $category = SCategoryQuery::create()->setComment(__METHOD__)->findOneById($input['category_id'])) {
            $data['category_url'] = $category->getFullPath();
        }

        foreach (['brand_id', 'property_id', 'value_id'] as $item) {
            if (array_key_exists($item, $input) && $input[$item] > 0) {
                $data[$item] = $input[$item];
            }
        }
        return $data;
    }

    /**
     * Creates url pattern for SFilterPattern
     * @param array $data
     * @return string
     */
    private function formUrlPattern($data) {

        $brand = SBrandsQuery::create()->setComment(__METHOD__)->findOneById($data['brand_id']);
        $property = SPropertiesQuery::create()->setComment(__METHOD__)->findOneById($data['property_id']);
        $propertyValue = SPropertyValueQuery::create()->setComment(__METHOD__)->findOneById($data['value_id']);

        $brand = $brand ? [$brand->getUrl()] : null;
        $property = $property ? [$property->getCsvName() => [$propertyValue ? $propertyValue->getId() : '*']] : null;

        $url = $this->buildUrl($brand, $property);

        return $url;

    }

    /**
     * Throws validation Exception if count of products for filter < 1
     * @param array $data
     * @throws PatternValidationException
     */
    public function checkProductsCount($data) {
        if ($this->provider->getProductsCount($data) < 1) {
            throw new PatternValidationException(lang('There are no products for this combination', 'smart_filter'));
        }
    }

    /**
     * Find pattern by properties and brands
     * @param int $categoryId
     * @param string $locale
     * @param array $brands
     * @param array $properties
     * @return null|SFilterPattern
     */
    public function findByValues($categoryId, $locale, $brands, $properties) {
        $urlPatternSubstitution = $this->buildUrl($brands, $properties, '%');
        $patterns = SFilterPatternQuery::create()
            ->filterByCategoryId($categoryId)
            ->joinWithI18n($locale, Criteria::INNER_JOIN)
            ->filterByUrlPattern($urlPatternSubstitution, Criteria::LIKE)
            ->find();

        return $this->selectBestMatch($patterns, $brands, $properties);
    }

    /**
     * Returns best match of pattern from patterns Collection
     * @param ObjectCollection|SFilterPattern[] $patterns
     * @param array $brands
     * @param array $properties
     * @return null|SFilterPattern
     */
    private function selectBestMatch($patterns, $brands, $properties) {
        $urlPatternClear = $this->buildUrl($brands, $properties);
        $matches = [];
        foreach ($patterns as $pattern) {
            if ($pattern->getUrlPattern() == $urlPatternClear) {
                return $pattern;
            }
            $count = $this->matchPattern($pattern, $properties);

            if ($count > 0) {
                $matches[$count] = $pattern;
            }
        }
        if (count($matches)) {
            return $matches[max(array_keys($matches))];
        }
    }

    /**
     * Returns number of matches
     * @param SFilterPattern $pattern
     * @param $properties
     * @return int|null
     */
    private function matchPattern(SFilterPattern $pattern, $properties) {
        $parser = new UrlParser(CoreFactory::getConfiguration());

        $parser->parse($pattern->getUrlPattern());

        $diff = [];
        if ($properties) {
            $dbProperties = $parser->getProperties();
            if (count($properties) !== count($dbProperties)) {
                return null;
            }
            foreach ($properties as $property => $values) {
                if (count($values) !== count($dbProperties[$property])) {
                    return null;
                }
                $diff = array_merge($diff, array_diff($dbProperties[$property], $values));
            }
            foreach ($diff as $diffValue) {
                if ($diffValue !== '*') {
                    return null;
                }
            }
            return count($properties, COUNT_RECURSIVE) - count($diff);
        }

    }

    /**
     * Build filter segments fromm input data
     * @param array|null $brands [brand1_url[, ...]]
     * @param array|null $properties [property1_csv_name => [ value1, ...]]
     * @param array|null $substituteValues used for substitution of all values to (for example:% ) for database query
     * @return string
     */
    public function buildUrl($brands = null, $properties = null, $substituteValues = null) {

        $url = '';
        if ($brands) {
            $url .= UrlParser::PREFIX_BRAND . implode(UrlParser::VALUE_SEPARATOR, $brands) . UrlParser::SEPARATOR;
        }

        if ($properties) {
            foreach ($properties as $property => $values) {
                if ($substituteValues) {
                    $values = array_fill(0, count($values), $substituteValues);
                }
                $url .= UrlParser::PREFIX_PROPERTY . $property . '-' . implode(UrlParser::VALUE_SEPARATOR, $values) . UrlParser::SEPARATOR;
            }
        }

        return rtrim($url, '/');
    }

    /**
     * @param string $brandUrl
     * @return ObjectCollection|SFilterPattern[]
     */
    public function findByBrand($brandUrl) {
        $patterns = SFilterPatternQuery::create()
            ->filterByUrlPattern('%' . UrlParser::PREFIX_BRAND . $brandUrl . '%', Criteria::LIKE)
            ->find();
        return $patterns;
    }

    /**
     * @param string $propertyCsvName
     * @return ObjectCollection|SFilterPattern[]
     */
    public function findByProperty($propertyCsvName) {
        $search = '%' . UrlParser::PREFIX_PROPERTY . $propertyCsvName . '%';

        $patterns = SFilterPatternQuery::create()
            ->filterByUrlPattern($search, Criteria::LIKE)
            ->find();

        return $patterns;
    }

    /**
     * All possible ulr's for pattern with *
     * @param SFilterPattern $pattern
     * @return array|mixed
     */
    public function getUrlsForMultiplePattern(SFilterPattern $pattern) {

        if ($pattern->hasValuesSubstitution()) {
            $segments = [];
            $parser = new UrlParser(CoreFactory::getConfiguration());
            $parser->parse($pattern->getFullUrl());

            if ($parser->getBrands()) {
                $segments[0] = [$this->buildUrl($parser->getBrands())];
            }

            $properties = $parser->getProperties();
            foreach ($properties as $csvName => $properties) {
                if (in_array('*', $properties)) {
                    $segments[$csvName] = $this->getUrlSegmentForMultipleProperty($csvName);
                }

            }

            $combinations = $this->getAllCombinations($segments);
            array_walk(
                $combinations,
                function (&$el) use ($pattern) {
                    $el = 'shop/category/' . $pattern->getDataCategoryUrl() . '/' . $el;
                }
            );
            return $combinations;

        }
        return [$pattern->getFullUrl()];

    }

    /**
     * Recursively creates all combinations of array
     *
     * @param array $segments
     * @return array|mixed
     */
    private function getAllCombinations($segments) {
        $res = [];
        if ($el = array_shift($segments)) {
            if (count($segments) == 0) {
                return $el;
            }
            foreach ($el as $item) {
                $other = $this->getAllCombinations($segments);
                foreach ($other as $combined) {
                    $res[] = $item . '/' . $combined;
                }
            }
        }
        return $res;
    }

    /**
     * @param string $propertyCsvName
     * @return array
     * @throws PropelException
     */
    private function getUrlSegmentForMultipleProperty($propertyCsvName) {
        $values = $this->getValuesByPropertyCsvName($propertyCsvName);
        $segments = [];
        foreach ($values as $value) {
            $segments[] = UrlParser::PREFIX_PROPERTY . $propertyCsvName . '-' . $value;
        }
        return $segments;
    }

    /**
     * @param string $csvName
     * @return array
     * @throws PropelException
     */
    private function getValuesByPropertyCsvName($csvName) {

        if (!isset($this->propertyValuesCache[$csvName])) {
            $ids = \SPropertyValueQuery::create()
                ->select([SPropertyValueTableMap::COL_ID])
                ->joinWithSProperties()
                ->useSPropertiesQuery()
                ->filterByCsvName($csvName)
                ->endUse()
                ->find()->toArray();
            $this->propertyValuesCache[$csvName] = $ids;
        }
        return $this->propertyValuesCache[$csvName];
    }

    /**
     * Check that pattern is unique for category
     * @param array $data
     * @param null|int $id
     * @return bool
     * @throws PatternValidationException
     */
    public function checkUniquePattern($data, $id = null) {

        $urlPattern = SFilterPatternQuery::create()
            ->filterByUrlPattern($this->formUrlPattern($data))
            ->filterByCategoryId($data['category_id'])
            ->findOne();

        if ($urlPattern) {
            $sameAsEdit = $id && $urlPattern->getId() === $id;

            if (!$sameAsEdit) {
                throw new PatternValidationException(lang('Same pattern already exists', 'smart_filter'));
            }
        }
        return true;
    }

}