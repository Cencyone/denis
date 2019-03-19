<?php

use Base\SCategoryQuery as BaseSCategoryQuery;
use CMSFactory\Tree\TreeCollection;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;


/**
 * Skeleton subclass for performing query and update operations on the 'shop_category' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.Shop
 */
class SCategoryQuery extends BaseSCategoryQuery
{

    /**
     * get Tree collection of all categories
     * @param int $rootId
     * @param Criteria $criteria
     * @return TreeCollection
     */
    public function getTree($rootId = 0, Criteria $criteria = null) {

        $collection = $this->getAllLevelSubCategories($rootId, $criteria);
        return new TreeCollection($collection, $rootId);
    }

    /**
     * Collection of all levels sub categories
     * @param int $rootId
     * @param Criteria|null $criteria
     * @return Collection|ObjectCollection
     */
    public function getAllLevelSubCategories($rootId, Criteria $criteria = null) {

        if ($rootId == 0) {
            $criteria = $criteria ?: $this;
            return $criteria->orderByPosition()->find();
        }

        if ($rootId instanceof Collection) {
            $collection = $rootId;
            $subItems = SCategoryQuery::create(null, $criteria)->filterByParentId($collection->getPrimaryKeys(), Criteria::IN)->orderByPosition()->find();
        } else {
            $subItems = SCategoryQuery::create(null, $criteria)->filterByParentId($rootId)->orderByPosition()->find();
            $collection = new ObjectCollection();
        }

        !$subItems->isEmpty() && $this->getAllLevelSubCategories($subItems);

        $data = array_merge($collection->getData(), $subItems->getData());
        $collection->setData($data);

        return $collection;

    }

}