<?php

namespace smart_filter\models\Base;

use \Exception;
use \PDO;
use \SCategory;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;
use smart_filter\models\SFilterPattern as ChildSFilterPattern;
use smart_filter\models\SFilterPatternI18nQuery as ChildSFilterPatternI18nQuery;
use smart_filter\models\SFilterPatternQuery as ChildSFilterPatternQuery;
use smart_filter\models\Map\SFilterPatternI18nTableMap;
use smart_filter\models\Map\SFilterPatternTableMap;

/**
 * Base class that represents a query for the 'smart_filter_patterns' table.
 *
 *
 *
 * @method     ChildSFilterPatternQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildSFilterPatternQuery orderByCategoryId($order = Criteria::ASC) Order by the category_id column
 * @method     ChildSFilterPatternQuery orderByActive($order = Criteria::ASC) Order by the active column
 * @method     ChildSFilterPatternQuery orderByUrlPattern($order = Criteria::ASC) Order by the url_pattern column
 * @method     ChildSFilterPatternQuery orderByData($order = Criteria::ASC) Order by the data column
 * @method     ChildSFilterPatternQuery orderByMetaIndex($order = Criteria::ASC) Order by the meta_index column
 * @method     ChildSFilterPatternQuery orderByMetaFollow($order = Criteria::ASC) Order by the meta_follow column
 * @method     ChildSFilterPatternQuery orderByCreated($order = Criteria::ASC) Order by the created column
 * @method     ChildSFilterPatternQuery orderByUpdated($order = Criteria::ASC) Order by the updated column
 *
 * @method     ChildSFilterPatternQuery groupById() Group by the id column
 * @method     ChildSFilterPatternQuery groupByCategoryId() Group by the category_id column
 * @method     ChildSFilterPatternQuery groupByActive() Group by the active column
 * @method     ChildSFilterPatternQuery groupByUrlPattern() Group by the url_pattern column
 * @method     ChildSFilterPatternQuery groupByData() Group by the data column
 * @method     ChildSFilterPatternQuery groupByMetaIndex() Group by the meta_index column
 * @method     ChildSFilterPatternQuery groupByMetaFollow() Group by the meta_follow column
 * @method     ChildSFilterPatternQuery groupByCreated() Group by the created column
 * @method     ChildSFilterPatternQuery groupByUpdated() Group by the updated column
 *
 * @method     ChildSFilterPatternQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildSFilterPatternQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildSFilterPatternQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildSFilterPatternQuery leftJoinWith($relation) Adds a LEFT JOIN clause and with to the query
 * @method     ChildSFilterPatternQuery rightJoinWith($relation) Adds a RIGHT JOIN clause and with to the query
 * @method     ChildSFilterPatternQuery innerJoinWith($relation) Adds a INNER JOIN clause and with to the query
 *
 * @method     ChildSFilterPatternQuery leftJoinCategory($relationAlias = null) Adds a LEFT JOIN clause to the query using the Category relation
 * @method     ChildSFilterPatternQuery rightJoinCategory($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Category relation
 * @method     ChildSFilterPatternQuery innerJoinCategory($relationAlias = null) Adds a INNER JOIN clause to the query using the Category relation
 *
 * @method     ChildSFilterPatternQuery joinWithCategory($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the Category relation
 *
 * @method     ChildSFilterPatternQuery leftJoinWithCategory() Adds a LEFT JOIN clause and with to the query using the Category relation
 * @method     ChildSFilterPatternQuery rightJoinWithCategory() Adds a RIGHT JOIN clause and with to the query using the Category relation
 * @method     ChildSFilterPatternQuery innerJoinWithCategory() Adds a INNER JOIN clause and with to the query using the Category relation
 *
 * @method     ChildSFilterPatternQuery leftJoinSFilterPatternI18n($relationAlias = null) Adds a LEFT JOIN clause to the query using the SFilterPatternI18n relation
 * @method     ChildSFilterPatternQuery rightJoinSFilterPatternI18n($relationAlias = null) Adds a RIGHT JOIN clause to the query using the SFilterPatternI18n relation
 * @method     ChildSFilterPatternQuery innerJoinSFilterPatternI18n($relationAlias = null) Adds a INNER JOIN clause to the query using the SFilterPatternI18n relation
 *
 * @method     ChildSFilterPatternQuery joinWithSFilterPatternI18n($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the SFilterPatternI18n relation
 *
 * @method     ChildSFilterPatternQuery leftJoinWithSFilterPatternI18n() Adds a LEFT JOIN clause and with to the query using the SFilterPatternI18n relation
 * @method     ChildSFilterPatternQuery rightJoinWithSFilterPatternI18n() Adds a RIGHT JOIN clause and with to the query using the SFilterPatternI18n relation
 * @method     ChildSFilterPatternQuery innerJoinWithSFilterPatternI18n() Adds a INNER JOIN clause and with to the query using the SFilterPatternI18n relation
 *
 * @method     \SCategoryQuery|\smart_filter\models\SFilterPatternI18nQuery endUse() Finalizes a secondary criteria and merges it with its primary Criteria
 *
 * @method     ChildSFilterPattern findOne(ConnectionInterface $con = null) Return the first ChildSFilterPattern matching the query
 * @method     ChildSFilterPattern findOneOrCreate(ConnectionInterface $con = null) Return the first ChildSFilterPattern matching the query, or a new ChildSFilterPattern object populated from the query conditions when no match is found
 *
 * @method     ChildSFilterPattern findOneById(int $id) Return the first ChildSFilterPattern filtered by the id column
 * @method     ChildSFilterPattern findOneByCategoryId(int $category_id) Return the first ChildSFilterPattern filtered by the category_id column
 * @method     ChildSFilterPattern findOneByActive(boolean $active) Return the first ChildSFilterPattern filtered by the active column
 * @method     ChildSFilterPattern findOneByUrlPattern(string $url_pattern) Return the first ChildSFilterPattern filtered by the url_pattern column
 * @method     ChildSFilterPattern findOneByData(string $data) Return the first ChildSFilterPattern filtered by the data column
 * @method     ChildSFilterPattern findOneByMetaIndex(int $meta_index) Return the first ChildSFilterPattern filtered by the meta_index column
 * @method     ChildSFilterPattern findOneByMetaFollow(int $meta_follow) Return the first ChildSFilterPattern filtered by the meta_follow column
 * @method     ChildSFilterPattern findOneByCreated(int $created) Return the first ChildSFilterPattern filtered by the created column
 * @method     ChildSFilterPattern findOneByUpdated(int $updated) Return the first ChildSFilterPattern filtered by the updated column *

 * @method     ChildSFilterPattern requirePk($key, ConnectionInterface $con = null) Return the ChildSFilterPattern by primary key and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSFilterPattern requireOne(ConnectionInterface $con = null) Return the first ChildSFilterPattern matching the query and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildSFilterPattern requireOneById(int $id) Return the first ChildSFilterPattern filtered by the id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSFilterPattern requireOneByCategoryId(int $category_id) Return the first ChildSFilterPattern filtered by the category_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSFilterPattern requireOneByActive(boolean $active) Return the first ChildSFilterPattern filtered by the active column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSFilterPattern requireOneByUrlPattern(string $url_pattern) Return the first ChildSFilterPattern filtered by the url_pattern column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSFilterPattern requireOneByData(string $data) Return the first ChildSFilterPattern filtered by the data column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSFilterPattern requireOneByMetaIndex(int $meta_index) Return the first ChildSFilterPattern filtered by the meta_index column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSFilterPattern requireOneByMetaFollow(int $meta_follow) Return the first ChildSFilterPattern filtered by the meta_follow column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSFilterPattern requireOneByCreated(int $created) Return the first ChildSFilterPattern filtered by the created column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSFilterPattern requireOneByUpdated(int $updated) Return the first ChildSFilterPattern filtered by the updated column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildSFilterPattern[]|ObjectCollection find(ConnectionInterface $con = null) Return ChildSFilterPattern objects based on current ModelCriteria
 * @method     ChildSFilterPattern[]|ObjectCollection findById(int $id) Return ChildSFilterPattern objects filtered by the id column
 * @method     ChildSFilterPattern[]|ObjectCollection findByCategoryId(int $category_id) Return ChildSFilterPattern objects filtered by the category_id column
 * @method     ChildSFilterPattern[]|ObjectCollection findByActive(boolean $active) Return ChildSFilterPattern objects filtered by the active column
 * @method     ChildSFilterPattern[]|ObjectCollection findByUrlPattern(string $url_pattern) Return ChildSFilterPattern objects filtered by the url_pattern column
 * @method     ChildSFilterPattern[]|ObjectCollection findByData(string $data) Return ChildSFilterPattern objects filtered by the data column
 * @method     ChildSFilterPattern[]|ObjectCollection findByMetaIndex(int $meta_index) Return ChildSFilterPattern objects filtered by the meta_index column
 * @method     ChildSFilterPattern[]|ObjectCollection findByMetaFollow(int $meta_follow) Return ChildSFilterPattern objects filtered by the meta_follow column
 * @method     ChildSFilterPattern[]|ObjectCollection findByCreated(int $created) Return ChildSFilterPattern objects filtered by the created column
 * @method     ChildSFilterPattern[]|ObjectCollection findByUpdated(int $updated) Return ChildSFilterPattern objects filtered by the updated column
 * @method     ChildSFilterPattern[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 *
 */
abstract class SFilterPatternQuery extends ModelCriteria
{
    protected $entityNotFoundExceptionClass = '\\Propel\\Runtime\\Exception\\EntityNotFoundException';

    /**
     * Initializes internal state of \smart_filter\models\Base\SFilterPatternQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'Shop', $modelName = '\\smart_filter\\models\\SFilterPattern', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildSFilterPatternQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildSFilterPatternQuery
     */
    public static function create($modelAlias = null, Criteria $criteria = null)
    {
        if ($criteria instanceof ChildSFilterPatternQuery) {
            return $criteria;
        }
        $query = new ChildSFilterPatternQuery();
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
     * @return ChildSFilterPattern|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(SFilterPatternTableMap::DATABASE_NAME);
        }

        $this->basePreSelect($con);

        if (
            $this->formatter || $this->modelAlias || $this->with || $this->select
            || $this->selectColumns || $this->asColumns || $this->selectModifiers
            || $this->map || $this->having || $this->joins
        ) {
            return $this->findPkComplex($key, $con);
        }

        if ((null !== ($obj = SFilterPatternTableMap::getInstanceFromPool(null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key)))) {
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
     * @return ChildSFilterPattern A model object, or null if the key is not found
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT id, category_id, active, url_pattern, data, meta_index, meta_follow, created, updated FROM smart_filter_patterns WHERE id = :p0';
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
            /** @var ChildSFilterPattern $obj */
            $obj = new ChildSFilterPattern();
            $obj->hydrate($row);
            SFilterPatternTableMap::addInstanceToPool($obj, null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key);
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
     * @return ChildSFilterPattern|array|mixed the result, formatted by the current formatter
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
     * @return $this|ChildSFilterPatternQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(SFilterPatternTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return $this|ChildSFilterPatternQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(SFilterPatternTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return $this|ChildSFilterPatternQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(SFilterPatternTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(SFilterPatternTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SFilterPatternTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the category_id column
     *
     * Example usage:
     * <code>
     * $query->filterByCategoryId(1234); // WHERE category_id = 1234
     * $query->filterByCategoryId(array(12, 34)); // WHERE category_id IN (12, 34)
     * $query->filterByCategoryId(array('min' => 12)); // WHERE category_id > 12
     * </code>
     *
     * @see       filterByCategory()
     *
     * @param     mixed $categoryId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildSFilterPatternQuery The current query, for fluid interface
     */
    public function filterByCategoryId($categoryId = null, $comparison = null)
    {
        if (is_array($categoryId)) {
            $useMinMax = false;
            if (isset($categoryId['min'])) {
                $this->addUsingAlias(SFilterPatternTableMap::COL_CATEGORY_ID, $categoryId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($categoryId['max'])) {
                $this->addUsingAlias(SFilterPatternTableMap::COL_CATEGORY_ID, $categoryId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SFilterPatternTableMap::COL_CATEGORY_ID, $categoryId, $comparison);
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
     * @return $this|ChildSFilterPatternQuery The current query, for fluid interface
     */
    public function filterByActive($active = null, $comparison = null)
    {
        if (is_string($active)) {
            $active = in_array(strtolower($active), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(SFilterPatternTableMap::COL_ACTIVE, $active, $comparison);
    }

    /**
     * Filter the query on the url_pattern column
     *
     * Example usage:
     * <code>
     * $query->filterByUrlPattern('fooValue');   // WHERE url_pattern = 'fooValue'
     * $query->filterByUrlPattern('%fooValue%', Criteria::LIKE); // WHERE url_pattern LIKE '%fooValue%'
     * </code>
     *
     * @param     string $urlPattern The value to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildSFilterPatternQuery The current query, for fluid interface
     */
    public function filterByUrlPattern($urlPattern = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($urlPattern)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SFilterPatternTableMap::COL_URL_PATTERN, $urlPattern, $comparison);
    }

    /**
     * Filter the query on the data column
     *
     * Example usage:
     * <code>
     * $query->filterByData('fooValue');   // WHERE data = 'fooValue'
     * $query->filterByData('%fooValue%', Criteria::LIKE); // WHERE data LIKE '%fooValue%'
     * </code>
     *
     * @param     string $data The value to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildSFilterPatternQuery The current query, for fluid interface
     */
    public function filterByData($data = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($data)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SFilterPatternTableMap::COL_DATA, $data, $comparison);
    }

    /**
     * Filter the query on the meta_index column
     *
     * @param     mixed $metaIndex The value to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildSFilterPatternQuery The current query, for fluid interface
     */
    public function filterByMetaIndex($metaIndex = null, $comparison = null)
    {
        $valueSet = SFilterPatternTableMap::getValueSet(SFilterPatternTableMap::COL_META_INDEX);
        if (is_scalar($metaIndex)) {
            if (!in_array($metaIndex, $valueSet)) {
                throw new PropelException(sprintf('Value "%s" is not accepted in this enumerated column', $metaIndex));
            }
            $metaIndex = array_search($metaIndex, $valueSet);
        } elseif (is_array($metaIndex)) {
            $convertedValues = array();
            foreach ($metaIndex as $value) {
                if (!in_array($value, $valueSet)) {
                    throw new PropelException(sprintf('Value "%s" is not accepted in this enumerated column', $value));
                }
                $convertedValues []= array_search($value, $valueSet);
            }
            $metaIndex = $convertedValues;
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SFilterPatternTableMap::COL_META_INDEX, $metaIndex, $comparison);
    }

    /**
     * Filter the query on the meta_follow column
     *
     * @param     mixed $metaFollow The value to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildSFilterPatternQuery The current query, for fluid interface
     */
    public function filterByMetaFollow($metaFollow = null, $comparison = null)
    {
        $valueSet = SFilterPatternTableMap::getValueSet(SFilterPatternTableMap::COL_META_FOLLOW);
        if (is_scalar($metaFollow)) {
            if (!in_array($metaFollow, $valueSet)) {
                throw new PropelException(sprintf('Value "%s" is not accepted in this enumerated column', $metaFollow));
            }
            $metaFollow = array_search($metaFollow, $valueSet);
        } elseif (is_array($metaFollow)) {
            $convertedValues = array();
            foreach ($metaFollow as $value) {
                if (!in_array($value, $valueSet)) {
                    throw new PropelException(sprintf('Value "%s" is not accepted in this enumerated column', $value));
                }
                $convertedValues []= array_search($value, $valueSet);
            }
            $metaFollow = $convertedValues;
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SFilterPatternTableMap::COL_META_FOLLOW, $metaFollow, $comparison);
    }

    /**
     * Filter the query on the created column
     *
     * Example usage:
     * <code>
     * $query->filterByCreated(1234); // WHERE created = 1234
     * $query->filterByCreated(array(12, 34)); // WHERE created IN (12, 34)
     * $query->filterByCreated(array('min' => 12)); // WHERE created > 12
     * </code>
     *
     * @param     mixed $created The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildSFilterPatternQuery The current query, for fluid interface
     */
    public function filterByCreated($created = null, $comparison = null)
    {
        if (is_array($created)) {
            $useMinMax = false;
            if (isset($created['min'])) {
                $this->addUsingAlias(SFilterPatternTableMap::COL_CREATED, $created['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($created['max'])) {
                $this->addUsingAlias(SFilterPatternTableMap::COL_CREATED, $created['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SFilterPatternTableMap::COL_CREATED, $created, $comparison);
    }

    /**
     * Filter the query on the updated column
     *
     * Example usage:
     * <code>
     * $query->filterByUpdated(1234); // WHERE updated = 1234
     * $query->filterByUpdated(array(12, 34)); // WHERE updated IN (12, 34)
     * $query->filterByUpdated(array('min' => 12)); // WHERE updated > 12
     * </code>
     *
     * @param     mixed $updated The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildSFilterPatternQuery The current query, for fluid interface
     */
    public function filterByUpdated($updated = null, $comparison = null)
    {
        if (is_array($updated)) {
            $useMinMax = false;
            if (isset($updated['min'])) {
                $this->addUsingAlias(SFilterPatternTableMap::COL_UPDATED, $updated['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updated['max'])) {
                $this->addUsingAlias(SFilterPatternTableMap::COL_UPDATED, $updated['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SFilterPatternTableMap::COL_UPDATED, $updated, $comparison);
    }

    /**
     * Filter the query by a related \SCategory object
     *
     * @param \SCategory|ObjectCollection $sCategory The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildSFilterPatternQuery The current query, for fluid interface
     */
    public function filterByCategory($sCategory, $comparison = null)
    {
        if ($sCategory instanceof \SCategory) {
            return $this
                ->addUsingAlias(SFilterPatternTableMap::COL_CATEGORY_ID, $sCategory->getId(), $comparison);
        } elseif ($sCategory instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(SFilterPatternTableMap::COL_CATEGORY_ID, $sCategory->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByCategory() only accepts arguments of type \SCategory or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Category relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildSFilterPatternQuery The current query, for fluid interface
     */
    public function joinCategory($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Category');

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
            $this->addJoinObject($join, 'Category');
        }

        return $this;
    }

    /**
     * Use the Category relation SCategory object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \SCategoryQuery A secondary query class using the current class as primary query
     */
    public function useCategoryQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCategory($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Category', '\SCategoryQuery');
    }

    /**
     * Filter the query by a related \smart_filter\models\SFilterPatternI18n object
     *
     * @param \smart_filter\models\SFilterPatternI18n|ObjectCollection $sFilterPatternI18n the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSFilterPatternQuery The current query, for fluid interface
     */
    public function filterBySFilterPatternI18n($sFilterPatternI18n, $comparison = null)
    {
        if ($sFilterPatternI18n instanceof \smart_filter\models\SFilterPatternI18n) {
            return $this
                ->addUsingAlias(SFilterPatternTableMap::COL_ID, $sFilterPatternI18n->getId(), $comparison);
        } elseif ($sFilterPatternI18n instanceof ObjectCollection) {
            return $this
                ->useSFilterPatternI18nQuery()
                ->filterByPrimaryKeys($sFilterPatternI18n->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterBySFilterPatternI18n() only accepts arguments of type \smart_filter\models\SFilterPatternI18n or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the SFilterPatternI18n relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildSFilterPatternQuery The current query, for fluid interface
     */
    public function joinSFilterPatternI18n($relationAlias = null, $joinType = 'LEFT JOIN')
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('SFilterPatternI18n');

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
            $this->addJoinObject($join, 'SFilterPatternI18n');
        }

        return $this;
    }

    /**
     * Use the SFilterPatternI18n relation SFilterPatternI18n object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \smart_filter\models\SFilterPatternI18nQuery A secondary query class using the current class as primary query
     */
    public function useSFilterPatternI18nQuery($relationAlias = null, $joinType = 'LEFT JOIN')
    {
        return $this
            ->joinSFilterPatternI18n($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'SFilterPatternI18n', '\smart_filter\models\SFilterPatternI18nQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildSFilterPattern $sFilterPattern Object to remove from the list of results
     *
     * @return $this|ChildSFilterPatternQuery The current query, for fluid interface
     */
    public function prune($sFilterPattern = null)
    {
        if ($sFilterPattern) {
            $this->addUsingAlias(SFilterPatternTableMap::COL_ID, $sFilterPattern->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the smart_filter_patterns table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(SFilterPatternTableMap::DATABASE_NAME);
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
            SFilterPatternTableMap::clearInstancePool();
            SFilterPatternTableMap::clearRelatedInstancePool();

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
            $con = Propel::getServiceContainer()->getWriteConnection(SFilterPatternTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(SFilterPatternTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            // cloning the Criteria in case it's modified by doSelect() or doSelectStmt()
            $c = clone $criteria;
            $affectedRows += $c->doOnDeleteCascade($con);

            SFilterPatternTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            SFilterPatternTableMap::clearRelatedInstancePool();

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
        $objects = ChildSFilterPatternQuery::create(null, $this)->find($con);
        foreach ($objects as $obj) {


            // delete related SFilterPatternI18n objects
            $query = new \smart_filter\models\SFilterPatternI18nQuery;

            $query->add(SFilterPatternI18nTableMap::COL_ID, $obj->getId());
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
     * @return    ChildSFilterPatternQuery The current query, for fluid interface
     */
    public function joinI18n($locale = 'ru', $relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $relationName = $relationAlias ? $relationAlias : 'SFilterPatternI18n';

        return $this
            ->joinSFilterPatternI18n($relationAlias, $joinType)
            ->addJoinCondition($relationName, $relationName . '.Locale = ?', $locale);
    }

    /**
     * Adds a JOIN clause to the query and hydrates the related I18n object.
     * Shortcut for $c->joinI18n($locale)->with()
     *
     * @param     string $locale Locale to use for the join condition, e.g. 'fr_FR'
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'. Defaults to left join.
     *
     * @return    $this|ChildSFilterPatternQuery The current query, for fluid interface
     */
    public function joinWithI18n($locale = 'ru', $joinType = Criteria::LEFT_JOIN)
    {
        $this
            ->joinI18n($locale, null, $joinType)
            ->with('SFilterPatternI18n');
        $this->with['SFilterPatternI18n']->setIsWithOneToMany(false);

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
     * @return    ChildSFilterPatternI18nQuery A secondary query class using the current class as primary query
     */
    public function useI18nQuery($locale = 'ru', $relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinI18n($locale, $relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'SFilterPatternI18n', '\smart_filter\models\SFilterPatternI18nQuery');
    }

    // timestampable behavior

    /**
     * Filter by the latest updated
     *
     * @param      int $nbDays Maximum age of the latest update in days
     *
     * @return     $this|ChildSFilterPatternQuery The current query, for fluid interface
     */
    public function recentlyUpdated($nbDays = 7)
    {
        return $this->addUsingAlias(SFilterPatternTableMap::COL_UPDATED, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by update date desc
     *
     * @return     $this|ChildSFilterPatternQuery The current query, for fluid interface
     */
    public function lastUpdatedFirst()
    {
        return $this->addDescendingOrderByColumn(SFilterPatternTableMap::COL_UPDATED);
    }

    /**
     * Order by update date asc
     *
     * @return     $this|ChildSFilterPatternQuery The current query, for fluid interface
     */
    public function firstUpdatedFirst()
    {
        return $this->addAscendingOrderByColumn(SFilterPatternTableMap::COL_UPDATED);
    }

    /**
     * Order by create date desc
     *
     * @return     $this|ChildSFilterPatternQuery The current query, for fluid interface
     */
    public function lastCreatedFirst()
    {
        return $this->addDescendingOrderByColumn(SFilterPatternTableMap::COL_CREATED);
    }

    /**
     * Filter by the latest created
     *
     * @param      int $nbDays Maximum age of in days
     *
     * @return     $this|ChildSFilterPatternQuery The current query, for fluid interface
     */
    public function recentlyCreated($nbDays = 7)
    {
        return $this->addUsingAlias(SFilterPatternTableMap::COL_CREATED, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by create date asc
     *
     * @return     $this|ChildSFilterPatternQuery The current query, for fluid interface
     */
    public function firstCreatedFirst()
    {
        return $this->addAscendingOrderByColumn(SFilterPatternTableMap::COL_CREATED);
    }

} // SFilterPatternQuery
