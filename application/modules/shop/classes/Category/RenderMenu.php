<?php

namespace Category;

use CMSFactory\DependencyInjection\DependencyInjectionProvider;
use Doctrine\Common\Cache\CacheProvider;
use MY_Controller;
use template_manager\classes\TemplateManager;

(defined('BASEPATH')) OR exit('No direct script access allowed');


/*
  |--------------------------------------------------------------------------|
  | Класс подлежит уничтожению.                                              |
  |                                                                          |
  | При необходимости уничтожить.                                            |
  |--------------------------------------------------------------------------|
  | Использует старые технологии,  альтернатива  STreeMenu                   |
  |__________________________________________________________________________|
 */


/**
 * @property Core $core
 * @property \CI_DB_active_record $db
 */
class RenderMenu extends \CI_Model
{

    protected static $_instance;

    const CACHE_KEY_MENU = 'old_category_tree';

    public $menu_array = []; // the root of the menu tree

    public $sub_menu_array = []; // the list of menu items

    public $select_hidden = FALSE;

    public $arranged_menu_array = [];

    public $activate_by_sub_urls = TRUE;

    public $menu_template_vars = [];

    private $current_uri = '';

    private $level = -1;

    private $category = [];

    private $ownerId = 0;

    private $noCache = false;

    private $config = [];

    private $tpl_folder = '';

    private $tpl_folder_prefix = 'level_';

    private $current_template;

    /**
     * @var CacheProvider
     */
    private $cache;

    public function __construct() {
        parent::__construct();
        $this->setConfig();
        $this->current_uri = $this->uri->uri_string();
        $this->current_template = TemplateManager::getInstance()->getCurentTemplate();
        $this->setCache();
    }

    /**
     * Returns a new RenderMenu object.
     * @return RenderMenu
     * @access public static
     * @author Kaero
     * @copyright ImageCMS (c) 2013, Kaero <dev@imagecms.net>
     */
    public static function create() {

        (null !== self::$_instance) OR self::$_instance = new self();

        return self::$_instance;
    }

    public function load($folder = '') {
        $this->tpl_folder = $folder;
        $this->startRenderCategory();
    }

    public function setConfig(array $arg = null) {

        $this->config = ['url.shop.category' => '/shop/category/'];
        if ($arg != null) {
            $this->config = array_unique(array_merge($this->config, $arg));
        }

        return $this;
    }

    public function showSubCategories($folder = '', $ownerId = null, $noCache = true) {
        $this->tpl_folder = $folder;
        if ($ownerId != null) {
            $this->ownerId = $ownerId;
        } else {
            $this->ownerId = $this->core->core_data['id'];
        }
        $this->noCache = $noCache;
        $this->startRenderCategory();
    }

    /**
     * @tutorial Shows all categories.
     * @access Public
     * @author PefoliosInc
     */
    public function startRenderCategory() {
            ob_start();
            $this->showMenu();
            $menu = ob_get_clean();
            echo $menu;
    }

    /**
     * @return Returns all the categories in the form of templates
     * @access Private
     * @author PefoliosInc
     * @copyright ImageCMS (c) 2012, <m.mamonchuk@imagecms.net>
     */
    private function showMenu() {

        $this->categorydb();
    }

    /**
     * @return no cache category DB
     * @access Private
     * @author PefoliosInc
     * @copyright ImageCMS (c) 2012, <m.mamonchuk@imagecms.net>
     */
    private function noCacheCategoryDB() {
        $locale = MY_Controller::getCurrentLocale();
        $this->db->select("*, IF(route.parent_url <> '', concat(route.parent_url, '/', route.url), route.url) as full_path", false);
        $this->db->from('shop_category');
        $this->db->join('route', 'route.id=shop_category.route_id');
        $this->db->where('locale', $locale);
        $this->db->where('active', '1');
        $this->db->order_by('position', 'ASC');
        $this->db->join('shop_category_i18n', 'shop_category_i18n.id = shop_category.id');
        $query = $this->db->get();

        if ($query) {
            $query = $query->result_array();
        } else {
            $query = [];
        }

        return $query;
    }

    /**
     * @return CacheProvider
     */
    public function getCache() {

        return $this->cache;
    }

    /**
     * @return void
     */
    public function setCache() {

        $this->cache = DependencyInjectionProvider::getContainer()->get('cache');
    }

    /**
     * Returns an array with the categories.
     * @access Private
     * @author PefoliosInc
     * @return void
     */
    private function categorydb() {

        $key = self::CACHE_KEY_MENU . MY_Controller::getCurrentLocale();

        if ($this->getCache()->contains($key)) {

            $categoryTree = $this->getCache()->fetch($key);

        } else {

            $this->category = $this->noCacheCategoryDB();
            $categoryTree = $this->renderCategory($this->ownerId);

            $this->getCache()->save($key, $categoryTree, config_item('cache_ttl'));
        }
        $this->template->clear_assign('wrapper');
        $this->recursionSubCategory($categoryTree, $this->productActive());
    }

    /**
     *
     * @tutorial Function recursively examines the category tree, and passes on the template. Be careful when mutations both! :)
     * @access Private
     * @author PefoliosInc
     * @copyright ImageCMS (c) 2012, <m.mamonchuk@imagecms.net>
     *
     */
    private function recursionSubCategory($array = NULL, $productActive = NULL, $index = null) {
        if (is_array($array)) {
            foreach ($array as $v) {
                if (!$this->isMenuLevelDir($v['lvl'])) {
                    return;
                }

                if ($v['subCategory'] != NULL) {
                    $this->recursionSubCategory($v['subCategory'], $productActive, $v['index']);
                } else {
                    $this->template->assign('wrapper', FALSE);
                }
                $data = [
                         'index'  => $index,
                         'id'     => $v['id'],
                         'title'  => $v['name'],
                         'link'   => site_url($this->config['url.shop.category'] . $v['full_path']),
                         'image'  => $v['image'],
                         'column' => $v['column'],
                        ];
                if ($productActive == null) {
                    $categoriesPathsArray = $this->returnArrayOfPathsToActiveCategory();
                } else {
                    $categoriesPathsArray = $this->composeCategoriesPaths($productActive);
                }

                if (!is_array($categoriesPathsArray)) {
                    $categoriesPathsArray = [];
                }

                $itemTplPath = '/' .$this->tpl_folder . '/level_' . $v['lvl'] . '/';
                $itemTplFullPath = TEMPLATES_PATH . \CI::$APP->config->item('template') .$itemTplPath;

                if (in_array($v['full_path'], $categoriesPathsArray) && file_exists($itemTplFullPath . 'item_default_active.tpl')) {
                    $wrappers .= $this->template->fetch($itemTplPath .'item_default_active', $data);
                } else {
                    $wrappers .= $this->template->fetch($itemTplPath . 'item_default', $data);
                }
            }
            $this->template->assign('wrapper', $wrappers);

            if ($v['lvl'] != 0) {
                $wrapper .= $this->template->fetch('/' . $this->tpl_folder . '/level_' . $v['lvl'] . '/container', $data);
            } else {
                $this->template->display('/' . $this->tpl_folder . '/level_0/container');
            }

            $this->template->assign('wrapper', $wrapper);
        } else {
            log_message('error', 'Class RenderMenyu function recursionSubCategory not received array');
            return false;
        }
    }

    private function isMenuLevelDir($level) {
        $directoryPath = TEMPLATES_PATH . "{$this->current_template->name}/{$this->tpl_folder}/{$this->tpl_folder_prefix}$level";
        return is_dir($directoryPath);
    }

    /**
     * Return array of paths to active category
     * @return array|boolean
     */
    public function returnArrayOfPathsToActiveCategory() {
        $uriSegmentsArray = explode('/', $this->current_uri);
        if ($this->uri->segment(1) == $this->locale()) {
            unset($uriSegmentsArray[0]);
            unset($uriSegmentsArray[1]);
            unset($uriSegmentsArray[2]);
            $arrayOfPaths = $this->composeCategoriesPaths($uriSegmentsArray);
        } else {
            unset($uriSegmentsArray[0]);
            unset($uriSegmentsArray[1]);
            $arrayOfPaths = $this->composeCategoriesPaths($uriSegmentsArray);
        }

        if ($arrayOfPaths != null) {
            return $arrayOfPaths;
        } else {
            return false;
        }
    }

    /**
     * Compose categories paths to active category
     * @param array $segments
     * @return array|boolean
     */
    private function composeCategoriesPaths($segments = null) {
        if ($segments) {
            $count = count($segments);
            $step = 0;
            while ($step < $count) {

                $arrayOfCategoriesPaths[$step] = implode('/', array_slice($segments, 0, $step + 1));
                $step++;
            }
        }
        return $arrayOfCategoriesPaths;
    }

    public function lang_menu() {

        if ($this->uri->segment(1) == $this->locale()) {
            $url = $this->uri->segment(4);
        } else {
            $url = $this->uri->segment(3);
        }
        return $url;
    }

    public function locale() {
        return MY_Controller::getCurrentLocale();
    }

    /**
     * @return Tree of all categories.
     * @access Private
     * @author PefoliosInc
     * @copyright ImageCMS (c) 2012, <m.mamonchuk@imagecms.net>
     */
    private function renderCategory($owner_id = 0) {

        $this->level++;
        $index = 0;
        foreach ($this->category as $value) {

            if ($value['parent_id'] == $owner_id) {

                $index++;
                $value['lvl'] = $this->level;
                $value['index'] = $index;

                $value['subCategory'] = $this->renderCategory($value['id']);
                $categoryTree[] = $value;

            }
        }
        $this->level--;

        return $categoryTree;
    }

    /**
     * @return Product category.
     * @access Private
     * @author PefoliosInc
     * @copyright ImageCMS (c) 2012, <m.mamonchuk@imagecms.net>
     */
    private function productActive() {
        $productId = $this->core->core_data;

        if ($productId['data_type'] !== 'product') {
            return false;
        }

        $this->db->select('full_path');
        $this->db->from('shop_products');
        $this->db->where('shop_products.id', $productId['id']);
        $this->db->join('shop_category', 'shop_category.id = shop_products.category_id');
        $query = $this->db->get();
        if ($query) {
            $query = $query->result_array();
        } else {
            $query = [];
        }
        $explode = explode('/', $query[0]['full_path']);
        return $explode;
    }

}