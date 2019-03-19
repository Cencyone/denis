<?php namespace smart_filter\src\Sitemap;

use Propel\Runtime\ActiveQuery\Criteria;
use smart_filter\models\Map\SFilterPatternTableMap;
use smart_filter\models\SFilterPattern;
use smart_filter\models\SFilterPatternI18n;
use smart_filter\models\SFilterPatternI18nQuery;

class ItemsGenerator
{

    /**
     * @var array
     */
    private $propertyValues = [];

    /**
     * @return \Generator
     * @throws \Propel\Runtime\Exception\PropelException
     */
    private function createPatternGenerator() {

        $patterns = SFilterPatternI18nQuery::create()
            ->joinWithSFilterPattern()
            ->useSFilterPatternQuery()
            ->filterByMetaIndex(SFilterPatternTableMap::COL_META_INDEX_NOINDEX, Criteria::NOT_EQUAL)
            ->_or()
            ->filterByMetaIndex(null)
            ->filterByActive(true)
            ->endUse()
            ->find();

        foreach ($patterns as $pattern) {
            yield $pattern;
        }

    }

    /**
     * Get all values for property
     * @param $propertyId
     * @return mixed
     * @throws \Propel\Runtime\Exception\PropelException
     */
    private function getPropertyValueIds($propertyId) {

        if (!isset($this->propertyValues[$propertyId])) {

            $values = \SPropertyValueQuery::create()
                ->select(['Id'])
                ->withColumn('Id', 'Id')
                ->filterByPropertyId($propertyId)->find();

            $this->propertyValues[$propertyId] = $values->toArray();
        }

        return $this->propertyValues[$propertyId];

    }

    /**
     * Add url of each property value id to sitemap
     * @param $items
     * @param \smart_filter\models\SFilterPattern $pattern
     * @param $locale
     * @param Sitemap $siteMap
     * @return mixed
     */
    private function addValues($items, $pattern, $locale, $siteMap) {

        $values = $this->getPropertyValueIds($pattern->getDataPropertyId());

        foreach ($values as $value) {
            $urlPattern = str_replace('*', $value, $pattern->getFullUrl());
            $url = $locale . $urlPattern;

            if (!isset($items[$url]) && $siteMap->not_blocked_url(ltrim($url, '/'))) {
                $items[$url] = [
                                'loc'        => site_url($url),
                                'changefreq' => $siteMap->products_categories_changefreq,
                                'priority'   => $siteMap->products_categories_priority,
                                'lastmod'    => date('Y-m-d', $pattern->getUpdated()),
                               ];
            }
        }
        return $items;
    }

    /**
     * Add static pages to sitemap
     * @param \Sitemap $siteMapObj
     * @param string $locale
     * @return bool
     */
    public function generateItems($siteMapObj, $locale) {

        $items = [];

        foreach ($this->createPatternGenerator() as $patternI18n) {
            /** @var SFilterPatternI18n $patternI18n */
            /** @var SFilterPattern $pattern */
            $pattern = $patternI18n->getSFilterPattern();

            $patternLocale = ($patternI18n->getLocale() !== $locale ? $patternI18n->getLocale() : '');

            if ($pattern->hasValuesSubstitution()) {
                $items = $this->addValues($items, $pattern, $patternLocale, $siteMapObj);
            } else {
                $url = $patternLocale . $pattern->getFullUrl();
                if ($siteMapObj->not_blocked_url(ltrim($url, '/'))) {

                    $items[$url] = [
                                    'loc'        => site_url($url),
                                    'changefreq' => $siteMapObj->products_categories_changefreq,
                                    'priority'   => $siteMapObj->products_categories_priority,
                                    'lastmod'    => date('Y-m-d', $pattern->getUpdated()),
                                   ];
                }
            }

        }

        return $items;

    }

}