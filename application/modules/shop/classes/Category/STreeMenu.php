<?php

namespace Category;

use CMSFactory\DependencyInjection\DependencyInjectionProvider;
use CMSFactory\Tree\ModelWrapper;
use CMSFactory\Tree\TreeCollection;
use Doctrine\Common\Cache\CacheProvider;
use MY_Controller;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Exception\PropelException;
use SCategory;
use SCategoryQuery;
use template_manager\classes\Template;
use template_manager\classes\TemplateManager;

(defined('BASEPATH')) OR exit('No direct script access allowed');

/**
 *
 * @property \MY_URI $uri
 * @property \CI_DB_active_record $db
 * @property \Template $template Description
 * @property \Core $core
 */
final class STreeMenu
{

    /**
     * @var STreeMenu
     */
    protected static $_instance;

    /**
     * @var string
     */
    private $cache_key_menu = 'categories_menu_tree_';

    /**
     * @var  string
     */
    private $cache_product_active = 'product_active_key_';

    /**
     * @var string
     */
    private $cache_html_menu = 'html_cache_menu_key_';

    /**
     * @var string
     */
    private $locale = 'ru';

    /**
     *
     * @var array
     */
    private $config;

    /**
     * @var string
     */
    private $tpl_folder = '';

    /**
     * @var string
     */
    private $current_uri;

    /**
     * @var Template
     */
    private $current_template;

    /**
     * @var CacheProvider
     */
    private $cache;

    /**
     * @var string
     */
    private $tpl_folder_prefix = 'level_';

    /**
     * @var MY_Controller
     */
    private $ci;

    /**
     * @var SCategory
     */
    private $activeCategory;

    private function __construct() {

        $this->ci =& get_instance();
        $this->setLocale();
        $this->current_uri = $this->ci->uri->uri_string();
        $this->current_template = TemplateManager::getInstance()->getCurentTemplate();
        $this->setCache();
        $this->activeCategory = $this->getActiveCategory();
    }

    private function getActiveCategory() {
        if ('shop_category' == $this->ci->core->core_data['data_type']) {
            return SCategoryQuery::create()->findOneById($this->ci->core->core_data['id']);
        }
    }

    /**
     * @param array|null $arg
     * @return $this
     */
    public function setConf(array $arg = null) {

        $this->config = ['url.shop.category' => '/shop/category/'];
        if ($arg != null) {
            $this->config = array_merge($this->config, $arg);
        }

        return $this;
    }

    /**
     * @return void
     */
    private function setLocale() {

        $this->locale = MY_Controller::getCurrentLocale();
    }

    /**
     * Returns a new TreeMenu object.
     * @return STreeMenu
     * @access public static
     */
    public static function create() {

        (null !== self::$_instance) OR self::$_instance = new self();

        return self::$_instance;
    }

    /**
     * @param string $path
     * @return void
     */
    public function load($path) {

        $this->tpl_folder = $path;

        $this->showMenu();
    }

    /**
     * Возвращяет сформированый html код
     * @return void
     * @throws PropelException
     */
    private function showMenu() {

        $htmlCache = $this->cache_html_menu . $this->tpl_folder . $this->getLocale();

        if ($this->getCache()->contains($htmlCache)) {
            $menu = $this->getCache()->fetch($htmlCache);
        } else {
            if ($this->getCache()->contains($this->getCacheKeyMenu())) {

                $tree = $this->getCache()->fetch($this->getCacheKeyMenu());

            } else {
                $categories = SCategoryQuery::create()
                    ->setComment(__METHOD__)
                    ->joinWithI18n($this->getLocale(), Criteria::INNER_JOIN)
                    ->filterByActive(true)
                    ->joinWithRoute()
                    ->filterByShowInMenu(true)
                    ->orderByPosition(Criteria::ASC)
                    ->find();

                $tree = new TreeCollection($categories);
                $this->getCache()->save($this->getCacheKeyMenu(), $tree, config_item('cache_ttl'));
            }
            if ($this->config['object_render'] === true) {

                $menu = $this->ci->template->fetch('/' . $this->tpl_folder . '/level_0/container', ['wrapper' => $tree]);

            } else {

                if ($this->recursionSubCategory($tree, $this->productActive())) {

                    $menu = $this->ci->template->fetch('/' . $this->tpl_folder . '/level_0/container');

                }
            }
            $this->getCache()->save($htmlCache, $menu);
        }

        echo $menu;
    }

    /**
     * @return CacheProvider
     */
    public function getCache() {

        return $this->cache;
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function setCache() {

        $this->cache = DependencyInjectionProvider::getContainer()->get('cache');
    }

    /**
     * @return mixed
     */
    private function getCacheKeyMenu() {

        return $this->cache_key_menu . $this->getLocale();
    }

    /**
     * @return string
     */
    private function getLocale() {

        return $this->locale;
    }

    /**
     * Return array of paths to active category
     * @return array|boolean
     */
    public function returnArrayOfPathsToActiveCategory() {

        $arrayOfPaths = [];
        if ($this->activeCategory) {
            $arrayOfPaths = explode('/', $this->activeCategory->getFullPath());

        }

        return $arrayOfPaths;
    }

    /**
     * @return array|bool
     * @throws PropelException
     */
    private function productActive() {

        if ($this->ci->core->core_data['data_type'] !== 'product') {
            return false;
        }

        if ($this->getCache()->contains($this->getCacheProductActive())) {

            $res = $this->getCache()->fetch($this->getCacheProductActive());

        } else {

            $res = \SProductsQuery::create()
                ->setComment(__METHOD__)
                ->joinMainCategory()
                ->findOneById($this->ci->core->core_data['id']);

            $this->getCache()->save($this->getCacheProductActive(), $res, config_item('cache_ttl'));
        }

        if ($res) {
            return explode('/', $res->getMainCategory()->getFullPath());
        }

        return false;

    }

    /**
     * @return string
     */
    public function getCacheProductActive() {

        $string = md5($this->cache_product_active . $this->core->core_data['id'] . '_' . $this->getLocale());
        return $string;
    }

    /**
     * @param ObjectCollection $tree
     * @param null|array $productActive
     * @param int $level
     * @return bool
     */
    private function recursionSubCategory(ObjectCollection $tree, $productActive = NULL, $level = 0) {

        $wrappers = null;
        /** @var SCategory|ModelWrapper $v */
        foreach ($tree as $v) {

            /** If parent category is disable  **/
            if ($v->getShowInMenu() && $this->isMenuLevelDir($level)) {

                /** Check sub items category  **/
                $this->checkSubItems($v, $productActive, $level);

                $data = [
                         'index' => $level,
                         'id'    => $v->getId(),
                         'title' => $v->getName(),
                         'link'  => site_url($v->getRouteUrl()),
                         'image' => $v->getImage(),
                        ];

                $itemTplPath = $this->tpl_folder . '/level_' . $level . '/';

                $wrappers .= $this->ci->template->fetch('/' . $itemTplPath . 'item_default', $data);

            }
            unset($v);
        }

        $this->ci->template->assign('wrapper', $wrappers);

        if ($level !== 0 && $this->isMenuLevelDir($level)) {
            $wrapper = $this->ci->template->fetch('/' . $this->tpl_folder . '/level_' . $level . '/container', $data);
            $this->ci->template->assign('wrapper', $wrapper);
        }

        return true;
    }

    /**
     * @param int $level
     * @return bool
     */
    private function isMenuLevelDir($level) {

        $directoryPath = TEMPLATES_PATH . $this->current_template->name . DIRECTORY_SEPARATOR
            . $this->tpl_folder . DIRECTORY_SEPARATOR . $this->tpl_folder_prefix . $level;

        return is_dir($directoryPath);
    }

    /**
     * @param ModelWrapper $category
     * @param array $prod_active
     * @param int $level
     * @return void
     */
    private function checkSubItems(ModelWrapper $category, $prod_active, $level) {

        if ($category->hasSubItems()) {

            $this->recursionSubCategory($category->getSubItems(), $prod_active, ++$level);

        } else {

            $this->ci->template->assign('wrapper', false);
        }

    }

    protected function __clone() {

        // ограничивает клонирование объекта
    }

}