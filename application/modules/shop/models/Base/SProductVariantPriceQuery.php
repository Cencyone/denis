<?php

namespace Base;

use \SProductVariantPrice as ChildSProductVariantPrice;
use \SProductVariantPriceQuery as ChildSProductVariantPriceQuery;
use \Exception;
use \PDO;
use Map\SProductVariantPriceTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'shop_product_variants_prices' table.
 *
 *
 *
 * @method     ChildSProductVariantPriceQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildSProductVariantPriceQuery orderByVarId($order = Criteria::ASC) Order by the var_id column
 * @method     ChildSProductVariantPriceQuery orderByTypeId($order = Criteria::ASC) Order by the type_id column
 * @method     ChildSProductVariantPriceQuery orderByPrice($order = Criteria::ASC) Order by the price column
 * @method     ChildSProductVariantPriceQuery orderByFinalPrice($order = Criteria::ASC) Order by the final_price column
 * @method     ChildSProductVariantPriceQuery orderByProductId($order = Criteria::ASC) Order by the product_id column
 *
 * @method     ChildSProductVariantPriceQuery groupById() Group by the id column
 * @method     ChildSProductVariantPriceQuery groupByVarId() Group by the var_id column
 * @method     ChildSProductVariantPriceQuery groupByTypeId() Group by the type_id column
 * @method     ChildSProductVariantPriceQuery groupByPrice() Group by the price column
 * @method     ChildSProductVariantPriceQuery groupByFinalPrice() Group by the final_price column
 * @method     ChildSProductVariantPriceQuery groupByProductId() Group by the product_id column
 *
 * @method     ChildSProductVariantPriceQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildSProductVariantPriceQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildSProductVariantPriceQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildSProductVariantPriceQuery leftJoinWith($relation) Adds a LEFT JOIN clause and with to the query
 * @method     ChildSProductVariantPriceQuery rightJoinWith($relation) Adds a RIGHT JOIN clause and with to the query
 * @method     ChildSProductVariantPriceQuery innerJoinWith($relation) Adds a INNER JOIN clause and with to the query
 *
 * @method     ChildSProductVariantPriceQuery leftJoinSProductVariantPriceType($relationAlias = null) Adds a LEFT JOIN clause to the query using the SProductVariantPriceType relation
 * @method     ChildSProductVariantPriceQuery rightJoinSProductVariantPriceType($relationAlias = null) Adds a RIGHT JOIN clause to the query using the SProductVariantPriceType relation
 * @method     ChildSProductVariantPriceQuery innerJoinSProductVariantPriceType($relationAlias = null) Adds a INNER JOIN clause to the query using the SProductVariantPriceType relation
 *
 * @method     ChildSProductVariantPriceQuery joinWithSProductVariantPriceType($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the SProductVariantPriceType relation
 *
 * @method     ChildSProductVariantPriceQuery leftJoinWithSProductVariantPriceType() Adds a LEFT JOIN clause and with to the query using the SProductVariantPriceType relation
 * @method     ChildSProductVariantPriceQuery rightJoinWithSProductVariantPriceType() Adds a RIGHT JOIN clause and with to the query using the SProductVariantPriceType relation
 * @method     ChildSProductVariantPriceQuery innerJoinWithSProductVariantPriceType() Adds a INNER JOIN clause and with to the query using the SProductVariantPriceType relation
 *
 * @method     ChildSProductVariantPriceQuery leftJoinSProductVariants($relationAlias = null) Adds a LEFT JOIN clause to the query using the SProductVariants relation
 * @method     ChildSProductVariantPriceQuery rightJoinSProductVariants($relationAlias = null) Adds a RIGHT JOIN clause to the query using the SProductVariants relation
 * @method     ChildSProductVariantPriceQuery innerJoinSProductVariants($relationAlias = null) Adds a INNER JOIN clause to the query using the SProductVariants relation
 *
 * @method     ChildSProductVariantPriceQuery joinWithSProductVariants($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the SProductVariants relation
 *
 * @method     ChildSProductVariantPriceQuery leftJoinWithSProductVariants() Adds a LEFT JOIN clause and with to the query using the SProductVariants relation
 * @method     ChildSProductVariantPriceQuery rightJoinWithSProductVariants() Adds a RIGHT JOIN clause and with to the query using the SProductVariants relation
 * @method     ChildSProductVariantPriceQuery innerJoinWithSProductVariants() Adds a INNER JOIN clause and with to the query using the SProductVariants relation
 *
 * @method     \SProductVariantPriceTypeQuery|\SProductVariantsQuery endUse() Finalizes a secondary criteria and merges it with its primary Criteria
 *
 * @method     ChildSProductVariantPrice findOne(ConnectionInterface $con = null) Return the first ChildSProductVariantPrice matching the query
 * @method     ChildSProductVariantPrice findOneOrCreate(ConnectionInterface $con = null) Return the first ChildSProductVariantPrice matching the query, or a new ChildSProductVariantPrice object populated from the query conditions when no match is found
 *
 * @method     ChildSProductVariantPrice findOneById(int $id) Return the first ChildSProductVariantPrice filtered by the id column
 * @method     ChildSProductVariantPrice findOneByVarId(int $var_id) Return the first ChildSProductVariantPrice filtered by the var_id column
 * @method     ChildSProductVariantPrice findOneByTypeId(int $type_id) Return the first ChildSProductVariantPrice filtered by the type_id column
 * @method     ChildSProductVariantPrice findOneByPrice(string $price) Return the first ChildSProductVariantPrice filtered by the price column
 * @method     ChildSProductVariantPrice findOneByFinalPrice(string $final_price) Return the first ChildSProductVariantPrice filtered by the final_price column
 * @method     ChildSProductVariantPrice findOneByProductId(int $product_id) Return the first ChildSProductVariantPrice filtered by the product_id column *

 * @method     ChildSProductVariantPrice requirePk($key, ConnectionInterface $con = null) Return the ChildSProductVariantPrice by primary key and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSProductVariantPrice requireOne(ConnectionInterface $con = null) Return the first ChildSProductVariantPrice matching the query and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildSProductVariantPrice requireOneById(int $id) Return the first ChildSProductVariantPrice filtered by the id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSProductVariantPrice requireOneByVarId(int $var_id) Return the first ChildSProductVariantPrice filtered by the var_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSProductVariantPrice requireOneByTypeId(int $type_id) Return the first ChildSProductVariantPrice filtered by the type_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSProductVariantPrice requireOneByPrice(string $price) Return the first ChildSProductVariantPrice filtered by the price column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSProductVariantPrice requireOneByFinalPrice(string $final_price) Return the first ChildSProductVariantPrice filtered by the final_price column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSProductVariantPrice requireOneByProductId(int $product_id) Return the first ChildSProductVariantPrice filtered by the product_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildSProductVariantPrice[]|ObjectCollection find(ConnectionInterface $con = null) Return ChildSProductVariantPrice objects based on current ModelCriteria
 * @method     ChildSProductVariantPrice[]|ObjectCollection findById(int $id) Return ChildSProductVariantPrice objects filtered by the id column
 * @method     ChildSProductVariantPrice[]|ObjectCollection findByVarId(int $var_id) Return ChildSProductVariantPrice objects filtered by the var_id column
 * @method     ChildSProductVariantPrice[]|ObjectCollection findByTypeId(int $type_id) Return ChildSProductVariantPrice objects filtered by the type_id column
 * @method     ChildSProductVariantPrice[]|ObjectCollection findByPrice(string $price) Return ChildSProductVariantPrice objects filtered by the price column
 * @method     ChildSProductVariantPrice[]|ObjectCollection findByFinalPrice(string $final_price) Return ChildSProductVariantPrice objects filtered by the final_price column
 * @method     ChildSProductVariantPrice[]|ObjectCollection findByProductId(int $product_id) Return ChildSProductVariantPrice objects filtered by the product_id column
 * @method     ChildSProductVariantPrice[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 *
 */
abstract class SProductVariantPriceQuery extends ModelCriteria
{
    protected $entityNotFoundExceptionClass = '\\Propel\\Runtime\\Exception\\EntityNotFoundException';

    /**
     * Initializes internal state of \Base\SProductVariantPriceQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'Shop', $modelName = '\\SProductVariantPrice', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildSProductVariantPriceQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildSProductVariantPriceQuery
     */
    public static function create($modelAlias = null, Criteria $criteria = null)
    {
        if ($criteria instanceof ChildSProductVariantPriceQuery) {
            return $criteria;
        }
        $query = new ChildSProductVariantPriceQuery();
        if (null !== $modelAlias) {
            $query->setModelAlias($modelAlias);
        }
        if ($criteria instanceof Criteria) {
            $query->mergeWith($criteria);
        }

        return $query;
    }

    /**
     * Find object by primary key.
     * Propel uses the instance pool to skip the database if the object exists.
     * Go fast if the query is untouched.
     *
     * <code>
     * $obj = $c->findPk(array(12, 34), $con);
     * </code>
     *
     * @param array[$id, $type_id] $key Primary key to use for the query
     * @param ConnectionInterface $con an optional connection object
     *
     * @return ChildSProductVariantPrice|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(SProductVariantPriceTableMap::DATABASE_NAME);
        }

        $this->basePreSelect($con);

        if (
            $this->formatter || $this->modelAlias || $this->with || $this->select
            || $this->selectColumns || $this->asColumns || $this->selectModifiers
            || $this->map || $this->having || $this->joins
        ) {
            return $this->findPkComplex($key, $con);
        }

        if ((null !== ($obj = SProductVariantPriceTableMap::getInstanceFromPool(serialize([(null === $key[0] || is_scalar($key[0]) || is_callable([$key[0], '__toString']) ? (string) $key[0] : $key[0]), (null === $key[1] || is_scalar($key[1]) || is_callable([$key[1], '__toString']) ? (string) $key[1] : $key[1])]))))) {
            // the object is already in the instance pool
            return $obj;
        }

        return $this->findPkSimple($key, $con);
    }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildSProductVariantPrice A model object, or null if the key is not found
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT id, var_id, type_id, price, final_price, product_id FROM shop_product_variants_prices WHERE id = :p0 AND type_id = :p1';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key[0], PDO::PARAM_INT);
            $stmt->bindValue(':p1', $key[1], PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), 0, $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
            /** @var ChildSProductVariantPrice $obj */
            $obj = new ChildSProductVariantPrice();
            $obj->hydrate($row);
            SProductVariantPriceTableMap::addInstanceToPool($obj, serialize([(null === $key[0] || is_scalar($key[0]) || is_callable([$key[0], '__toString']) ? (string) $key[0] : $key[0]), (null === $key[1] || is_scalar($key[1]) || is_callable([$key[1], '__toString']) ? (string) $key[1] : $key[1])]));
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @return ChildSProductVariantPrice|array|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, ConnectionInterface $con)
    {
        // As the query uses a PK condition, no limit(1) is necessary.
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKey($key)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->formatOne($dataFetcher);
    }

    /**
     * Find objects by primary key
     * <code>
     * $objs = $c->findPks(array(array(12, 56), array(832, 123), array(123, 456)), $con);
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return ObjectCollection|array|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getReadConnection($this->getDbName());
        }
        $this->basePreSelect($con);
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKeys($keys)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->format($dataFetcher);
    }

    /**
     * Filter the query by primary key
     *
     * @param     mixed $key Primary key to use for the query
     *
     * @return $this|ChildSProductVariantPriceQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {
        $this->addUsingAlias(SProductVariantPriceTableMap::COL_ID, $key[0], Criteria::EQUAL);
        $this->addUsingAlias(SProductVariantPriceTableMap::COL_TYPE_ID, $key[1], Criteria::EQUAL);

        return $this;
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return $this|ChildSProductVariantPriceQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {
        if (empty($keys)) {
            return $this->add(null, '1<>1', Criteria::CUSTOM);
        }
        foreach ($keys as $key) {
            $cton0 = $this->getNewCriterion(SProductVariantPriceTableMap::COL_ID, $key[0], Criteria::EQUAL);
            $cton1 = $this->getNewCriterion(SProductVariantPriceTableMap::COL_TYPE_ID, $key[1], Criteria::EQUAL);
            $cton0->addAnd($cton1);
            $this->addOr($cton0);
        }

        return $this;
    }

    /**
     * Filter the query on the id column
     *
     * Example usage:
     * <code>
     * $query->filterById(1234); // WHERE id = 1234
     * $query->filterById(array(12, 34)); // WHERE id IN (12, 34)
     * $query->filterById(array('min' => 12)); // WHERE id > 12
     * </code>
     *
     * @param     mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildSProductVariantPriceQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(SProductVariantPriceTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(SProductVariantPriceTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SProductVariantPriceTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the var_id column
     *
     * Example usage:
     * <code>
     * $query->filterByVarId(1234); // WHERE var_id = 1234
     * $query->filterByVarId(array(12, 34)); // WHERE var_id IN (12, 34)
     * $query->filterByVarId(array('min' => 12)); // WHERE var_id > 12
     * </code>
     *
     * @see       filterBySProductVariants()
     *
     * @param     mixed $varId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildSProductVariantPriceQuery The current query, for fluid interface
     */
    public function filterByVarId($varId = null, $comparison = null)
    {
        if (is_array($varId)) {
            $useMinMax = false;
            if (isset($varId['min'])) {
                $this->addUsingAlias(SProductVariantPriceTableMap::COL_VAR_ID, $varId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($varId['max'])) {
                $this->addUsingAlias(SProductVariantPriceTableMap::COL_VAR_ID, $varId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SProductVariantPriceTableMap::COL_VAR_ID, $varId, $comparison);
    }

    /**
     * Filter the query on the type_id column
     *
     * Example usage:
     * <code>
     * $query->filterByTypeId(1234); // WHERE type_id = 1234
     * $query->filterByTypeId(array(12, 34)); // WHERE type_id IN (12, 34)
     * $query->filterByTypeId(array('min' => 12)); // WHERE type_id > 12
     * </code>
     *
     * @see       filterBySProductVariantPriceType()
     *
     * @param     mixed $typeId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildSProductVariantPriceQuery The current query, for fluid interface
     */
    public function filterByTypeId($typeId = null, $comparison = null)
    {
        if (is_array($typeId)) {
            $useMinMax = false;
            if (isset($typeId['min'])) {
                $this->addUsingAlias(SProductVariantPriceTableMap::COL_TYPE_ID, $typeId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($typeId['max'])) {
                $this->addUsingAlias(SProductVariantPriceTableMap::COL_TYPE_ID, $typeId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SProductVariantPriceTableMap::COL_TYPE_ID, $typeId, $comparison);
    }

    /**
     * Filter the query on the price column
     *
     * Example usage:
     * <code>
     * $query->filterByPrice(1234); // WHERE price = 1234
     * $query->filterByPrice(array(12, 34)); // WHERE price IN (12, 34)
     * $query->filterByPrice(array('min' => 12)); // WHERE price > 12
     * </code>
     *
     * @param     mixed $price The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildSProductVariantPriceQuery The current query, for fluid interface
     */
    public function filterByPrice($price = null, $comparison = null)
    {
        if (is_array($price)) {
            $useMinMax = false;
            if (isset($price['min'])) {
                $this->addUsingAlias(SProductVariantPriceTableMap::COL_PRICE, $price['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($price['max'])) {
                $this->addUsingAlias(SProductVariantPriceTableMap::COL_PRICE, $price['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SProductVariantPriceTableMap::COL_PRICE, $price, $comparison);
    }

    /**
     * Filter the query on the final_price column
     *
     * Example usage:
     * <code>
     * $query->filterByFinalPrice(1234); // WHERE final_price = 1234
     * $query->filterByFinalPrice(array(12, 34)); // WHERE final_price IN (12, 34)
     * $query->filterByFinalPrice(array('min' => 12)); // WHERE final_price > 12
     * </code>
     *
     * @param     mixed $finalPrice The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildSProductVariantPriceQuery The current query, for fluid interface
     */
    public function filterByFinalPrice($finalPrice = null, $comparison = null)
    {
        if (is_array($finalPrice)) {
            $useMinMax = false;
            if (isset($finalPrice['min'])) {
                $this->addUsingAlias(SProductVariantPriceTableMap::COL_FINAL_PRICE, $finalPrice['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($finalPrice['max'])) {
                $this->addUsingAlias(SProductVariantPriceTableMap::COL_FINAL_PRICE, $finalPrice['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SProductVariantPriceTableMap::COL_FINAL_PRICE, $finalPrice, $comparison);
    }

    /**
     * Filter the query on the product_id column
     *
     * Example usage:
     * <code>
     * $query->filterByProductId(1234); // WHERE product_id = 1234
     * $query->filterByProductId(array(12, 34)); // WHERE product_id IN (12, 34)
     * $query->filterByProductId(array('min' => 12)); // WHERE product_id > 12
     * </code>
     *
     * @param     mixed $productId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildSProductVariantPriceQuery The current query, for fluid interface
     */
    public function filterByProductId($productId = null, $comparison = null)
    {
        if (is_array($productId)) {
            $useMinMax = false;
            if (isset($productId['min'])) {
                $this->addUsingAlias(SProductVariantPriceTableMap::COL_PRODUCT_ID, $productId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($productId['max'])) {
                $this->addUsingAlias(SProductVariantPriceTableMap::COL_PRODUCT_ID, $productId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SProductVariantPriceTableMap::COL_PRODUCT_ID, $productId, $comparison);
    }

    /**
     * Filter the query by a related \SProductVariantPriceType object
     *
     * @param \SProductVariantPriceType|ObjectCollection $sProductVariantPriceType The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildSProductVariantPriceQuery The current query, for fluid interface
     */
    public function filterBySProductVariantPriceType($sProductVariantPriceType, $comparison = null)
    {
        if ($sProductVariantPriceType instanceof \SProductVariantPriceType) {
            return $this
                ->addUsingAlias(SProductVariantPriceTableMap::COL_TYPE_ID, $sProductVariantPriceType->getId(), $comparison);
        } elseif ($sProductVariantPriceType instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(SProductVariantPriceTableMap::COL_TYPE_ID, $sProductVariantPriceType->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterBySProductVariantPriceType() only accepts arguments of type \SProductVariantPriceType or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the SProductVariantPriceType relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildSProductVariantPriceQuery The current query, for fluid interface
     */
    public function joinSProductVariantPriceType($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('SProductVariantPriceType');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'SProductVariantPriceType');
        }

        return $this;
    }

    /**
     * Use the SProductVariantPriceType relation SProductVariantPriceType object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \SProductVariantPriceTypeQuery A secondary query class using the current class as primary query
     */
    public function useSProductVariantPriceTypeQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinSProductVariantPriceType($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'SProductVariantPriceType', '\SProductVariantPriceTypeQuery');
    }

    /**
     * Filter the query by a related \SProductVariants object
     *
     * @param \SProductVariants|ObjectCollection $sProductVariants The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildSProductVariantPriceQuery The current query, for fluid interface
     */
    public function filterBySProductVariants($sProductVariants, $comparison = null)
    {
        if ($sProductVariants instanceof \SProductVariants) {
            return $this
                ->addUsingAlias(SProductVariantPriceTableMap::COL_VAR_ID, $sProductVariants->getId(), $comparison);
        } elseif ($sProductVariants instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(SProductVariantPriceTableMap::COL_VAR_ID, $sProductVariants->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterBySProductVariants() only accepts arguments of type \SProductVariants or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the SProductVariants relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildSProductVariantPriceQuery The current query, for fluid interface
     */
    public function joinSProductVariants($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('SProductVariants');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'SProductVariants');
        }

        return $this;
    }

    /**
     * Use the SProductVariants relation SProductVariants object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \SProductVariantsQuery A secondary query class using the current class as primary query
     */
    public function useSProductVariantsQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinSProductVariants($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'SProductVariants', '\SProductVariantsQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildSProductVariantPrice $sProductVariantPrice Object to remove from the list of results
     *
     * @return $this|ChildSProductVariantPriceQuery The current query, for fluid interface
     */
    public function prune($sProductVariantPrice = null)
    {
        if ($sProductVariantPrice) {
            $this->addCond('pruneCond0', $this->getAliasedColName(SProductVariantPriceTableMap::COL_ID), $sProductVariantPrice->getId(), Criteria::NOT_EQUAL);
            $this->addCond('pruneCond1', $this->getAliasedColName(SProductVariantPriceTableMap::COL_TYPE_ID), $sProductVariantPrice->getTypeId(), Criteria::NOT_EQUAL);
            $this->combine(array('pruneCond0', 'pruneCond1'), Criteria::LOGICAL_OR);
        }

        return $this;
    }

    /**
     * Deletes all rows from the shop_product_variants_prices table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(SProductVariantPriceTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            SProductVariantPriceTableMap::clearInstancePool();
            SProductVariantPriceTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

    /**
     * Performs a DELETE on the database based on the current ModelCriteria
     *
     * @param ConnectionInterface $con the connection to use
     * @return int             The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                         if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public function delete(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(SProductVariantPriceTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(SProductVariantPriceTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            SProductVariantPriceTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            SProductVariantPriceTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

} // SProductVariantPriceQuery
