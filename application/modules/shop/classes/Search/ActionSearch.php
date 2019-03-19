<?php

namespace Search;

use Map\SProductsTableMap;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Exception\PropelException;
use SProductsQuery;

/**
 * Class ActionSearch
 * @package Search
 */
class ActionSearch extends BaseSearch
{

    public $type;

    /**
     * ActionSearch constructor.
     * @param $locate
     */
    public function __construct($locate) {

        $this->setLocate($locate);
    }

    /**
     * @return SProductsQuery mixed
     * @throws PropelException
     */
    private function QueryProducts() {

        $locate = $this->getLocate();
        $orderBy = $this->changeOrderBy();

        $res = SProductsQuery::create()
            ->distinct()
            ->filterByActive(1)
            ->withColumn('IF(sum(shop_product_variants.stock) > 0, 1, 0)', 'allstock')
            ->joinI18n($locate)
            ->joinMainCategory()
                ->useMainCategoryQuery()
                    ->filterByActive(1)
                ->endUse()
            ->joinProductVariant()
                ->useProductVariantQuery()
                    ->joinI18n($locate, '', Criteria::INNER_JOIN)
                ->endUse()
            ->_if($this->getType() == 'all')
                ->condition('numberCondition', 'SProducts.Hot =?', true)
                ->condition('nameCondition', 'SProducts.Hit =?', true)
                ->condition('nameVariantCondition', 'SProducts.Action =?', true)
                ->where(['numberCondition', 'nameCondition', 'nameVariantCondition'], Criteria::LOGICAL_OR)
            ->_else()
                ->filterBy($this->getType(), true)
            ->_endif()
            ->groupBy('shop_products_i18n.id')
            ->orderBy('allstock', Criteria::DESC)
            ->_if($orderBy)
                ->globalSort($orderBy)
            ->_endif();

        return $res;
    }

    /**
     * @param string $type
     * @return array
     */
    public function getActionsProducts($type) {

        $this->setType($type);

        $data = $this->QueryProducts();

        $products = clone $data;

        $products = $this->getProductsByFilters($products);

        $data = $data->find();

        $re = $this->getTotalActions();

        $res = [
                'products'      => $products,
                'totalProducts' => $this->getTotalRow(),
                'categories'    => $this->getProductsCategories($data),
                'totalActions'  => $re['countAction'] ?: 0,
                'totalHots'     => $re['countHot'] ?: 0,
                'totalHits'     => $re['countHit'] ?: 0,
               ];

        return $res;

    }

    /**
     * @return int
     */
    public function getTotalActions() {

        $res = SProductsQuery::create()
            ->select(['countHit', 'countHot', 'countAction'])
            ->filterByActive(1)
            ->filterByArchive(0)
            ->withColumn('sum('.SProductsTableMap::COL_HIT.')', 'countHit')
            ->withColumn('sum('.SProductsTableMap::COL_HOT.')', 'countHot')
            ->withColumn('sum('.SProductsTableMap::COL_ACTION.')', 'countAction')
            ->findOne();

        return $res;

    }

    /**
     * @return string
     */
    public function changeOrderBy() {

        $order = $this->getOrderBy();

        if ($order == 'rel') {
            return '';
        }
        return $order;

    }

    /**
     * @param $type
     */
    public function setType($type) {

        $check = mb_strtolower($type);

        switch ($check) {
            case 'hit':
                $type = 'Hit';
                break;
            case 'hot';
                $type = 'Hot';
                break;
            case 'action':
                $type = 'Action';
                break;
            default :
                $type = 'all';
        }

        $this->type = $type;
    }

    public function getType() {
        return $this->type;
    }

}