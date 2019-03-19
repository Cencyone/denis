<?php

use CMSFactory\Events;
use CMSFactory\assetManager;
use Category\BaseCategory;
use core\src\CoreFactory;
use smart_filter\src\Admin\DataProvider;
use smart_filter\src\Admin\EventSubscriber;
use smart_filter\src\Admin\PatternHandler;
use smart_filter\src\Filter\SmartFilter;
use smart_filter\src\Physical\PhysicalPagesListener;
use smart_filter\src\Sitemap\ItemsGenerator;

/**
 * Class smart_filter
 */
class smart_filter extends \MY_Controller
{

    /**
     * @var PhysicalPagesListener
     */
    private $physicalPages;

    /**
     * @var boolean
     */
    private $isLoaded = false;

    /**
     *
     */
    public function init() {

        $this->_render();
    }

    /**
     *
     */
    public function _render() {

        if (!$this->isLoaded) {
            return;
        }
        echo assetManager::create()->fetchTemplate('main');
    }

    /**
     * The actual filtering
     */
    public function autoload() {

        $this->isLoaded = true;
        Events::create()->setListener([$this, '_onPreselectCategoryProducts'], BaseCategory::EVENT_CATEGORY_PRESELECT_PRODUCTS);

        if (parent::isPremiumCMS()) {
            Events::create()->setListener(
                function ($data) {
                    $categoryObj = $data['categoryObj'];
                    $categoryObj->categoryPath = $this->formCategoryPath();
                    assetManager::create()->registerScript('physical_pages');
                },
                BaseCategory::EVENT_CATEGORY_PRELOAD
            );

            Events::create()->setListener(
                function($data){
                    call_user_func([$this->getPhysicalPages(), '_onLoadCategory'], $data);
                },
                BaseCategory::EVENT_CATEGORY_LOAD
            );
        }
    }

    /**
     * Add event subscriber for brands properties categories (create/update/delete) actions
     * @throws Exception
     */
    public static function adminAutoload() {

        if (parent::isPremiumCMS()) {
            $provider = new DataProvider();
            $handler = new PatternHandler($provider);
            $subscriber = new EventSubscriber(CI::$APP->load->module('trash'), $handler, CI::$APP->db);

            foreach ($subscriber->getHandlers() as $eventName => $callback) {

                Events::create()->on($eventName)->setListener([$subscriber, $callback]);

            }

        }
    }

    public function formCategoryPath() {
        return CoreFactory::getUrlParser()->getUrl();
    }

    /**
     * Add static pages to sitemap
     * @param \Sitemap $siteMapObj
     */
    public function attachPages($siteMapObj) {
        if (parent::isPremiumCMS()) {
            $siteMapGenerator = new ItemsGenerator();
            $locale = MY_Controller::getCurrentLocale();
            $items = $siteMapGenerator->generateItems($siteMapObj, $locale);
            $siteMapObj->items = array_merge($siteMapObj->items, array_values($items));
        }
    }

    /**
     * Filter products select statement according to filter results
     * @param array $data
     */
    public function _onPreselectCategoryProducts($data) {

        $productsQuery = $data['productsQuery'];

        /** @var SCategory $categoryModel */
        $categoryModel = $data['model'];

        $getParams = CI::$APP->input->get();

        $cache = $this->getCache();
        $filter = new SmartFilter($categoryModel->getId(), $getParams, CoreFactory::getUrlParser(), $cache);
        $filter->applyFilterConditions($productsQuery);

        $priceRange = $filter->getPriceRange();
        $curMin = (int) $getParams['lp'] ?: (int) $priceRange['minCost'];
        $curMax = (int) $getParams['rp'] ?: (int) $priceRange['maxCost'];

        $brands = $filter->getBrands();
        $selectedBrands = $filter->getSelectedBrands();

        $properties = $filter->getProperties();
        $selectedProperties = $filter->getSelectedProperties();

        if (parent::isPremiumCMS() && $this->getPhysicalPages()) {
            $this->physicalPages->addVariable('selectedProperties', $selectedProperties);
            $this->physicalPages->addVariable('selectedBrands', $selectedBrands);
            $this->physicalPages->addVariable('minPrice', (int) $priceRange['minCost']);
            $this->physicalPages->addVariable('maxPrice', (int) $priceRange['maxCost']);
        }

        assetManager::create()
            ->registerScript('jquery.ui-slider', false, 'after')
            ->registerScript('filter', false, 'after')
            ->setData(
                [
                 'selectedProperties' => $selectedProperties,
                 'selectedBrands'     => $selectedBrands,
                 'brands'             => $brands,
                 'propertiesInCat'    => $properties,
                 'priceRange'         => $priceRange,
                 'curMin'             => $curMin,
                 'curMax'             => $curMax,
                 'minPrice'           => (int) $priceRange['minCost'],
                 'maxPrice'           => (int) $priceRange['maxCost'],
                ]
            );
    }

    private function getPhysicalPages() {
        return $this->physicalPages = $this->physicalPages ?:
            new PhysicalPagesListener($this->getContainer()->get('twig'), CoreFactory::getUrlParser(), new PatternHandler(new DataProvider()));
    }

    public function _install() {
        $sql = 'CREATE TABLE `smart_filter_patterns`
(
  `id`          INTEGER(11) NOT NULL AUTO_INCREMENT,
  `category_id` INTEGER(11) NOT NULL,
  `active`      TINYINT(1),
  `url_pattern` VARCHAR(255),
  `data`        VARCHAR(255),
  `meta_index`  TINYINT              DEFAULT NULL,
  `meta_follow` TINYINT              DEFAULT NULL,
  `created`     INTEGER(11),
  `updated`     INTEGER(11),
  PRIMARY KEY (`id`),
  UNIQUE INDEX `smart_filter_patterns_u_7826e2` (`category_id`, `url_pattern`)
)
  ENGINE = MYISAM
  CHARACTER SET = \'utf8\';';

        $this->db->query($sql);

        $sql = 'CREATE TABLE `smart_filter_patterns_i18n`
(
  `id`               INTEGER(11)             NOT NULL,
  `locale`           VARCHAR(5) DEFAULT \'ru\' NOT NULL,
  `h1`               TEXT,
  `meta_title`       TEXT,
  `meta_description` TEXT,
  `meta_keywords`    TEXT,
  `seo_text`         TEXT,
  `name`             VARCHAR(255),
  PRIMARY KEY (`id`, `locale`)
)
  ENGINE = MYISAM
  CHARACTER SET = \'utf8\';';

        $this->db->query($sql);
    }
}