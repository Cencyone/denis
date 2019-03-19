<?php

namespace Base;

use \SSorting as ChildSSorting;
use \SSortingI18nQuery as ChildSSortingI18nQuery;
use \SSortingQuery as ChildSSortingQuery;
use \Exception;
use \PDO;
use Map\SSortingI18nTableMap;
use Map\SSortingTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'shop_sorting' table.
 *
 *
 *
 * @method     ChildSSortingQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildSSortingQuery orderByPos($order = Criteria::ASC) Order by the pos column
 * @method     ChildSSortingQuery orderByGet($order = Criteria::ASC) Order by the get column
 * @method     ChildSSortingQuery orderByActive($order = Criteria::ASC) Order by the active column
 *
 * @method     ChildSSortingQuery groupById() Group by the id column
 * @method     ChildSSortingQuery groupByPos() Group by the pos column
 * @method     ChildSSortingQuery groupByGet() Group by the get column
 * @method     ChildSSortingQuery groupByActive() Group by the active column
 *
 * @method     ChildSSortingQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildSSortingQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildSSortingQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildSSortingQuery leftJoinWith($relation) Adds a LEFT JOIN clause and with to the query
 * @method     ChildSSortingQuery rightJoinWith($relation) Adds a RIGHT JOIN clause and with to the query
 * @method     ChildSSortingQuery innerJoinWith($relation) Adds a INNER JOIN clause and with to the query
 *
 * @method     ChildSSortingQuery leftJoinSSortingI18n($relationAlias = null) Adds a LEFT JOIN clause to the query using the SSortingI18n relation
 * @method     ChildSSortingQuery rightJoinSSortingI18n($relationAlias = null) Adds a RIGHT JOIN clause to the query using the SSortingI18n relation
 * @method     ChildSSortingQuery innerJoinSSortingI18n($relationAlias = null) Adds a INNER JOIN clause to the query using the SSortingI18n relation
 *
 * @method     ChildSSortingQuery joinWithSSortingI18n($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the SSortingI18n relation
 *
 * @method     ChildSSortingQuery leftJoinWithSSortingI18n() Adds a LEFT JOIN clause and with to the query using the SSortingI18n relation
 * @method     ChildSSortingQuery rightJoinWithSSortingI18n() Adds a RIGHT JOIN clause and with to the query using the SSortingI18n relation
 * @method     ChildSSortingQuery innerJoinWithSSortingI18n() Adds a INNER JOIN clause and with to the query using the SSortingI18n relation
 *
 * @method     \SSortingI18nQuery endUse() Finalizes a secondary criteria and merges it with its primary Criteria
 *
 * @method     ChildSSorting findOne(ConnectionInterface $con = null) Return the first ChildSSorting matching the query
 * @method     ChildSSorting findOneOrCreate(ConnectionInterface $con = null) Return the first ChildSSorting matching the query, or a new ChildSSorting object populated from the query conditions when no match is found
 *
 * @method     ChildSSorting findOneById(int $id) Return the first ChildSSorting filtered by the id column
 * @method     ChildSSorting findOneByPos(int $pos) Return the first ChildSSorting filtered by the pos column
 * @method     ChildSSorting findOneByGet(string $get) Return the first ChildSSorting filtered by the get column
 * @method     ChildSSorting findOneByActive(boolean $active) Return the first ChildSSorting filtered by the active column *

 * @method     ChildSSorting requirePk($key, ConnectionInterface $con = null) Return the ChildSSorting by primary key and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSSorting requireOne(ConnectionInterface $con = null) Return the first ChildSSorting matching the query and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildSSorting requireOneById(int $id) Return the first ChildSSorting filtered by the id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSSorting requireOneByPos(int $pos) Return the first ChildSSorting filtered by the pos column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSSorting requireOneByGet(string $get) Return the first ChildSSorting filtered by the get column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSSorting requireOneByActive(boolean $active) Return the first ChildSSorting filtered by the active column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildSSorting[]|ObjectCollection find(ConnectionInterface $con = null) Return ChildSSorting objects based on current ModelCriteria
 * @method     ChildSSorting[]|ObjectCollection findById(int $id) Return ChildSSorting objects filtered by the id column
 * @method     ChildSSorting[]|ObjectCollection findByPos(int $pos) Return ChildSSorting objects filtered by the pos column
 * @method     ChildSSorting[]|ObjectCollection findByGet(string $get) Return ChildSSorting objects filtered by the get column
 * @method     ChildSSorting[]|ObjectCollection findByActive(boolean $active) Return ChildSSorting objects filtered by the active column
 * @method     ChildSSorting[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 *
 */
abstract class SSortingQuery extends ModelCriteria
{
    protected $entityNotFoundExceptionClass = '\\Propel\\Runtime\\Exception\\EntityNotFoundException';

    /**
     * Initializes internal state of \Base\SSortingQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'Shop', $modelName = '\\SSorting', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildSSortingQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildSSortingQuery
     */
    public static function create($modelAlias = null, Criteria $criteria = null)
    {
        if ($criteria instanceof ChildSSortingQuery) {
            return $criteria;
        }
        $query = new ChildSSortingQuery();
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
     * @return ChildSSorting|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(SSortingTableMap::DATABASE_NAME);
        }

        $this->basePreSelect($con);

        if (
            $this->formatter || $this->modelAlias || $this->with || $this->select
            || $this->selectColumns || $this->asColumns || $this->selectModifiers
            || $this->map || $this->having || $this->joins
        ) {
            return $this->findPkComplex($key, $con);
        }

        if ((null !== ($obj = SSortingTableMap::getInstanceFromPool(null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key)))) {
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
     * @return ChildSSorting A model object, or null if the key is not found
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT id, pos, get, active FROM shop_sorting WHERE id = :p0';
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
            /** @var ChildSSorting $obj */
            $obj = new ChildSSorting();
            $obj->hydrate($row);
            SSortingTableMap::addInstanceToPool($obj, null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key);
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
     * @return ChildSSorting|array|mixed the result, formatted by the current formatter
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
     * @return $this|ChildSSortingQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(SSortingTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return $this|ChildSSortingQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(SSortingTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return $this|ChildSSortingQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(SSortingTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(SSortingTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SSortingTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the pos column
     *
     * Example usage:
     * <code>
     * $query->filterByPos(1234); // WHERE pos = 1234
     * $query->filterByPos(array(12, 34)); // WHERE pos IN (12, 34)
     * $query->filterByPos(array('min' => 12)); // WHERE pos > 12
     * </code>
     *
     * @param     mixed $pos The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildSSortingQuery The current query, for fluid interface
     */
    public function filterByPos($pos = null, $comparison = null)
    {
        if (is_array($pos)) {
            $useMinMax = false;
            if (isset($pos['min'])) {
                $this->addUsingAlias(SSortingTableMap::COL_POS, $pos['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($pos['max'])) {
                $this->addUsingAlias(SSortingTableMap::COL_POS, $pos['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SSortingTableMap::COL_POS, $pos, $comparison);
    }

    /**
     * Filter the query on the get column
     *
     * Example usage:
     * <code>
     * $query->filterByGet('fooValue');   // WHERE get = 'fooValue'
     * $query->filterByGet('%fooValue%', Criteria::LIKE); // WHERE get LIKE '%fooValue%'
     * </code>
     *
     * @param     string $get The value to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildSSortingQuery The current query, for fluid interface
     */
    public function filterByGet($get = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($get)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SSortingTableMap::COL_GET, $get, $comparison);
    }

    /**
     * Filter the query on the active column
     *
     * Example usage:
     * <code>
     * $query->filterByActive(true); // WHERE active = true
     * $query->filterByActive('yes'); // WHERE active = true
     * </code>
     *
     * @param     boolean|string $active The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildSSortingQuery The current query, for fluid interface
     */
    public function filterByActive($active = null, $comparison = null)
    {
        if (is_string($active)) {
            $active = in_array(strtolower($active), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(SSortingTableMap::COL_ACTIVE, $active, $comparison);
    }

    /**
     * Filter the query by a related \SSortingI18n object
     *
     * @param \SSortingI18n|ObjectCollection $sSortingI18n the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSSortingQuery The current query, for fluid interface
     */
    public function filterBySSortingI18n($sSortingI18n, $comparison = null)
    {
        if ($sSortingI18n instanceof \SSortingI18n) {
            return $this
                ->addUsingAlias(SSortingTableMap::COL_ID, $sSortingI18n->getId(), $comparison);
        } elseif ($sSortingI18n instanceof ObjectCollection) {
            return $this
                ->useSSortingI18nQuery()
                ->filterByPrimaryKeys($sSortingI18n->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterBySSortingI18n() only accepts arguments of type \SSortingI18n or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the SSortingI18n relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildSSortingQuery The current query, for fluid interface
     */
    public function joinSSortingI18n($relationAlias = null, $joinType = 'LEFT JOIN')
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('SSortingI18n');

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
            $this->addJoinObject($join, 'SSortingI18n');
        }

        return $this;
    }

    /**
     * Use the SSortingI18n relation SSortingI18n object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \SSortingI18nQuery A secondary query class using the current class as primary query
     */
    public function useSSortingI18nQuery($relationAlias = null, $joinType = 'LEFT JOIN')
    {
        return $this
            ->joinSSortingI18n($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'SSortingI18n', '\SSortingI18nQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildSSorting $sSorting Object to remove from the list of results
     *
     * @return $this|ChildSSortingQuery The current query, for fluid interface
     */
    public function prune($sSorting = null)
    {
        if ($sSorting) {
            $this->addUsingAlias(SSortingTableMap::COL_ID, $sSorting->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the shop_sorting table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(SSortingTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += $this->doOnDeleteCascade($con);
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            SSortingTableMap::clearInstancePool();
            SSortingTableMap::clearRelatedInstancePool();

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
            $con = Propel::getServiceContainer()->getWriteConnection(SSortingTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(SSortingTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            // cloning the Criteria in case it's modified by doSelect() or doSelectStmt()
            $c = clone $criteria;
            $affectedRows += $c->doOnDeleteCascade($con);

            SSortingTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            SSortingTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

    /**
     * This is a method for emulating ON DELETE CASCADE for DBs that don't support this
     * feature (like MySQL or SQLite).
     *
     * This method is not very speedy because it must perform a query first to get
     * the implicated records and then perform the deletes by calling those Query classes.
     *
     * This method should be used within a transaction if possible.
     *
     * @param ConnectionInterface $con
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    protected function doOnDeleteCascade(ConnectionInterface $con)
    {
        // initialize var to track total num of affected rows
        $affectedRows = 0;

        // first find the objects that are implicated by the $this
        $objects = ChildSSortingQuery::create(null, $this)->find($con);
        foreach ($objects as $obj) {


            // delete related SSortingI18n objects
            $query = new \SSortingI18nQuery;

            $query->add(SSortingI18nTableMap::COL_ID, $obj->getId());
            $affectedRows += $query->delete($con);
        }

        return $affectedRows;
    }

    // i18n behavior

    /**
     * Adds a JOIN clause to the query using the i18n relation
     *
     * @param     string $locale Locale to use for the join condition, e.g. 'fr_FR'
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'. Defaults to left join.
     *
     * @return    ChildSSortingQuery The current query, for fluid interface
     */
    public function joinI18n($locale = 'en_US', $relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $relationName = $relationAlias ? $relationAlias : 'SSortingI18n';

        return $this
            ->joinSSortingI18n($relationAlias, $joinType)
            ->addJoinCondition($relationName, $relationName . '.Locale = ?', $locale);
    }

    /**
     * Adds a JOIN clause to the query and hydrates the related I18n object.
     * Shortcut for $c->joinI18n($locale)->with()
     *
     * @param     string $locale Locale to use for the join condition, e.g. 'fr_FR'
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'. Defaults to left join.
     *
     * @return    $this|ChildSSortingQuery The current query, for fluid interface
     */
    public function joinWithI18n($locale = 'en_US', $joinType = Criteria::LEFT_JOIN)
    {
        $this
            ->joinI18n($locale, null, $joinType)
            ->with('SSortingI18n');
        $this->with['SSortingI18n']->setIsWithOneToMany(false);

        return $this;
    }

    /**
     * Use the I18n relation query object
     *
     * @see       useQuery()
     *
     * @param     string $locale Locale to use for the join condition, e.g. 'fr_FR'
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'. Defaults to left join.
     *
     * @return    ChildSSortingI18nQuery A secondary query class using the current class as primary query
     */
    public function useI18nQuery($locale = 'en_US', $relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinI18n($locale, $relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'SSortingI18n', '\SSortingI18nQuery');
    }

} // SSortingQuery
