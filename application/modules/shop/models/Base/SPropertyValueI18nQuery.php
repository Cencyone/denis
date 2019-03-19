<?php

namespace Base;

use \SPropertyValueI18n as ChildSPropertyValueI18n;
use \SPropertyValueI18nQuery as ChildSPropertyValueI18nQuery;
use \Exception;
use \PDO;
use Map\SPropertyValueI18nTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'shop_product_property_value_i18n' table.
 *
 *
 *
 * @method     ChildSPropertyValueI18nQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildSPropertyValueI18nQuery orderByLocale($order = Criteria::ASC) Order by the locale column
 * @method     ChildSPropertyValueI18nQuery orderByValue($order = Criteria::ASC) Order by the value column
 *
 * @method     ChildSPropertyValueI18nQuery groupById() Group by the id column
 * @method     ChildSPropertyValueI18nQuery groupByLocale() Group by the locale column
 * @method     ChildSPropertyValueI18nQuery groupByValue() Group by the value column
 *
 * @method     ChildSPropertyValueI18nQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildSPropertyValueI18nQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildSPropertyValueI18nQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildSPropertyValueI18nQuery leftJoinWith($relation) Adds a LEFT JOIN clause and with to the query
 * @method     ChildSPropertyValueI18nQuery rightJoinWith($relation) Adds a RIGHT JOIN clause and with to the query
 * @method     ChildSPropertyValueI18nQuery innerJoinWith($relation) Adds a INNER JOIN clause and with to the query
 *
 * @method     ChildSPropertyValueI18nQuery leftJoinSPropertyValue($relationAlias = null) Adds a LEFT JOIN clause to the query using the SPropertyValue relation
 * @method     ChildSPropertyValueI18nQuery rightJoinSPropertyValue($relationAlias = null) Adds a RIGHT JOIN clause to the query using the SPropertyValue relation
 * @method     ChildSPropertyValueI18nQuery innerJoinSPropertyValue($relationAlias = null) Adds a INNER JOIN clause to the query using the SPropertyValue relation
 *
 * @method     ChildSPropertyValueI18nQuery joinWithSPropertyValue($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the SPropertyValue relation
 *
 * @method     ChildSPropertyValueI18nQuery leftJoinWithSPropertyValue() Adds a LEFT JOIN clause and with to the query using the SPropertyValue relation
 * @method     ChildSPropertyValueI18nQuery rightJoinWithSPropertyValue() Adds a RIGHT JOIN clause and with to the query using the SPropertyValue relation
 * @method     ChildSPropertyValueI18nQuery innerJoinWithSPropertyValue() Adds a INNER JOIN clause and with to the query using the SPropertyValue relation
 *
 * @method     \SPropertyValueQuery endUse() Finalizes a secondary criteria and merges it with its primary Criteria
 *
 * @method     ChildSPropertyValueI18n findOne(ConnectionInterface $con = null) Return the first ChildSPropertyValueI18n matching the query
 * @method     ChildSPropertyValueI18n findOneOrCreate(ConnectionInterface $con = null) Return the first ChildSPropertyValueI18n matching the query, or a new ChildSPropertyValueI18n object populated from the query conditions when no match is found
 *
 * @method     ChildSPropertyValueI18n findOneById(int $id) Return the first ChildSPropertyValueI18n filtered by the id column
 * @method     ChildSPropertyValueI18n findOneByLocale(string $locale) Return the first ChildSPropertyValueI18n filtered by the locale column
 * @method     ChildSPropertyValueI18n findOneByValue(string $value) Return the first ChildSPropertyValueI18n filtered by the value column *

 * @method     ChildSPropertyValueI18n requirePk($key, ConnectionInterface $con = null) Return the ChildSPropertyValueI18n by primary key and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSPropertyValueI18n requireOne(ConnectionInterface $con = null) Return the first ChildSPropertyValueI18n matching the query and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildSPropertyValueI18n requireOneById(int $id) Return the first ChildSPropertyValueI18n filtered by the id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSPropertyValueI18n requireOneByLocale(string $locale) Return the first ChildSPropertyValueI18n filtered by the locale column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSPropertyValueI18n requireOneByValue(string $value) Return the first ChildSPropertyValueI18n filtered by the value column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildSPropertyValueI18n[]|ObjectCollection find(ConnectionInterface $con = null) Return ChildSPropertyValueI18n objects based on current ModelCriteria
 * @method     ChildSPropertyValueI18n[]|ObjectCollection findById(int $id) Return ChildSPropertyValueI18n objects filtered by the id column
 * @method     ChildSPropertyValueI18n[]|ObjectCollection findByLocale(string $locale) Return ChildSPropertyValueI18n objects filtered by the locale column
 * @method     ChildSPropertyValueI18n[]|ObjectCollection findByValue(string $value) Return ChildSPropertyValueI18n objects filtered by the value column
 * @method     ChildSPropertyValueI18n[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 *
 */
abstract class SPropertyValueI18nQuery extends ModelCriteria
{
    protected $entityNotFoundExceptionClass = '\\Propel\\Runtime\\Exception\\EntityNotFoundException';

    /**
     * Initializes internal state of \Base\SPropertyValueI18nQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'Shop', $modelName = '\\SPropertyValueI18n', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildSPropertyValueI18nQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildSPropertyValueI18nQuery
     */
    public static function create($modelAlias = null, Criteria $criteria = null)
    {
        if ($criteria instanceof ChildSPropertyValueI18nQuery) {
            return $criteria;
        }
        $query = new ChildSPropertyValueI18nQuery();
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
     * @param array[$id, $locale] $key Primary key to use for the query
     * @param ConnectionInterface $con an optional connection object
     *
     * @return ChildSPropertyValueI18n|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(SPropertyValueI18nTableMap::DATABASE_NAME);
        }

        $this->basePreSelect($con);

        if (
            $this->formatter || $this->modelAlias || $this->with || $this->select
            || $this->selectColumns || $this->asColumns || $this->selectModifiers
            || $this->map || $this->having || $this->joins
        ) {
            return $this->findPkComplex($key, $con);
        }

        if ((null !== ($obj = SPropertyValueI18nTableMap::getInstanceFromPool(serialize([(null === $key[0] || is_scalar($key[0]) || is_callable([$key[0], '__toString']) ? (string) $key[0] : $key[0]), (null === $key[1] || is_scalar($key[1]) || is_callable([$key[1], '__toString']) ? (string) $key[1] : $key[1])]))))) {
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
     * @return ChildSPropertyValueI18n A model object, or null if the key is not found
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT id, locale, value FROM shop_product_property_value_i18n WHERE id = :p0 AND locale = :p1';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key[0], PDO::PARAM_INT);
            $stmt->bindValue(':p1', $key[1], PDO::PARAM_STR);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), 0, $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
            /** @var ChildSPropertyValueI18n $obj */
            $obj = new ChildSPropertyValueI18n();
            $obj->hydrate($row);
            SPropertyValueI18nTableMap::addInstanceToPool($obj, serialize([(null === $key[0] || is_scalar($key[0]) || is_callable([$key[0], '__toString']) ? (string) $key[0] : $key[0]), (null === $key[1] || is_scalar($key[1]) || is_callable([$key[1], '__toString']) ? (string) $key[1] : $key[1])]));
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
     * @return ChildSPropertyValueI18n|array|mixed the result, formatted by the current formatter
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
     * @return $this|ChildSPropertyValueI18nQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {
        $this->addUsingAlias(SPropertyValueI18nTableMap::COL_ID, $key[0], Criteria::EQUAL);
        $this->addUsingAlias(SPropertyValueI18nTableMap::COL_LOCALE, $key[1], Criteria::EQUAL);

        return $this;
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return $this|ChildSPropertyValueI18nQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {
        if (empty($keys)) {
            return $this->add(null, '1<>1', Criteria::CUSTOM);
        }
        foreach ($keys as $key) {
            $cton0 = $this->getNewCriterion(SPropertyValueI18nTableMap::COL_ID, $key[0], Criteria::EQUAL);
            $cton1 = $this->getNewCriterion(SPropertyValueI18nTableMap::COL_LOCALE, $key[1], Criteria::EQUAL);
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
     * @see       filterBySPropertyValue()
     *
     * @param     mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildSPropertyValueI18nQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(SPropertyValueI18nTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(SPropertyValueI18nTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SPropertyValueI18nTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the locale column
     *
     * Example usage:
     * <code>
     * $query->filterByLocale('fooValue');   // WHERE locale = 'fooValue'
     * $query->filterByLocale('%fooValue%', Criteria::LIKE); // WHERE locale LIKE '%fooValue%'
     * </code>
     *
     * @param     string $locale The value to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildSPropertyValueI18nQuery The current query, for fluid interface
     */
    public function filterByLocale($locale = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($locale)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SPropertyValueI18nTableMap::COL_LOCALE, $locale, $comparison);
    }

    /**
     * Filter the query on the value column
     *
     * Example usage:
     * <code>
     * $query->filterByValue('fooValue');   // WHERE value = 'fooValue'
     * $query->filterByValue('%fooValue%', Criteria::LIKE); // WHERE value LIKE '%fooValue%'
     * </code>
     *
     * @param     string $value The value to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildSPropertyValueI18nQuery The current query, for fluid interface
     */
    public function filterByValue($value = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($value)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SPropertyValueI18nTableMap::COL_VALUE, $value, $comparison);
    }

    /**
     * Filter the query by a related \SPropertyValue object
     *
     * @param \SPropertyValue|ObjectCollection $sPropertyValue The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildSPropertyValueI18nQuery The current query, for fluid interface
     */
    public function filterBySPropertyValue($sPropertyValue, $comparison = null)
    {
        if ($sPropertyValue instanceof \SPropertyValue) {
            return $this
                ->addUsingAlias(SPropertyValueI18nTableMap::COL_ID, $sPropertyValue->getId(), $comparison);
        } elseif ($sPropertyValue instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(SPropertyValueI18nTableMap::COL_ID, $sPropertyValue->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterBySPropertyValue() only accepts arguments of type \SPropertyValue or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the SPropertyValue relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildSPropertyValueI18nQuery The current query, for fluid interface
     */
    public function joinSPropertyValue($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('SPropertyValue');

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
            $this->addJoinObject($join, 'SPropertyValue');
        }

        return $this;
    }

    /**
     * Use the SPropertyValue relation SPropertyValue object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \SPropertyValueQuery A secondary query class using the current class as primary query
     */
    public function useSPropertyValueQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinSPropertyValue($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'SPropertyValue', '\SPropertyValueQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildSPropertyValueI18n $sPropertyValueI18n Object to remove from the list of results
     *
     * @return $this|ChildSPropertyValueI18nQuery The current query, for fluid interface
     */
    public function prune($sPropertyValueI18n = null)
    {
        if ($sPropertyValueI18n) {
            $this->addCond('pruneCond0', $this->getAliasedColName(SPropertyValueI18nTableMap::COL_ID), $sPropertyValueI18n->getId(), Criteria::NOT_EQUAL);
            $this->addCond('pruneCond1', $this->getAliasedColName(SPropertyValueI18nTableMap::COL_LOCALE), $sPropertyValueI18n->getLocale(), Criteria::NOT_EQUAL);
            $this->combine(array('pruneCond0', 'pruneCond1'), Criteria::LOGICAL_OR);
        }

        return $this;
    }

    /**
     * Deletes all rows from the shop_product_property_value_i18n table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(SPropertyValueI18nTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            SPropertyValueI18nTableMap::clearInstancePool();
            SPropertyValueI18nTableMap::clearRelatedInstancePool();

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
            $con = Propel::getServiceContainer()->getWriteConnection(SPropertyValueI18nTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(SPropertyValueI18nTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            SPropertyValueI18nTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            SPropertyValueI18nTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

} // SPropertyValueI18nQuery
