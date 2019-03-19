<?php

namespace Base;

use \SProductVariantPriceTypeValue as ChildSProductVariantPriceTypeValue;
use \SProductVariantPriceTypeValueQuery as ChildSProductVariantPriceTypeValueQuery;
use \Exception;
use \PDO;
use Map\SProductVariantPriceTypeValueTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'shop_product_variants_price_type_values' table.
 *
 *
 *
 * @method     ChildSProductVariantPriceTypeValueQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildSProductVariantPriceTypeValueQuery orderByPriceTypeId($order = Criteria::ASC) Order by the price_type_id column
 * @method     ChildSProductVariantPriceTypeValueQuery orderByValue($order = Criteria::ASC) Order by the value column
 *
 * @method     ChildSProductVariantPriceTypeValueQuery groupById() Group by the id column
 * @method     ChildSProductVariantPriceTypeValueQuery groupByPriceTypeId() Group by the price_type_id column
 * @method     ChildSProductVariantPriceTypeValueQuery groupByValue() Group by the value column
 *
 * @method     ChildSProductVariantPriceTypeValueQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildSProductVariantPriceTypeValueQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildSProductVariantPriceTypeValueQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildSProductVariantPriceTypeValueQuery leftJoinWith($relation) Adds a LEFT JOIN clause and with to the query
 * @method     ChildSProductVariantPriceTypeValueQuery rightJoinWith($relation) Adds a RIGHT JOIN clause and with to the query
 * @method     ChildSProductVariantPriceTypeValueQuery innerJoinWith($relation) Adds a INNER JOIN clause and with to the query
 *
 * @method     ChildSProductVariantPriceTypeValueQuery leftJoinSProductVariantPriceType($relationAlias = null) Adds a LEFT JOIN clause to the query using the SProductVariantPriceType relation
 * @method     ChildSProductVariantPriceTypeValueQuery rightJoinSProductVariantPriceType($relationAlias = null) Adds a RIGHT JOIN clause to the query using the SProductVariantPriceType relation
 * @method     ChildSProductVariantPriceTypeValueQuery innerJoinSProductVariantPriceType($relationAlias = null) Adds a INNER JOIN clause to the query using the SProductVariantPriceType relation
 *
 * @method     ChildSProductVariantPriceTypeValueQuery joinWithSProductVariantPriceType($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the SProductVariantPriceType relation
 *
 * @method     ChildSProductVariantPriceTypeValueQuery leftJoinWithSProductVariantPriceType() Adds a LEFT JOIN clause and with to the query using the SProductVariantPriceType relation
 * @method     ChildSProductVariantPriceTypeValueQuery rightJoinWithSProductVariantPriceType() Adds a RIGHT JOIN clause and with to the query using the SProductVariantPriceType relation
 * @method     ChildSProductVariantPriceTypeValueQuery innerJoinWithSProductVariantPriceType() Adds a INNER JOIN clause and with to the query using the SProductVariantPriceType relation
 *
 * @method     \SProductVariantPriceTypeQuery endUse() Finalizes a secondary criteria and merges it with its primary Criteria
 *
 * @method     ChildSProductVariantPriceTypeValue findOne(ConnectionInterface $con = null) Return the first ChildSProductVariantPriceTypeValue matching the query
 * @method     ChildSProductVariantPriceTypeValue findOneOrCreate(ConnectionInterface $con = null) Return the first ChildSProductVariantPriceTypeValue matching the query, or a new ChildSProductVariantPriceTypeValue object populated from the query conditions when no match is found
 *
 * @method     ChildSProductVariantPriceTypeValue findOneById(int $id) Return the first ChildSProductVariantPriceTypeValue filtered by the id column
 * @method     ChildSProductVariantPriceTypeValue findOneByPriceTypeId(int $price_type_id) Return the first ChildSProductVariantPriceTypeValue filtered by the price_type_id column
 * @method     ChildSProductVariantPriceTypeValue findOneByValue(int $value) Return the first ChildSProductVariantPriceTypeValue filtered by the value column *

 * @method     ChildSProductVariantPriceTypeValue requirePk($key, ConnectionInterface $con = null) Return the ChildSProductVariantPriceTypeValue by primary key and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSProductVariantPriceTypeValue requireOne(ConnectionInterface $con = null) Return the first ChildSProductVariantPriceTypeValue matching the query and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildSProductVariantPriceTypeValue requireOneById(int $id) Return the first ChildSProductVariantPriceTypeValue filtered by the id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSProductVariantPriceTypeValue requireOneByPriceTypeId(int $price_type_id) Return the first ChildSProductVariantPriceTypeValue filtered by the price_type_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSProductVariantPriceTypeValue requireOneByValue(int $value) Return the first ChildSProductVariantPriceTypeValue filtered by the value column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildSProductVariantPriceTypeValue[]|ObjectCollection find(ConnectionInterface $con = null) Return ChildSProductVariantPriceTypeValue objects based on current ModelCriteria
 * @method     ChildSProductVariantPriceTypeValue[]|ObjectCollection findById(int $id) Return ChildSProductVariantPriceTypeValue objects filtered by the id column
 * @method     ChildSProductVariantPriceTypeValue[]|ObjectCollection findByPriceTypeId(int $price_type_id) Return ChildSProductVariantPriceTypeValue objects filtered by the price_type_id column
 * @method     ChildSProductVariantPriceTypeValue[]|ObjectCollection findByValue(int $value) Return ChildSProductVariantPriceTypeValue objects filtered by the value column
 * @method     ChildSProductVariantPriceTypeValue[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 *
 */
abstract class SProductVariantPriceTypeValueQuery extends ModelCriteria
{
    protected $entityNotFoundExceptionClass = '\\Propel\\Runtime\\Exception\\EntityNotFoundException';

    /**
     * Initializes internal state of \Base\SProductVariantPriceTypeValueQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'Shop', $modelName = '\\SProductVariantPriceTypeValue', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildSProductVariantPriceTypeValueQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildSProductVariantPriceTypeValueQuery
     */
    public static function create($modelAlias = null, Criteria $criteria = null)
    {
        if ($criteria instanceof ChildSProductVariantPriceTypeValueQuery) {
            return $criteria;
        }
        $query = new ChildSProductVariantPriceTypeValueQuery();
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
     * $obj  = $c->findPk(12, $con);
     * </code>
     *
     * @param mixed $key Primary key to use for the query
     * @param ConnectionInterface $con an optional connection object
     *
     * @return ChildSProductVariantPriceTypeValue|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(SProductVariantPriceTypeValueTableMap::DATABASE_NAME);
        }

        $this->basePreSelect($con);

        if (
            $this->formatter || $this->modelAlias || $this->with || $this->select
            || $this->selectColumns || $this->asColumns || $this->selectModifiers
            || $this->map || $this->having || $this->joins
        ) {
            return $this->findPkComplex($key, $con);
        }

        if ((null !== ($obj = SProductVariantPriceTypeValueTableMap::getInstanceFromPool(null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key)))) {
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
     * @return ChildSProductVariantPriceTypeValue A model object, or null if the key is not found
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT id, price_type_id, value FROM shop_product_variants_price_type_values WHERE id = :p0';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), 0, $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
            /** @var ChildSProductVariantPriceTypeValue $obj */
            $obj = new ChildSProductVariantPriceTypeValue();
            $obj->hydrate($row);
            SProductVariantPriceTypeValueTableMap::addInstanceToPool($obj, null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key);
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
     * @return ChildSProductVariantPriceTypeValue|array|mixed the result, formatted by the current formatter
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
     * $objs = $c->findPks(array(12, 56, 832), $con);
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
     * @return $this|ChildSProductVariantPriceTypeValueQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(SProductVariantPriceTypeValueTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return $this|ChildSProductVariantPriceTypeValueQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(SProductVariantPriceTypeValueTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return $this|ChildSProductVariantPriceTypeValueQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(SProductVariantPriceTypeValueTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(SProductVariantPriceTypeValueTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SProductVariantPriceTypeValueTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the price_type_id column
     *
     * Example usage:
     * <code>
     * $query->filterByPriceTypeId(1234); // WHERE price_type_id = 1234
     * $query->filterByPriceTypeId(array(12, 34)); // WHERE price_type_id IN (12, 34)
     * $query->filterByPriceTypeId(array('min' => 12)); // WHERE price_type_id > 12
     * </code>
     *
     * @see       filterBySProductVariantPriceType()
     *
     * @param     mixed $priceTypeId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildSProductVariantPriceTypeValueQuery The current query, for fluid interface
     */
    public function filterByPriceTypeId($priceTypeId = null, $comparison = null)
    {
        if (is_array($priceTypeId)) {
            $useMinMax = false;
            if (isset($priceTypeId['min'])) {
                $this->addUsingAlias(SProductVariantPriceTypeValueTableMap::COL_PRICE_TYPE_ID, $priceTypeId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($priceTypeId['max'])) {
                $this->addUsingAlias(SProductVariantPriceTypeValueTableMap::COL_PRICE_TYPE_ID, $priceTypeId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SProductVariantPriceTypeValueTableMap::COL_PRICE_TYPE_ID, $priceTypeId, $comparison);
    }

    /**
     * Filter the query on the value column
     *
     * Example usage:
     * <code>
     * $query->filterByValue(1234); // WHERE value = 1234
     * $query->filterByValue(array(12, 34)); // WHERE value IN (12, 34)
     * $query->filterByValue(array('min' => 12)); // WHERE value > 12
     * </code>
     *
     * @param     mixed $value The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildSProductVariantPriceTypeValueQuery The current query, for fluid interface
     */
    public function filterByValue($value = null, $comparison = null)
    {
        if (is_array($value)) {
            $useMinMax = false;
            if (isset($value['min'])) {
                $this->addUsingAlias(SProductVariantPriceTypeValueTableMap::COL_VALUE, $value['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($value['max'])) {
                $this->addUsingAlias(SProductVariantPriceTypeValueTableMap::COL_VALUE, $value['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SProductVariantPriceTypeValueTableMap::COL_VALUE, $value, $comparison);
    }

    /**
     * Filter the query by a related \SProductVariantPriceType object
     *
     * @param \SProductVariantPriceType|ObjectCollection $sProductVariantPriceType The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildSProductVariantPriceTypeValueQuery The current query, for fluid interface
     */
    public function filterBySProductVariantPriceType($sProductVariantPriceType, $comparison = null)
    {
        if ($sProductVariantPriceType instanceof \SProductVariantPriceType) {
            return $this
                ->addUsingAlias(SProductVariantPriceTypeValueTableMap::COL_PRICE_TYPE_ID, $sProductVariantPriceType->getId(), $comparison);
        } elseif ($sProductVariantPriceType instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(SProductVariantPriceTypeValueTableMap::COL_PRICE_TYPE_ID, $sProductVariantPriceType->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return $this|ChildSProductVariantPriceTypeValueQuery The current query, for fluid interface
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
     * Exclude object from result
     *
     * @param   ChildSProductVariantPriceTypeValue $sProductVariantPriceTypeValue Object to remove from the list of results
     *
     * @return $this|ChildSProductVariantPriceTypeValueQuery The current query, for fluid interface
     */
    public function prune($sProductVariantPriceTypeValue = null)
    {
        if ($sProductVariantPriceTypeValue) {
            $this->addUsingAlias(SProductVariantPriceTypeValueTableMap::COL_ID, $sProductVariantPriceTypeValue->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the shop_product_variants_price_type_values table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(SProductVariantPriceTypeValueTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            SProductVariantPriceTypeValueTableMap::clearInstancePool();
            SProductVariantPriceTypeValueTableMap::clearRelatedInstancePool();

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
            $con = Propel::getServiceContainer()->getWriteConnection(SProductVariantPriceTypeValueTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(SProductVariantPriceTypeValueTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            SProductVariantPriceTypeValueTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            SProductVariantPriceTypeValueTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

} // SProductVariantPriceTypeValueQuery
