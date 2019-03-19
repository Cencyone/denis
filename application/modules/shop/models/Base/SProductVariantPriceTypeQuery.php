<?php

namespace Base;

use \SProductVariantPriceType as ChildSProductVariantPriceType;
use \SProductVariantPriceTypeQuery as ChildSProductVariantPriceTypeQuery;
use \Exception;
use \PDO;
use Map\SProductVariantPriceTableMap;
use Map\SProductVariantPriceTypeTableMap;
use Map\SProductVariantPriceTypeValueTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'shop_product_variants_price_types' table.
 *
 *
 *
 * @method     ChildSProductVariantPriceTypeQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildSProductVariantPriceTypeQuery orderByNameType($order = Criteria::ASC) Order by the name_type column
 * @method     ChildSProductVariantPriceTypeQuery orderByCurrencyId($order = Criteria::ASC) Order by the currency_id column
 * @method     ChildSProductVariantPriceTypeQuery orderByStatus($order = Criteria::ASC) Order by the status column
 * @method     ChildSProductVariantPriceTypeQuery orderByPosition($order = Criteria::ASC) Order by the position column
 * @method     ChildSProductVariantPriceTypeQuery orderByPriceType($order = Criteria::ASC) Order by the price_type column
 * @method     ChildSProductVariantPriceTypeQuery orderByConsiderDiscount($order = Criteria::ASC) Order by the consider_discount column
 *
 * @method     ChildSProductVariantPriceTypeQuery groupById() Group by the id column
 * @method     ChildSProductVariantPriceTypeQuery groupByNameType() Group by the name_type column
 * @method     ChildSProductVariantPriceTypeQuery groupByCurrencyId() Group by the currency_id column
 * @method     ChildSProductVariantPriceTypeQuery groupByStatus() Group by the status column
 * @method     ChildSProductVariantPriceTypeQuery groupByPosition() Group by the position column
 * @method     ChildSProductVariantPriceTypeQuery groupByPriceType() Group by the price_type column
 * @method     ChildSProductVariantPriceTypeQuery groupByConsiderDiscount() Group by the consider_discount column
 *
 * @method     ChildSProductVariantPriceTypeQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildSProductVariantPriceTypeQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildSProductVariantPriceTypeQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildSProductVariantPriceTypeQuery leftJoinWith($relation) Adds a LEFT JOIN clause and with to the query
 * @method     ChildSProductVariantPriceTypeQuery rightJoinWith($relation) Adds a RIGHT JOIN clause and with to the query
 * @method     ChildSProductVariantPriceTypeQuery innerJoinWith($relation) Adds a INNER JOIN clause and with to the query
 *
 * @method     ChildSProductVariantPriceTypeQuery leftJoinCurrency($relationAlias = null) Adds a LEFT JOIN clause to the query using the Currency relation
 * @method     ChildSProductVariantPriceTypeQuery rightJoinCurrency($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Currency relation
 * @method     ChildSProductVariantPriceTypeQuery innerJoinCurrency($relationAlias = null) Adds a INNER JOIN clause to the query using the Currency relation
 *
 * @method     ChildSProductVariantPriceTypeQuery joinWithCurrency($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the Currency relation
 *
 * @method     ChildSProductVariantPriceTypeQuery leftJoinWithCurrency() Adds a LEFT JOIN clause and with to the query using the Currency relation
 * @method     ChildSProductVariantPriceTypeQuery rightJoinWithCurrency() Adds a RIGHT JOIN clause and with to the query using the Currency relation
 * @method     ChildSProductVariantPriceTypeQuery innerJoinWithCurrency() Adds a INNER JOIN clause and with to the query using the Currency relation
 *
 * @method     ChildSProductVariantPriceTypeQuery leftJoinSProductVariantPriceTypeValue($relationAlias = null) Adds a LEFT JOIN clause to the query using the SProductVariantPriceTypeValue relation
 * @method     ChildSProductVariantPriceTypeQuery rightJoinSProductVariantPriceTypeValue($relationAlias = null) Adds a RIGHT JOIN clause to the query using the SProductVariantPriceTypeValue relation
 * @method     ChildSProductVariantPriceTypeQuery innerJoinSProductVariantPriceTypeValue($relationAlias = null) Adds a INNER JOIN clause to the query using the SProductVariantPriceTypeValue relation
 *
 * @method     ChildSProductVariantPriceTypeQuery joinWithSProductVariantPriceTypeValue($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the SProductVariantPriceTypeValue relation
 *
 * @method     ChildSProductVariantPriceTypeQuery leftJoinWithSProductVariantPriceTypeValue() Adds a LEFT JOIN clause and with to the query using the SProductVariantPriceTypeValue relation
 * @method     ChildSProductVariantPriceTypeQuery rightJoinWithSProductVariantPriceTypeValue() Adds a RIGHT JOIN clause and with to the query using the SProductVariantPriceTypeValue relation
 * @method     ChildSProductVariantPriceTypeQuery innerJoinWithSProductVariantPriceTypeValue() Adds a INNER JOIN clause and with to the query using the SProductVariantPriceTypeValue relation
 *
 * @method     ChildSProductVariantPriceTypeQuery leftJoinSProductVariantPrice($relationAlias = null) Adds a LEFT JOIN clause to the query using the SProductVariantPrice relation
 * @method     ChildSProductVariantPriceTypeQuery rightJoinSProductVariantPrice($relationAlias = null) Adds a RIGHT JOIN clause to the query using the SProductVariantPrice relation
 * @method     ChildSProductVariantPriceTypeQuery innerJoinSProductVariantPrice($relationAlias = null) Adds a INNER JOIN clause to the query using the SProductVariantPrice relation
 *
 * @method     ChildSProductVariantPriceTypeQuery joinWithSProductVariantPrice($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the SProductVariantPrice relation
 *
 * @method     ChildSProductVariantPriceTypeQuery leftJoinWithSProductVariantPrice() Adds a LEFT JOIN clause and with to the query using the SProductVariantPrice relation
 * @method     ChildSProductVariantPriceTypeQuery rightJoinWithSProductVariantPrice() Adds a RIGHT JOIN clause and with to the query using the SProductVariantPrice relation
 * @method     ChildSProductVariantPriceTypeQuery innerJoinWithSProductVariantPrice() Adds a INNER JOIN clause and with to the query using the SProductVariantPrice relation
 *
 * @method     \SCurrenciesQuery|\SProductVariantPriceTypeValueQuery|\SProductVariantPriceQuery endUse() Finalizes a secondary criteria and merges it with its primary Criteria
 *
 * @method     ChildSProductVariantPriceType findOne(ConnectionInterface $con = null) Return the first ChildSProductVariantPriceType matching the query
 * @method     ChildSProductVariantPriceType findOneOrCreate(ConnectionInterface $con = null) Return the first ChildSProductVariantPriceType matching the query, or a new ChildSProductVariantPriceType object populated from the query conditions when no match is found
 *
 * @method     ChildSProductVariantPriceType findOneById(int $id) Return the first ChildSProductVariantPriceType filtered by the id column
 * @method     ChildSProductVariantPriceType findOneByNameType(string $name_type) Return the first ChildSProductVariantPriceType filtered by the name_type column
 * @method     ChildSProductVariantPriceType findOneByCurrencyId(int $currency_id) Return the first ChildSProductVariantPriceType filtered by the currency_id column
 * @method     ChildSProductVariantPriceType findOneByStatus(int $status) Return the first ChildSProductVariantPriceType filtered by the status column
 * @method     ChildSProductVariantPriceType findOneByPosition(int $position) Return the first ChildSProductVariantPriceType filtered by the position column
 * @method     ChildSProductVariantPriceType findOneByPriceType(int $price_type) Return the first ChildSProductVariantPriceType filtered by the price_type column
 * @method     ChildSProductVariantPriceType findOneByConsiderDiscount(boolean $consider_discount) Return the first ChildSProductVariantPriceType filtered by the consider_discount column *

 * @method     ChildSProductVariantPriceType requirePk($key, ConnectionInterface $con = null) Return the ChildSProductVariantPriceType by primary key and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSProductVariantPriceType requireOne(ConnectionInterface $con = null) Return the first ChildSProductVariantPriceType matching the query and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildSProductVariantPriceType requireOneById(int $id) Return the first ChildSProductVariantPriceType filtered by the id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSProductVariantPriceType requireOneByNameType(string $name_type) Return the first ChildSProductVariantPriceType filtered by the name_type column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSProductVariantPriceType requireOneByCurrencyId(int $currency_id) Return the first ChildSProductVariantPriceType filtered by the currency_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSProductVariantPriceType requireOneByStatus(int $status) Return the first ChildSProductVariantPriceType filtered by the status column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSProductVariantPriceType requireOneByPosition(int $position) Return the first ChildSProductVariantPriceType filtered by the position column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSProductVariantPriceType requireOneByPriceType(int $price_type) Return the first ChildSProductVariantPriceType filtered by the price_type column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSProductVariantPriceType requireOneByConsiderDiscount(boolean $consider_discount) Return the first ChildSProductVariantPriceType filtered by the consider_discount column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildSProductVariantPriceType[]|ObjectCollection find(ConnectionInterface $con = null) Return ChildSProductVariantPriceType objects based on current ModelCriteria
 * @method     ChildSProductVariantPriceType[]|ObjectCollection findById(int $id) Return ChildSProductVariantPriceType objects filtered by the id column
 * @method     ChildSProductVariantPriceType[]|ObjectCollection findByNameType(string $name_type) Return ChildSProductVariantPriceType objects filtered by the name_type column
 * @method     ChildSProductVariantPriceType[]|ObjectCollection findByCurrencyId(int $currency_id) Return ChildSProductVariantPriceType objects filtered by the currency_id column
 * @method     ChildSProductVariantPriceType[]|ObjectCollection findByStatus(int $status) Return ChildSProductVariantPriceType objects filtered by the status column
 * @method     ChildSProductVariantPriceType[]|ObjectCollection findByPosition(int $position) Return ChildSProductVariantPriceType objects filtered by the position column
 * @method     ChildSProductVariantPriceType[]|ObjectCollection findByPriceType(int $price_type) Return ChildSProductVariantPriceType objects filtered by the price_type column
 * @method     ChildSProductVariantPriceType[]|ObjectCollection findByConsiderDiscount(boolean $consider_discount) Return ChildSProductVariantPriceType objects filtered by the consider_discount column
 * @method     ChildSProductVariantPriceType[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 *
 */
abstract class SProductVariantPriceTypeQuery extends ModelCriteria
{
    protected $entityNotFoundExceptionClass = '\\Propel\\Runtime\\Exception\\EntityNotFoundException';

    /**
     * Initializes internal state of \Base\SProductVariantPriceTypeQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'Shop', $modelName = '\\SProductVariantPriceType', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildSProductVariantPriceTypeQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildSProductVariantPriceTypeQuery
     */
    public static function create($modelAlias = null, Criteria $criteria = null)
    {
        if ($criteria instanceof ChildSProductVariantPriceTypeQuery) {
            return $criteria;
        }
        $query = new ChildSProductVariantPriceTypeQuery();
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
     * @return ChildSProductVariantPriceType|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(SProductVariantPriceTypeTableMap::DATABASE_NAME);
        }

        $this->basePreSelect($con);

        if (
            $this->formatter || $this->modelAlias || $this->with || $this->select
            || $this->selectColumns || $this->asColumns || $this->selectModifiers
            || $this->map || $this->having || $this->joins
        ) {
            return $this->findPkComplex($key, $con);
        }

        if ((null !== ($obj = SProductVariantPriceTypeTableMap::getInstanceFromPool(null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key)))) {
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
     * @return ChildSProductVariantPriceType A model object, or null if the key is not found
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT id, name_type, currency_id, status, position, price_type, consider_discount FROM shop_product_variants_price_types WHERE id = :p0';
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
            /** @var ChildSProductVariantPriceType $obj */
            $obj = new ChildSProductVariantPriceType();
            $obj->hydrate($row);
            SProductVariantPriceTypeTableMap::addInstanceToPool($obj, null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key);
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
     * @return ChildSProductVariantPriceType|array|mixed the result, formatted by the current formatter
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
     * @return $this|ChildSProductVariantPriceTypeQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(SProductVariantPriceTypeTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return $this|ChildSProductVariantPriceTypeQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(SProductVariantPriceTypeTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return $this|ChildSProductVariantPriceTypeQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(SProductVariantPriceTypeTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(SProductVariantPriceTypeTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SProductVariantPriceTypeTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the name_type column
     *
     * Example usage:
     * <code>
     * $query->filterByNameType('fooValue');   // WHERE name_type = 'fooValue'
     * $query->filterByNameType('%fooValue%', Criteria::LIKE); // WHERE name_type LIKE '%fooValue%'
     * </code>
     *
     * @param     string $nameType The value to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildSProductVariantPriceTypeQuery The current query, for fluid interface
     */
    public function filterByNameType($nameType = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($nameType)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SProductVariantPriceTypeTableMap::COL_NAME_TYPE, $nameType, $comparison);
    }

    /**
     * Filter the query on the currency_id column
     *
     * Example usage:
     * <code>
     * $query->filterByCurrencyId(1234); // WHERE currency_id = 1234
     * $query->filterByCurrencyId(array(12, 34)); // WHERE currency_id IN (12, 34)
     * $query->filterByCurrencyId(array('min' => 12)); // WHERE currency_id > 12
     * </code>
     *
     * @see       filterByCurrency()
     *
     * @param     mixed $currencyId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildSProductVariantPriceTypeQuery The current query, for fluid interface
     */
    public function filterByCurrencyId($currencyId = null, $comparison = null)
    {
        if (is_array($currencyId)) {
            $useMinMax = false;
            if (isset($currencyId['min'])) {
                $this->addUsingAlias(SProductVariantPriceTypeTableMap::COL_CURRENCY_ID, $currencyId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($currencyId['max'])) {
                $this->addUsingAlias(SProductVariantPriceTypeTableMap::COL_CURRENCY_ID, $currencyId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SProductVariantPriceTypeTableMap::COL_CURRENCY_ID, $currencyId, $comparison);
    }

    /**
     * Filter the query on the status column
     *
     * Example usage:
     * <code>
     * $query->filterByStatus(1234); // WHERE status = 1234
     * $query->filterByStatus(array(12, 34)); // WHERE status IN (12, 34)
     * $query->filterByStatus(array('min' => 12)); // WHERE status > 12
     * </code>
     *
     * @param     mixed $status The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildSProductVariantPriceTypeQuery The current query, for fluid interface
     */
    public function filterByStatus($status = null, $comparison = null)
    {
        if (is_array($status)) {
            $useMinMax = false;
            if (isset($status['min'])) {
                $this->addUsingAlias(SProductVariantPriceTypeTableMap::COL_STATUS, $status['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($status['max'])) {
                $this->addUsingAlias(SProductVariantPriceTypeTableMap::COL_STATUS, $status['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SProductVariantPriceTypeTableMap::COL_STATUS, $status, $comparison);
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
     * @return $this|ChildSProductVariantPriceTypeQuery The current query, for fluid interface
     */
    public function filterByPosition($position = null, $comparison = null)
    {
        if (is_array($position)) {
            $useMinMax = false;
            if (isset($position['min'])) {
                $this->addUsingAlias(SProductVariantPriceTypeTableMap::COL_POSITION, $position['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($position['max'])) {
                $this->addUsingAlias(SProductVariantPriceTypeTableMap::COL_POSITION, $position['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SProductVariantPriceTypeTableMap::COL_POSITION, $position, $comparison);
    }

    /**
     * Filter the query on the price_type column
     *
     * Example usage:
     * <code>
     * $query->filterByPriceType(1234); // WHERE price_type = 1234
     * $query->filterByPriceType(array(12, 34)); // WHERE price_type IN (12, 34)
     * $query->filterByPriceType(array('min' => 12)); // WHERE price_type > 12
     * </code>
     *
     * @param     mixed $priceType The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildSProductVariantPriceTypeQuery The current query, for fluid interface
     */
    public function filterByPriceType($priceType = null, $comparison = null)
    {
        if (is_array($priceType)) {
            $useMinMax = false;
            if (isset($priceType['min'])) {
                $this->addUsingAlias(SProductVariantPriceTypeTableMap::COL_PRICE_TYPE, $priceType['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($priceType['max'])) {
                $this->addUsingAlias(SProductVariantPriceTypeTableMap::COL_PRICE_TYPE, $priceType['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SProductVariantPriceTypeTableMap::COL_PRICE_TYPE, $priceType, $comparison);
    }

    /**
     * Filter the query on the consider_discount column
     *
     * Example usage:
     * <code>
     * $query->filterByConsiderDiscount(true); // WHERE consider_discount = true
     * $query->filterByConsiderDiscount('yes'); // WHERE consider_discount = true
     * </code>
     *
     * @param     boolean|string $considerDiscount The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildSProductVariantPriceTypeQuery The current query, for fluid interface
     */
    public function filterByConsiderDiscount($considerDiscount = null, $comparison = null)
    {
        if (is_string($considerDiscount)) {
            $considerDiscount = in_array(strtolower($considerDiscount), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(SProductVariantPriceTypeTableMap::COL_CONSIDER_DISCOUNT, $considerDiscount, $comparison);
    }

    /**
     * Filter the query by a related \SCurrencies object
     *
     * @param \SCurrencies|ObjectCollection $sCurrencies The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildSProductVariantPriceTypeQuery The current query, for fluid interface
     */
    public function filterByCurrency($sCurrencies, $comparison = null)
    {
        if ($sCurrencies instanceof \SCurrencies) {
            return $this
                ->addUsingAlias(SProductVariantPriceTypeTableMap::COL_CURRENCY_ID, $sCurrencies->getId(), $comparison);
        } elseif ($sCurrencies instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(SProductVariantPriceTypeTableMap::COL_CURRENCY_ID, $sCurrencies->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByCurrency() only accepts arguments of type \SCurrencies or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Currency relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildSProductVariantPriceTypeQuery The current query, for fluid interface
     */
    public function joinCurrency($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Currency');

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
            $this->addJoinObject($join, 'Currency');
        }

        return $this;
    }

    /**
     * Use the Currency relation SCurrencies object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \SCurrenciesQuery A secondary query class using the current class as primary query
     */
    public function useCurrencyQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinCurrency($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Currency', '\SCurrenciesQuery');
    }

    /**
     * Filter the query by a related \SProductVariantPriceTypeValue object
     *
     * @param \SProductVariantPriceTypeValue|ObjectCollection $sProductVariantPriceTypeValue the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSProductVariantPriceTypeQuery The current query, for fluid interface
     */
    public function filterBySProductVariantPriceTypeValue($sProductVariantPriceTypeValue, $comparison = null)
    {
        if ($sProductVariantPriceTypeValue instanceof \SProductVariantPriceTypeValue) {
            return $this
                ->addUsingAlias(SProductVariantPriceTypeTableMap::COL_ID, $sProductVariantPriceTypeValue->getPriceTypeId(), $comparison);
        } elseif ($sProductVariantPriceTypeValue instanceof ObjectCollection) {
            return $this
                ->useSProductVariantPriceTypeValueQuery()
                ->filterByPrimaryKeys($sProductVariantPriceTypeValue->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterBySProductVariantPriceTypeValue() only accepts arguments of type \SProductVariantPriceTypeValue or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the SProductVariantPriceTypeValue relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildSProductVariantPriceTypeQuery The current query, for fluid interface
     */
    public function joinSProductVariantPriceTypeValue($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('SProductVariantPriceTypeValue');

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
            $this->addJoinObject($join, 'SProductVariantPriceTypeValue');
        }

        return $this;
    }

    /**
     * Use the SProductVariantPriceTypeValue relation SProductVariantPriceTypeValue object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \SProductVariantPriceTypeValueQuery A secondary query class using the current class as primary query
     */
    public function useSProductVariantPriceTypeValueQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinSProductVariantPriceTypeValue($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'SProductVariantPriceTypeValue', '\SProductVariantPriceTypeValueQuery');
    }

    /**
     * Filter the query by a related \SProductVariantPrice object
     *
     * @param \SProductVariantPrice|ObjectCollection $sProductVariantPrice the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSProductVariantPriceTypeQuery The current query, for fluid interface
     */
    public function filterBySProductVariantPrice($sProductVariantPrice, $comparison = null)
    {
        if ($sProductVariantPrice instanceof \SProductVariantPrice) {
            return $this
                ->addUsingAlias(SProductVariantPriceTypeTableMap::COL_ID, $sProductVariantPrice->getTypeId(), $comparison);
        } elseif ($sProductVariantPrice instanceof ObjectCollection) {
            return $this
                ->useSProductVariantPriceQuery()
                ->filterByPrimaryKeys($sProductVariantPrice->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterBySProductVariantPrice() only accepts arguments of type \SProductVariantPrice or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the SProductVariantPrice relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildSProductVariantPriceTypeQuery The current query, for fluid interface
     */
    public function joinSProductVariantPrice($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('SProductVariantPrice');

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
            $this->addJoinObject($join, 'SProductVariantPrice');
        }

        return $this;
    }

    /**
     * Use the SProductVariantPrice relation SProductVariantPrice object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \SProductVariantPriceQuery A secondary query class using the current class as primary query
     */
    public function useSProductVariantPriceQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinSProductVariantPrice($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'SProductVariantPrice', '\SProductVariantPriceQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildSProductVariantPriceType $sProductVariantPriceType Object to remove from the list of results
     *
     * @return $this|ChildSProductVariantPriceTypeQuery The current query, for fluid interface
     */
    public function prune($sProductVariantPriceType = null)
    {
        if ($sProductVariantPriceType) {
            $this->addUsingAlias(SProductVariantPriceTypeTableMap::COL_ID, $sProductVariantPriceType->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the shop_product_variants_price_types table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(SProductVariantPriceTypeTableMap::DATABASE_NAME);
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
            SProductVariantPriceTypeTableMap::clearInstancePool();
            SProductVariantPriceTypeTableMap::clearRelatedInstancePool();

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
            $con = Propel::getServiceContainer()->getWriteConnection(SProductVariantPriceTypeTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(SProductVariantPriceTypeTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            // cloning the Criteria in case it's modified by doSelect() or doSelectStmt()
            $c = clone $criteria;
            $affectedRows += $c->doOnDeleteCascade($con);

            SProductVariantPriceTypeTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            SProductVariantPriceTypeTableMap::clearRelatedInstancePool();

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
        $objects = ChildSProductVariantPriceTypeQuery::create(null, $this)->find($con);
        foreach ($objects as $obj) {


            // delete related SProductVariantPriceTypeValue objects
            $query = new \SProductVariantPriceTypeValueQuery;

            $query->add(SProductVariantPriceTypeValueTableMap::COL_PRICE_TYPE_ID, $obj->getId());
            $affectedRows += $query->delete($con);

            // delete related SProductVariantPrice objects
            $query = new \SProductVariantPriceQuery;

            $query->add(SProductVariantPriceTableMap::COL_TYPE_ID, $obj->getId());
            $affectedRows += $query->delete($con);
        }

        return $affectedRows;
    }

} // SProductVariantPriceTypeQuery
