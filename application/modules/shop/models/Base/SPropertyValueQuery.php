<?php

namespace Base;

use \SPropertyValue as ChildSPropertyValue;
use \SPropertyValueI18nQuery as ChildSPropertyValueI18nQuery;
use \SPropertyValueQuery as ChildSPropertyValueQuery;
use \Exception;
use \PDO;
use Map\SProductPropertiesDataTableMap;
use Map\SPropertyValueI18nTableMap;
use Map\SPropertyValueTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'shop_product_property_value' table.
 *
 *
 *
 * @method     ChildSPropertyValueQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildSPropertyValueQuery orderByPropertyId($order = Criteria::ASC) Order by the property_id column
 * @method     ChildSPropertyValueQuery orderByPosition($order = Criteria::ASC) Order by the position column
 *
 * @method     ChildSPropertyValueQuery groupById() Group by the id column
 * @method     ChildSPropertyValueQuery groupByPropertyId() Group by the property_id column
 * @method     ChildSPropertyValueQuery groupByPosition() Group by the position column
 *
 * @method     ChildSPropertyValueQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildSPropertyValueQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildSPropertyValueQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildSPropertyValueQuery leftJoinWith($relation) Adds a LEFT JOIN clause and with to the query
 * @method     ChildSPropertyValueQuery rightJoinWith($relation) Adds a RIGHT JOIN clause and with to the query
 * @method     ChildSPropertyValueQuery innerJoinWith($relation) Adds a INNER JOIN clause and with to the query
 *
 * @method     ChildSPropertyValueQuery leftJoinSProperties($relationAlias = null) Adds a LEFT JOIN clause to the query using the SProperties relation
 * @method     ChildSPropertyValueQuery rightJoinSProperties($relationAlias = null) Adds a RIGHT JOIN clause to the query using the SProperties relation
 * @method     ChildSPropertyValueQuery innerJoinSProperties($relationAlias = null) Adds a INNER JOIN clause to the query using the SProperties relation
 *
 * @method     ChildSPropertyValueQuery joinWithSProperties($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the SProperties relation
 *
 * @method     ChildSPropertyValueQuery leftJoinWithSProperties() Adds a LEFT JOIN clause and with to the query using the SProperties relation
 * @method     ChildSPropertyValueQuery rightJoinWithSProperties() Adds a RIGHT JOIN clause and with to the query using the SProperties relation
 * @method     ChildSPropertyValueQuery innerJoinWithSProperties() Adds a INNER JOIN clause and with to the query using the SProperties relation
 *
 * @method     ChildSPropertyValueQuery leftJoinSPropertyValueI18n($relationAlias = null) Adds a LEFT JOIN clause to the query using the SPropertyValueI18n relation
 * @method     ChildSPropertyValueQuery rightJoinSPropertyValueI18n($relationAlias = null) Adds a RIGHT JOIN clause to the query using the SPropertyValueI18n relation
 * @method     ChildSPropertyValueQuery innerJoinSPropertyValueI18n($relationAlias = null) Adds a INNER JOIN clause to the query using the SPropertyValueI18n relation
 *
 * @method     ChildSPropertyValueQuery joinWithSPropertyValueI18n($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the SPropertyValueI18n relation
 *
 * @method     ChildSPropertyValueQuery leftJoinWithSPropertyValueI18n() Adds a LEFT JOIN clause and with to the query using the SPropertyValueI18n relation
 * @method     ChildSPropertyValueQuery rightJoinWithSPropertyValueI18n() Adds a RIGHT JOIN clause and with to the query using the SPropertyValueI18n relation
 * @method     ChildSPropertyValueQuery innerJoinWithSPropertyValueI18n() Adds a INNER JOIN clause and with to the query using the SPropertyValueI18n relation
 *
 * @method     ChildSPropertyValueQuery leftJoinSProductPropertiesData($relationAlias = null) Adds a LEFT JOIN clause to the query using the SProductPropertiesData relation
 * @method     ChildSPropertyValueQuery rightJoinSProductPropertiesData($relationAlias = null) Adds a RIGHT JOIN clause to the query using the SProductPropertiesData relation
 * @method     ChildSPropertyValueQuery innerJoinSProductPropertiesData($relationAlias = null) Adds a INNER JOIN clause to the query using the SProductPropertiesData relation
 *
 * @method     ChildSPropertyValueQuery joinWithSProductPropertiesData($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the SProductPropertiesData relation
 *
 * @method     ChildSPropertyValueQuery leftJoinWithSProductPropertiesData() Adds a LEFT JOIN clause and with to the query using the SProductPropertiesData relation
 * @method     ChildSPropertyValueQuery rightJoinWithSProductPropertiesData() Adds a RIGHT JOIN clause and with to the query using the SProductPropertiesData relation
 * @method     ChildSPropertyValueQuery innerJoinWithSProductPropertiesData() Adds a INNER JOIN clause and with to the query using the SProductPropertiesData relation
 *
 * @method     \SPropertiesQuery|\SPropertyValueI18nQuery|\SProductPropertiesDataQuery endUse() Finalizes a secondary criteria and merges it with its primary Criteria
 *
 * @method     ChildSPropertyValue findOne(ConnectionInterface $con = null) Return the first ChildSPropertyValue matching the query
 * @method     ChildSPropertyValue findOneOrCreate(ConnectionInterface $con = null) Return the first ChildSPropertyValue matching the query, or a new ChildSPropertyValue object populated from the query conditions when no match is found
 *
 * @method     ChildSPropertyValue findOneById(int $id) Return the first ChildSPropertyValue filtered by the id column
 * @method     ChildSPropertyValue findOneByPropertyId(int $property_id) Return the first ChildSPropertyValue filtered by the property_id column
 * @method     ChildSPropertyValue findOneByPosition(int $position) Return the first ChildSPropertyValue filtered by the position column *

 * @method     ChildSPropertyValue requirePk($key, ConnectionInterface $con = null) Return the ChildSPropertyValue by primary key and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSPropertyValue requireOne(ConnectionInterface $con = null) Return the first ChildSPropertyValue matching the query and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildSPropertyValue requireOneById(int $id) Return the first ChildSPropertyValue filtered by the id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSPropertyValue requireOneByPropertyId(int $property_id) Return the first ChildSPropertyValue filtered by the property_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSPropertyValue requireOneByPosition(int $position) Return the first ChildSPropertyValue filtered by the position column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildSPropertyValue[]|ObjectCollection find(ConnectionInterface $con = null) Return ChildSPropertyValue objects based on current ModelCriteria
 * @method     ChildSPropertyValue[]|ObjectCollection findById(int $id) Return ChildSPropertyValue objects filtered by the id column
 * @method     ChildSPropertyValue[]|ObjectCollection findByPropertyId(int $property_id) Return ChildSPropertyValue objects filtered by the property_id column
 * @method     ChildSPropertyValue[]|ObjectCollection findByPosition(int $position) Return ChildSPropertyValue objects filtered by the position column
 * @method     ChildSPropertyValue[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 *
 */
abstract class SPropertyValueQuery extends ModelCriteria
{
    protected $entityNotFoundExceptionClass = '\\Propel\\Runtime\\Exception\\EntityNotFoundException';

    /**
     * Initializes internal state of \Base\SPropertyValueQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'Shop', $modelName = '\\SPropertyValue', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildSPropertyValueQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildSPropertyValueQuery
     */
    public static function create($modelAlias = null, Criteria $criteria = null)
    {
        if ($criteria instanceof ChildSPropertyValueQuery) {
            return $criteria;
        }
        $query = new ChildSPropertyValueQuery();
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
     * @return ChildSPropertyValue|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(SPropertyValueTableMap::DATABASE_NAME);
        }

        $this->basePreSelect($con);

        if (
            $this->formatter || $this->modelAlias || $this->with || $this->select
            || $this->selectColumns || $this->asColumns || $this->selectModifiers
            || $this->map || $this->having || $this->joins
        ) {
            return $this->findPkComplex($key, $con);
        }

        if ((null !== ($obj = SPropertyValueTableMap::getInstanceFromPool(null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key)))) {
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
     * @return ChildSPropertyValue A model object, or null if the key is not found
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT id, property_id, position FROM shop_product_property_value WHERE id = :p0';
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
            /** @var ChildSPropertyValue $obj */
            $obj = new ChildSPropertyValue();
            $obj->hydrate($row);
            SPropertyValueTableMap::addInstanceToPool($obj, null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key);
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
     * @return ChildSPropertyValue|array|mixed the result, formatted by the current formatter
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
     * @return $this|ChildSPropertyValueQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(SPropertyValueTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return $this|ChildSPropertyValueQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(SPropertyValueTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return $this|ChildSPropertyValueQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(SPropertyValueTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(SPropertyValueTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SPropertyValueTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the property_id column
     *
     * Example usage:
     * <code>
     * $query->filterByPropertyId(1234); // WHERE property_id = 1234
     * $query->filterByPropertyId(array(12, 34)); // WHERE property_id IN (12, 34)
     * $query->filterByPropertyId(array('min' => 12)); // WHERE property_id > 12
     * </code>
     *
     * @see       filterBySProperties()
     *
     * @param     mixed $propertyId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildSPropertyValueQuery The current query, for fluid interface
     */
    public function filterByPropertyId($propertyId = null, $comparison = null)
    {
        if (is_array($propertyId)) {
            $useMinMax = false;
            if (isset($propertyId['min'])) {
                $this->addUsingAlias(SPropertyValueTableMap::COL_PROPERTY_ID, $propertyId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($propertyId['max'])) {
                $this->addUsingAlias(SPropertyValueTableMap::COL_PROPERTY_ID, $propertyId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SPropertyValueTableMap::COL_PROPERTY_ID, $propertyId, $comparison);
    }

    /**
     * Filter the query on the position column
     *
     * Example usage:
     * <code>
     * $query->filterByPosition(1234); // WHERE position = 1234
     * $query->filterByPosition(array(12, 34)); // WHERE position IN (12, 34)
     * $query->filterByPosition(array('min' => 12)); // WHERE position > 12
     * </code>
     *
     * @param     mixed $position The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildSPropertyValueQuery The current query, for fluid interface
     */
    public function filterByPosition($position = null, $comparison = null)
    {
        if (is_array($position)) {
            $useMinMax = false;
            if (isset($position['min'])) {
                $this->addUsingAlias(SPropertyValueTableMap::COL_POSITION, $position['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($position['max'])) {
                $this->addUsingAlias(SPropertyValueTableMap::COL_POSITION, $position['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SPropertyValueTableMap::COL_POSITION, $position, $comparison);
    }

    /**
     * Filter the query by a related \SProperties object
     *
     * @param \SProperties|ObjectCollection $sProperties The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildSPropertyValueQuery The current query, for fluid interface
     */
    public function filterBySProperties($sProperties, $comparison = null)
    {
        if ($sProperties instanceof \SProperties) {
            return $this
                ->addUsingAlias(SPropertyValueTableMap::COL_PROPERTY_ID, $sProperties->getId(), $comparison);
        } elseif ($sProperties instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(SPropertyValueTableMap::COL_PROPERTY_ID, $sProperties->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterBySProperties() only accepts arguments of type \SProperties or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the SProperties relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildSPropertyValueQuery The current query, for fluid interface
     */
    public function joinSProperties($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('SProperties');

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
            $this->addJoinObject($join, 'SProperties');
        }

        return $this;
    }

    /**
     * Use the SProperties relation SProperties object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \SPropertiesQuery A secondary query class using the current class as primary query
     */
    public function useSPropertiesQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinSProperties($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'SProperties', '\SPropertiesQuery');
    }

    /**
     * Filter the query by a related \SPropertyValueI18n object
     *
     * @param \SPropertyValueI18n|ObjectCollection $sPropertyValueI18n the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSPropertyValueQuery The current query, for fluid interface
     */
    public function filterBySPropertyValueI18n($sPropertyValueI18n, $comparison = null)
    {
        if ($sPropertyValueI18n instanceof \SPropertyValueI18n) {
            return $this
                ->addUsingAlias(SPropertyValueTableMap::COL_ID, $sPropertyValueI18n->getId(), $comparison);
        } elseif ($sPropertyValueI18n instanceof ObjectCollection) {
            return $this
                ->useSPropertyValueI18nQuery()
                ->filterByPrimaryKeys($sPropertyValueI18n->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterBySPropertyValueI18n() only accepts arguments of type \SPropertyValueI18n or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the SPropertyValueI18n relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildSPropertyValueQuery The current query, for fluid interface
     */
    public function joinSPropertyValueI18n($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('SPropertyValueI18n');

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
            $this->addJoinObject($join, 'SPropertyValueI18n');
        }

        return $this;
    }

    /**
     * Use the SPropertyValueI18n relation SPropertyValueI18n object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \SPropertyValueI18nQuery A secondary query class using the current class as primary query
     */
    public function useSPropertyValueI18nQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinSPropertyValueI18n($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'SPropertyValueI18n', '\SPropertyValueI18nQuery');
    }

    /**
     * Filter the query by a related \SProductPropertiesData object
     *
     * @param \SProductPropertiesData|ObjectCollection $sProductPropertiesData the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSPropertyValueQuery The current query, for fluid interface
     */
    public function filterBySProductPropertiesData($sProductPropertiesData, $comparison = null)
    {
        if ($sProductPropertiesData instanceof \SProductPropertiesData) {
            return $this
                ->addUsingAlias(SPropertyValueTableMap::COL_ID, $sProductPropertiesData->getValueId(), $comparison);
        } elseif ($sProductPropertiesData instanceof ObjectCollection) {
            return $this
                ->useSProductPropertiesDataQuery()
                ->filterByPrimaryKeys($sProductPropertiesData->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterBySProductPropertiesData() only accepts arguments of type \SProductPropertiesData or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the SProductPropertiesData relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildSPropertyValueQuery The current query, for fluid interface
     */
    public function joinSProductPropertiesData($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('SProductPropertiesData');

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
            $this->addJoinObject($join, 'SProductPropertiesData');
        }

        return $this;
    }

    /**
     * Use the SProductPropertiesData relation SProductPropertiesData object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \SProductPropertiesDataQuery A secondary query class using the current class as primary query
     */
    public function useSProductPropertiesDataQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinSProductPropertiesData($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'SProductPropertiesData', '\SProductPropertiesDataQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildSPropertyValue $sPropertyValue Object to remove from the list of results
     *
     * @return $this|ChildSPropertyValueQuery The current query, for fluid interface
     */
    public function prune($sPropertyValue = null)
    {
        if ($sPropertyValue) {
            $this->addUsingAlias(SPropertyValueTableMap::COL_ID, $sPropertyValue->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the shop_product_property_value table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(SPropertyValueTableMap::DATABASE_NAME);
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
            SPropertyValueTableMap::clearInstancePool();
            SPropertyValueTableMap::clearRelatedInstancePool();

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
            $con = Propel::getServiceContainer()->getWriteConnection(SPropertyValueTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(SPropertyValueTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            // cloning the Criteria in case it's modified by doSelect() or doSelectStmt()
            $c = clone $criteria;
            $affectedRows += $c->doOnDeleteCascade($con);

            SPropertyValueTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            SPropertyValueTableMap::clearRelatedInstancePool();

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
        $objects = ChildSPropertyValueQuery::create(null, $this)->find($con);
        foreach ($objects as $obj) {


            // delete related SPropertyValueI18n objects
            $query = new \SPropertyValueI18nQuery;

            $query->add(SPropertyValueI18nTableMap::COL_ID, $obj->getId());
            $affectedRows += $query->delete($con);

            // delete related SProductPropertiesData objects
            $query = new \SProductPropertiesDataQuery;

            $query->add(SProductPropertiesDataTableMap::COL_VALUE_ID, $obj->getId());
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
     * @return    ChildSPropertyValueQuery The current query, for fluid interface
     */
    public function joinI18n($locale = 'ru', $relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $relationName = $relationAlias ? $relationAlias : 'SPropertyValueI18n';

        return $this
            ->joinSPropertyValueI18n($relationAlias, $joinType)
            ->addJoinCondition($relationName, $relationName . '.Locale = ?', $locale);
    }

    /**
     * Adds a JOIN clause to the query and hydrates the related I18n object.
     * Shortcut for $c->joinI18n($locale)->with()
     *
     * @param     string $locale Locale to use for the join condition, e.g. 'fr_FR'
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'. Defaults to left join.
     *
     * @return    $this|ChildSPropertyValueQuery The current query, for fluid interface
     */
    public function joinWithI18n($locale = 'ru', $joinType = Criteria::LEFT_JOIN)
    {
        $this
            ->joinI18n($locale, null, $joinType)
            ->with('SPropertyValueI18n');
        $this->with['SPropertyValueI18n']->setIsWithOneToMany(false);

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
     * @return    ChildSPropertyValueI18nQuery A secondary query class using the current class as primary query
     */
    public function useI18nQuery($locale = 'ru', $relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinI18n($locale, $relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'SPropertyValueI18n', '\SPropertyValueI18nQuery');
    }

} // SPropertyValueQuery
