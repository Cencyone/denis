<?php namespace smart_filter\src\Admin;

use CI_DB_active_record;
use CMSFactory\Exception;
use core\src\UrlParser;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Collection\ObjectCollection;
use smart_filter\models\SFilterPattern;
use smart_filter\models\SFilterPatternQuery;
use Trash;

/**
 * Class EventSubscriber
 * Filter Event subscriber
 * Creates delete redirects on deleting properties brands categories
 * Removes redirects on creating
 *
 * @package smart_filter\src\Admin
 */
class EventSubscriber
{

    /**
     * @var Trash
     */
    private $trash;

    /**
     * @var PatternHandler
     */
    private $handler;

    /**
     * @var CI_DB_active_record
     */
    private $db;

    public function getHandlers() {
        return [
                'ShopAdminBrands:create'      => 'createBrand',
            //            'ShopAdminBrands:edit' => 'updateBrand',
                'ShopAdminBrands:delete'      => 'removeBrand',

            //            'ShopAdminCategories:create' => 'createCategory',
            //            'ShopAdminCategories:fastCreate' => 'createCategory',
            //            'ShopAdminCategories:edit' => 'updateCategory',
                'ShopAdminCategories:delete'  => 'removeCategory',

            //            'ShopAdminProperties:create' => 'createProperty',
            //            'ShopAdminProperties:fastCreate' => 'createProperty',
            //            'ShopAdminProperties:edit' => 'updateProperty',
                'ShopAdminProperties::delete' => 'removeProperty',
               ];
    }

    /**
     * EventSubscriber constructor.
     * @param Trash $trash
     * @param PatternHandler $handler
     * @param CI_DB_active_record $db
     */
    public function __construct(Trash $trash, PatternHandler $handler, CI_DB_active_record $db) {
        $this->trash = $trash;
        $this->handler = $handler;
        $this->db = $db;
    }

    /**
     * @param array $data
     */
    public function createBrand($data) {
        /** @var \SBrands $model */
        $model = $data['model'];
        $redirects = $this->getRedirectsFromUrls(UrlParser::PREFIX_BRAND . $model->getUrl());
        foreach ($redirects as $redirect) {
            $this->deleteRedirectsFrom($redirect);
        }
    }

    /**
     * @param array $data
     */
    public function removeBrand($data) {
        /** @var \SBrands[] $brands */
        $brands = $data['model'];
        foreach ($brands as $brand) {
            $patterns = $this->handler->findByBrand($brand->getUrl());
            $this->removePatterns($patterns);

        }
    }

    /**
     * @param array $data
     */
    public function removeProperty($data) {
        /** @var \SProperties $model */
        $model = $data['model'];
        $patterns = $this->handler->findByProperty($model->getCsvName());
        $this->removePatterns($patterns);
    }

    /**
     * @param array $data
     */
    public function removeCategory($data) {
        /** @var int[] $ids */
        $ids = $data['ShopCategoryId'];

        $patterns = SFilterPatternQuery::create()->setComment(__METHOD__)->filterByCategoryId($ids, Criteria::IN)->find();
        $categories = \SCategoryQuery::create()->setComment(__METHOD__)->filterById($ids, Criteria::IN)->find();

        foreach ($categories as $category) {
            $this->deleteRedirectsTo('shop/category/' . $category->getFullPath());
        }

        $patterns->delete();
    }

    /**
     * @param string $like
     * @return array
     */
    private function getRedirectsFromUrls($like) {
        /** @var  \CI_DB_mysql_result $query */
        $query = $this->db->select('trash_url', false)->where('trash_redirect_type', 'url')->like('trash_url', $like)->get('trash');
        if ($query->num_rows()) {
            $urls = [];
            $result = $query->result_array();
            foreach ($result as $item) {
                $urls[] = $item['trash_url'];
            }
            return $urls;
        }
    }

    /**
     * @param string $like
     */
    private function deleteRedirectsTo($like) {
        $this->db->where('trash_redirect_type', 'url')->like('trash_redirect', $like)->delete('trash');
    }

    /**
     * @param string $like
     */
    private function deleteRedirectsFrom($like) {
        $this->db->where('trash_redirect_type', 'url')->like('trash_url', $like)->delete('trash');
    }

    /**
     * @param SFilterPattern[]|ObjectCollection $patterns
     * @throws Exception
     */
    private function removePatterns($patterns) {
        foreach ($patterns as $pattern) {
            $urls = $this->handler->getUrlsForMultiplePattern($pattern);
            foreach ($urls as $url) {
                $this->trash->create_redirect($url, 'shop/category/' . $pattern->getDataCategoryUrl(), 302);
            }
        }
        $patterns->delete();
    }

}