<?php

use Base\SProductsQuery as BaseSProductsQuery;
use Map\SProductVariantsTableMap;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Exception\PropelException;

/**
 * Skeleton subclass for performing query and update operations on the 'shop_products' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.Shop
 */
class SProductsQuery extends BaseSProductsQuery
{

    /**
     * @param array $data
     * @return $this
     * @throws PropelException
     */
    public function combinator(array $data) {
        $n = 0;
        foreach ($data as $key => $values) {
            $combiners = [];
            foreach ($values as $searchText) {
                $alias = 'C' . $n;
                $c1 = $alias . $n . 'c1';
                $c2 = $alias . $n . 'c2';
                $combineName = 'Combine' . $n;

                $this->join('SProductPropertiesData ' . $alias, Criteria::LEFT_JOIN);

                $this->condition($c1, $alias . '.PropertyId = ?', $key);
                $this->condition($c2, $alias . '.Value = ?', $searchText);
                $this->combine([$c1, $c2], 'and', $combineName);

                $combiners[] = $combineName;

                $n++;
            }

            $this->combine($combiners, 'or', 'Combiner' . $n);
            $allCombiners[] = 'Combiner' . $n;
        }

        $this->where($allCombiners, 'and');
        $this->distinct();

        return $this;
    }

    /**
     * Apply custom fields query.
     *
     * @param array $fieldsDataArray
     * @return SProductsQuery
     */
    public function applyCustomFieldsQuery(array $fieldsDataArray) {
        $filterData = [];

        if (count($fieldsDataArray) > 0) {
            foreach ($fieldsDataArray as $fieldId => $values) {
                if (count($values) > 0 && is_array($values)) {

                    // This is SPARTA!
                    // Load field
                    $field = SPropertiesQuery::create()
                        ->filterByActive(true)
                        ->findPk($fieldId);

                    if ($field !== null) {
                        $fieldValues = $field->asArray();
                        foreach ($values as $needVal) {
                            if (isset($fieldValues, $needVal) && (!empty($needVal) OR $needVal == '0') && !is_array($needVal)) {
                                if (!is_array($filterData[$field->getId()])) {
                                    $filterData[$field->getId()] = [];
                                }

                                if (is_array($fieldValues)) {
                                    array_push($filterData[$field->getId()], $fieldValues[$needVal]);
                                } else {
                                    array_push($filterData[$field->getId()], $needVal);
                                }
                                $filterData[$field->getId()] = array_unique($filterData[$field->getId()]);
                            }
                        }
                    }
                }
            }
        }

        if (count($filterData) > 0) {
            return $this->combinator($filterData);
        } else {
            return $this;
        }
    }

    /**
     * @return string
     */
    public static function getFilterQueryString() {
        $data = [];

        $need = [
                 'p',
                 'pv',
                 'lp',
                 'rp',
                 'brand',
                 'order',
                 'user_per_page',
                 'categoryId',
                ];

        foreach ($need as $value) {
            if (isset(ShopCore::$ORIGIN_GET[$value])) {
                $data[$value] = ShopCore::$ORIGIN_GET[$value];
            }
        }

        return '?' . http_build_query($data);
    }

    /**
     * @param int $limit
     * @return $this
     */
    public function mostViewed($limit = 6) {
        $this->orderByViews(Criteria::DESC)
            ->filterByActive(true)
            ->limit($limit);
        return $this;
    }

    /**
     * @param SProducts $model
     * @return $this
     */
    public function getSimilarProducts(SProducts $model) {

        $properties = SProductPropertiesDataQuery::create()
            ->findByProductId($model->getId());

        foreach ($properties as $property) {
            $data[][$property->getPropertyId()] = [$property->getValue()];
        }

        shuffle($data);
        return $this
            ->leftJoin('ProductVariant')
            ->where('SProducts.Id != ?', $model->getId())
            ->filterByActive(true)
            ->combinator($data[0]);
    }

    /**
     * @param string $locale
     * @param null|string $joinType
     * @return $this
     */
    public function joinWithI18n($locale = 'ru', $joinType = null) {
        if ($joinType == null) {
            switch (ShopController::getShowUntranslated()) {
                case FALSE:
                    $joinType = Criteria::INNER_JOIN;
                    break;
                default:
                    $joinType = Criteria::LEFT_JOIN;
                    break;
            }
        }

        parent::joinWithI18n($locale, $joinType);
        return $this;
    }

    /**
     * Sort by order method
     * @param string $order_method
     * @return SProductsQuery
     */
    public function globalSort($order_method) {

        switch ($order_method) {
            case 'price':
                $this->orderBy(SProductVariantsTableMap::COL_PRICE, Criteria::ASC);
                break;

            case 'price_desc':
                $this->orderBy(SProductVariantsTableMap::COL_PRICE, Criteria::DESC);
                break;

            case 'name':
                $this->useI18nQuery(\MY_Controller::getCurrentLocale(), null, Criteria::INNER_JOIN)->orderByName(Criteria::ASC)->endUse();
                break;

            case 'name_desc':
                $this->useI18nQuery(\MY_Controller::getCurrentLocale(), null, Criteria::INNER_JOIN)->orderByName(Criteria::DESC)->endUse();
                break;

            case 'views':
                $this->orderByViews(Criteria::DESC);
                break;

            case 'topsales':
                $this->orderByAddedToCartCount(Criteria::DESC);
                break;

            case 'hit':
                $this->orderByHit(Criteria::DESC)->orderByCreated(Criteria::DESC);
                break;

            case 'hot':
                $this->orderByHot(Criteria::DESC)->orderByCreated(Criteria::DESC);
                break;

            case 'action':
                $this->orderByAction(Criteria::DESC)->orderByCreated(Criteria::DESC);
                break;

            case 'created_asc':
                $this->orderByCreated(Criteria::ASC);
                break;

            case 'created_desc':
                $this->orderByCreated(Criteria::DESC);
                break;

            case 'rel':
                $this->orderBy('rel', Criteria::DESC);
                break;

            case 'rating':
                $this->useSProductsRatingQuery('TRating', Criteria::LEFT_JOIN)
                    ->withColumn('TRating.Rating / TRating.Votes', 'Midrate')
                    ->endUse()
                    ->orderBy('Midrate', Criteria::DESC)
                    ->orderBy('TRating.Votes', Criteria::DESC);
                break;

            default:
                $this->useSProductsRatingQuery('TRating', Criteria::LEFT_JOIN)
                    ->withColumn('TRating.Rating / TRating.Votes', 'Midrate')
                    ->endUse()
                    ->orderBy('Midrate', Criteria::DESC)
                    ->orderBy('TRating.Votes', Criteria::DESC);
                break;
        }
        return $this;
    }

}

// SProductsQuery