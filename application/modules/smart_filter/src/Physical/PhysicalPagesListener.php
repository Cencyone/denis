<?php

namespace smart_filter\src\Physical;

use Base\SPropertyValueQuery;
use Category;
use CI;
use CMSFactory\ModuleSettings;
use core\src\UrlParser;
use MY_Controller;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Exception\PropelException;
use SBrandsQuery;
use SCategory;
use smart_filter\models\SFilterPattern;
use smart_filter\src\Admin\PatternHandler;
use SPropertiesQuery;
use Twig_Environment;
use Twig_Error_Loader;
use Twig_Error_Syntax;

/**
 * Class PhysicalPages
 * @package smart_filter\src\Physical
 */
class PhysicalPagesListener
{

    /**
     * @var Twig_Environment
     */
    private $twig;

    /**
     * @var UrlParser
     */
    private $parser;

    /**
     * @var array
     */
    private $variables = [];

    /**
     * @var string
     */
    private $locale;

    /**
     * @var ModuleSettings
     */
    private $settings;

    /**
     * @var PatternHandler
     */
    private $handler;

    /**
     * PhysicalPages constructor.
     * @param Twig_Environment $twig
     * @param UrlParser $urlParser
     * @param PatternHandler $handler
     */
    public function __construct(Twig_Environment $twig, UrlParser $urlParser, PatternHandler $handler) {

        $this->twig = $twig;
        $this->parser = $urlParser;
        $this->settings = ModuleSettings::ofModule('smart_filter')->get(MY_Controller::getCurrentLocale());
        $this->handler = $handler;

    }

    /**
     * @param Category $categoryObj
     * @throws PropelException
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Syntax
     */
    public function _onLoadCategory($categoryObj) {

        /** @var SCategory $category */
        $category = $categoryObj->data['category'];
        $this->addVariable('category', $category);

        $this->locale = MY_Controller::getCurrentLocale();
        $pattern = $this->handler->findByValues($category->getId(), $this->locale, $this->parser->getBrands(), $this->parser->getProperties());

        if ($pattern && $pattern->getActive()) {
            $this->setMetaTagsByPattern($pattern, $categoryObj);
        } elseif ($this->parser->getFilterSegment() != '') {
            CI::$APP->template->registerCanonical(site_url($this->parser->getUrl()));
            $this->createDefaultMeta($categoryObj);
        }
    }

    /**
     * Also used by Smart filter to set [min|max]Price
     * @param string $key
     * @param mixed $value
     */
    public function addVariable($key, $value) {

        $this->variables[$key] = $value;
    }

    /**
     * @param SFilterPattern $pattern
     * @param Category $categoryObj
     * @throws PropelException
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Syntax
     */
    private function setMetaTagsByPattern(SFilterPattern $pattern, $categoryObj) {

        $this->grabVariables($pattern);
        if ($pattern->getMetaRobots() !== '') {
            CI::$APP->template->registerMeta('robots', $pattern->getMetaRobots());
        }
        $this->setMetaTags(
            $categoryObj,
            $this->render($pattern->getH1()),
            $this->render($pattern->getSeoText()),
            $this->render($pattern->getMetaTitle()),
            $this->render($pattern->getMetaKeywords()),
            $this->render($pattern->getMetaDescription())
        );

    }

    /**
     * @param SFilterPattern $pattern
     */
    private function grabVariables(SFilterPattern $pattern) {

        if ($brandId = $pattern->getDataBrandId()) {
            $brand = SBrandsQuery::create()->setComment(__METHOD__)->joinWithI18n($this->locale, Criteria::INNER_JOIN)->findOneById($brandId);
            $this->addVariable('brand', $brand);
        }
        if ($propertyId = $pattern->getDataPropertyId()) {
            $property = SPropertiesQuery::create()->setComment(__METHOD__)->joinWithI18n($this->locale, Criteria::INNER_JOIN)->findOneById($propertyId);
            $this->addVariable('property', $property);
        }

        if ($propertyValueId = $this->getPropertyValueId()) {
            $propertyValue = SPropertyValueQuery::create()->setComment(__METHOD__)->joinWithI18n($this->locale, Criteria::INNER_JOIN)
                ->filterByPropertyId($propertyId)
                ->findOneById($propertyValueId);
            $this->addVariable('value', $propertyValue);

        }
    }

    /**
     * @return int|null|string
     */
    private function getPropertyValueId() {

        $parser = $this->parser;
        $propertyValueId = null;
        if (1 === $parser->countProperties() && 1 === $parser->countValues($parser->getFirstProperty())) {
            $property = $parser->getFirstProperty();
            $propertyValueId = $parser->getFirstValue($property);
        }
        return $propertyValueId;
    }

    /**
     * @param Category $categoryObj
     * @param string $h1
     * @param string $seoText
     * @param string $title
     * @param string $keywords
     * @param string $description
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Syntax
     */
    private function setMetaTags($categoryObj, $h1, $seoText, $title, $keywords, $description) {

        /** @var SCategory $category */
        $category = $categoryObj->data['category'];
        $category->setDescription($seoText);

        $category->setH1($h1);
        $category->setVirtualColumn('title', $h1);
        $categoryObj->data['title'] = $h1;

        CI::$APP->core->set_meta_tags(
            $this->render($title),
            $this->render($keywords),
            $this->render($description),
            $categoryObj->data['page_number'],
            $category->getShowsitetitle()
        );
    }

    /**
     * @param string $template
     * @return string
     * @throws Twig_Error_Syntax
     * @throws Twig_Error_Loader
     */
    private function render($template) {

        $template = '{% autoescape false %} ' . $template . ' {% endautoescape %}';
        $template = $this->twig->createTemplate($template)->render($this->variables);
        return $template;
    }

    /**
     * @param Category $categoryObj
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Syntax
     */
    private function createDefaultMeta($categoryObj) {

        if ($this->settings['active']) {

            $this->grabDefaultVariables();

            $this->setMetaTags(
                $categoryObj,
                $this->render($this->settings['h1']),
                $this->render($this->settings['seo_text']),
                $this->render($this->settings['meta_title']),
                $this->render($this->settings['meta_keywords']),
                $this->render($this->settings['meta_description'])
            );
        }
    }

    private function grabDefaultVariables() {

        $properties = [];
        $brands = [];

        if (isset($this->variables['selectedBrands'])) {
            $brands = array_map(
                function ($brand) {
                    return $brand->name;
                },
                $this->variables['selectedBrands']
            );
        }

        if (isset($this->variables['selectedProperties'])) {

            foreach ($this->variables['selectedProperties'] as $property) {

                $values = [];

                foreach ($property->selectedValues as $value) {
                    $values[] = $value['value'];
                }
                $properties[] = $property->name . ' (' . join(', ', $values) . ')';
            }

        }

        $this->variables['brands'] = $brands;
        $this->variables['properties'] = $properties;
    }

}