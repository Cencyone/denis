<?php

namespace Base;

use \SCategory as ChildSCategory;
use \SCategoryI18nQuery as ChildSCategoryI18nQuery;
use \SCategoryQuery as ChildSCategoryQuery;
use \Exception;
use \PDO;
use Map\SCategoryI18nTableMap;
use Map\SCategoryTableMap;
use Map\SProductsTableMap;
use Map\ShopProductCategoriesTableMap;
use Map\ShopProductPropertiesCategoriesTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;
use core\models\Route;
use smart_filter\models\SFilterPattern;
use smart_filter\models\Map\SFilterPatternTableMap;

/**
 * Base class that represents a query for the 'shop_category' table.
 *
 *
 *
 * @method     ChildSCategoryQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildSCategoryQuery orderByParentId($order = Criteria::ASC) Order by the parent_id column
 * @method     ChildSCategoryQuery orderByRouteId($order = Criteria::ASC) Order by the route_id column
 * @method     ChildSCategoryQuery orderByExternalId($order = Criteria::ASC) Order by the external_id column
 * @method     ChildSCategoryQuery orderByActive($order = Criteria::ASC) Order by the active column
 * @method     ChildSCategoryQuery orderByImage($order = Criteria::ASC) Order by the image column
 * @method     ChildSCategoryQuery orderByPosition($order = Criteria::ASC) Order by the position column
 * @method     ChildSCategoryQuery orderByFullPathIds($order = Criteria::ASC) Order by the full_path_ids column
 * @method     ChildSCategoryQuery orderByTpl($order = Criteria::ASC) Order by the tpl column
 * @method     ChildSCategoryQuery orderByOrderMethod($order = Criteria::ASC) Order by the order_method column
 * @method     ChildSCategoryQuery orderByShowsitetitle($order = Criteria::ASC) Order by the showsitetitle column
 * @method     ChildSCategoryQuery orderByShowInMenu($order = Criteria::ASC) Order by the show_in_menu column
 * @method     ChildSCategoryQuery orderByCreated($order = Criteria::ASC) Order by the created column
 * @method     ChildSCategoryQuery orderByUpdated($order = Criteria::ASC) Order by the updated column
 *
 * @method     ChildSCategoryQuery groupById() Group by the id column
 * @method     ChildSCategoryQuery groupByParentId() Group by the parent_id column
 * @method     ChildSCategoryQuery groupByRouteId() Group by the route_id column
 * @method     ChildSCategoryQuery groupByExternalId() Group by the external_id column
 * @method     ChildSCategoryQuery groupByActive() Group by the active column
 * @method     ChildSCategoryQuery groupByImage() Group by the image column
 * @method     ChildSCategoryQuery groupByPosition() Group by the position column
 * @method     ChildSCategoryQuery groupByFullPathIds() Group by the full_path_ids column
 * @method     ChildSCategoryQuery groupByTpl() Group by the tpl column
 * @method     ChildSCategoryQuery groupByOrderMethod() Group by the order_method column
 * @method     ChildSCategoryQuery groupByShowsitetitle() Group by the showsitetitle column
 * @method     ChildSCategoryQuery groupByShowInMenu() Group by the show_in_menu column
 * @method     ChildSCategoryQuery groupByCreated() Group by the created column
 * @method     ChildSCategoryQuery groupByUpdated() Group by the updated column
 *
 * @method     ChildSCategoryQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildSCategoryQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildSCategoryQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildSCategoryQuery leftJoinWith($relation) Adds a LEFT JOIN clause and with to the query
 * @method     ChildSCategoryQuery rightJoinWith($relation) Adds a RIGHT JOIN clause and with to the query
 * @method     ChildSCategoryQuery innerJoinWith($relation) Adds a INNER JOIN clause and with to the query
 *
 * @method     ChildSCategoryQuery leftJoinSCategory($relationAlias = null) Adds a LEFT JOIN clause to the query using the SCategory relation
 * @method     ChildSCategoryQuery rightJoinSCategory($relationAlias = null) Adds a RIGHT JOIN clause to the query using the SCategory relation
 * @method     ChildSCategoryQuery innerJoinSCategory($relationAlias = null) Adds a INNER JOIN clause to the query using the SCategory relation
 *
 * @method     ChildSCategoryQuery joinWithSCategory($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the SCategory relation
 *
 * @method     ChildSCategoryQuery leftJoinWithSCategory() Adds a LEFT JOIN clause and with to the query using the SCategory relation
 * @method     ChildSCategoryQuery rightJoinWithSCategory() Adds a RIGHT JOIN clause and with to the query using the SCategory relation
 * @method     ChildSCategoryQuery innerJoinWithSCategory() Adds a INNER JOIN clause and with to the query using the SCategory relation
 *
 * @method     ChildSCategoryQuery leftJoinRoute($relationAlias = null) Adds a LEFT JOIN clause to the query using the Route relation
 * @method     ChildSCategoryQuery rightJoinRoute($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Route relation
 * @method     ChildSCategoryQuery innerJoinRoute($relationAlias = null) Adds a INNER JOIN clause to the query using the Route relation
 *
 * @method     ChildSCategoryQuery joinWithRoute($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the Route relation
 *
 * @method     ChildSCategoryQuery leftJoinWithRoute() Adds a LEFT JOIN clause and with to the query using the Route relation
 * @method     ChildSCategoryQuery rightJoinWithRoute() Adds a RIGHT JOIN clause and with to the query using the Route relation
 * @method     ChildSCategoryQuery innerJoinWithRoute() Adds a INNER JOIN clause and with to the query using the Route relation
 *
 * @method     ChildSCategoryQuery leftJoinSCategoryRelatedById($relationAlias = null) Adds a LEFT JOIN clause to the query using the SCategoryRelatedById relation
 * @method     ChildSCategoryQuery rightJoinSCategoryRelatedById($relationAlias = null) Adds a RIGHT JOIN clause to the query using the SCategoryRelatedById relation
 * @method     ChildSCategoryQuery innerJoinSCategoryRelatedById($relationAlias = null) Adds a INNER JOIN clause to the query using the SCategoryRelatedById relation
 *
 * @method     ChildSCategoryQuery joinWithSCategoryRelatedById($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the SCategoryRelatedById relation
 *
 * @method     ChildSCategoryQuery leftJoinWithSCategoryRelatedById() Adds a LEFT JOIN clause and with to the query using the SCategoryRelatedById relation
 * @method     ChildSCategoryQuery rightJoinWithSCategoryRelatedById() Adds a RIGHT JOIN clause and with to the query using the SCategoryRelatedById relation
 * @method     ChildSCategoryQuery innerJoinWithSCategoryRelatedById() Adds a INNER JOIN clause and with to the query using the SCategoryRelatedById relation
 *
 * @method     ChildSCategoryQuery leftJoinSCategoryI18n($relationAlias = null) Adds a LEFT JOIN clause to the query using the SCategoryI18n relation
 * @method     ChildSCategoryQuery rightJoinSCategoryI18n($relationAlias = null) Adds a RIGHT JOIN clause to the query using the SCategoryI18n relation
 * @method     ChildSCategoryQuery innerJoinSCategoryI18n($relationAlias = null) Adds a INNER JOIN clause to the query using the SCategoryI18n relation
 *
 * @method     ChildSCategoryQuery joinWithSCategoryI18n($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the SCategoryI18n relation
 *
 * @method     ChildSCategoryQuery leftJoinWithSCategoryI18n() Adds a LEFT JOIN clause and with to the query using the SCategoryI18n relation
 * @method     ChildSCategoryQuery rightJoinWithSCategoryI18n() Adds a RIGHT JOIN clause and with to the query using the SCategoryI18n relation
 * @method     ChildSCategoryQuery innerJoinWithSCategoryI18n() Adds a INNER JOIN clause and with to the query using the SCategoryI18n relation
 *
 * @method     ChildSCategoryQuery leftJoinSProducts($relationAlias = null) Adds a LEFT JOIN clause to the query using the SProducts relation
 * @method     ChildSCategoryQuery rightJoinSProducts($relationAlias = null) Adds a RIGHT JOIN clause to the query using the SProducts relation
 * @method     ChildSCategoryQuery innerJoinSProducts($relationAlias = null) Adds a INNER JOIN clause to the query using the SProducts relation
 *
 * @method     ChildSCategoryQuery joinWithSProducts($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the SProducts relation
 *
 * @method     ChildSCategoryQuery leftJoinWithSProducts() Adds a LEFT JOIN clause and with to the query using the SProducts relation
 * @method     ChildSCategoryQuery rightJoinWithSProducts() Adds a RIGHT JOIN clause and with to the query using the SProducts relation
 * @method     ChildSCategoryQuery innerJoinWithSProducts() Adds a INNER JOIN clause and with to the query using the SProducts relation
 *
 * @method     ChildSCategoryQuery leftJoinShopProductCategories($relationAlias = null) Adds a LEFT JOIN clause to the query using the ShopProductCategories relation
 * @method     ChildSCategoryQuery rightJoinShopProductCategories($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ShopProductCategories relation
 * @method     ChildSCategoryQuery innerJoinShopProductCategories($relationAlias = null) Adds a INNER JOIN clause to the query using the ShopProductCategories relation
 *
 * @method     ChildSCategoryQuery joinWithShopProductCategories($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the ShopProductCategories relation
 *
 * @method     ChildSCategoryQuery leftJoinWithShopProductCategories() Adds a LEFT JOIN clause and with to the query using the ShopProductCategories relation
 * @method     ChildSCategoryQuery rightJoinWithShopProductCategories() Adds a RIGHT JOIN clause and with to the query using the ShopProductCategories relation
 * @method     ChildSCategoryQuery innerJoinWithShopProductCategories() Adds a INNER JOIN clause and with to the query using the ShopProductCategories relation
 *
 * @method     ChildSCategoryQuery leftJoinShopProductPropertiesCategories($relationAlias = null) Adds a LEFT JOIN clause to the query using the ShopProductPropertiesCategories relation
 * @method     ChildSCategoryQuery rightJoinShopProductPropertiesCategories($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ShopProductPropertiesCategories relation
 * @method     ChildSCategoryQuery innerJoinShopProductPropertiesCategories($relationAlias = null) Adds a INNER JOIN clause to the query using the ShopProductPropertiesCategories relation
 *
 * @method     ChildSCategoryQuery joinWithShopProductPropertiesCategories($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the ShopProductPropertiesCategories relation
 *
 * @method     ChildSCategoryQuery leftJoinWithShopProductPropertiesCategories() Adds a LEFT JOIN clause and with to the query using the ShopProductPropertiesCategories relation
 * @method     ChildSCategoryQuery rightJoinWithShopProductPropertiesCategories() Adds a RIGHT JOIN clause and with to the query using the ShopProductPropertiesCategories relation
 * @method     ChildSCategoryQuery innerJoinWithShopProductPropertiesCategories() Adds a INNER JOIN clause and with to the query using the ShopProductPropertiesCategories relation
 *
 * @method     ChildSCategoryQuery leftJoinSFilterPattern($relationAlias = null) Adds a LEFT JOIN clause to the query using the SFilterPattern relation
 * @method     ChildSCategoryQuery rightJoinSFilterPattern($relationAlias = null) Adds a RIGHT JOIN clause to the query using the SFilterPattern relation
 * @method     ChildSCategoryQuery innerJoinSFilterPattern($relationAlias = null) Adds a INNER JOIN clause to the query using the SFilterPattern relation
 *
 * @method     ChildSCategoryQuery joinWithSFilterPattern($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the SFilterPattern relation
 *
 * @method     ChildSCategoryQuery leftJoinWithSFilterPattern() Adds a LEFT JOIN clause and with to the query using the SFilterPattern relation
 * @method     ChildSCategoryQuery rightJoinWithSFilterPattern() Adds a RIGHT JOIN clause and with to the query using the SFilterPattern relation
 * @method     ChildSCategoryQuery innerJoinWithSFilterPattern() Adds a INNER JOIN clause and with to the query using the SFilterPattern relation
 *
 * @method     \SCategoryQuery|\core\models\RouteQuery|\SCategoryI18nQuery|\SProductsQuery|\ShopProductCategoriesQuery|\ShopProductPropertiesCategoriesQuery|\smart_filter\models\SFilterPatternQuery endUse() Finalizes a secondary criteria and merges it with its primary Criteria
 *
 * @method     ChildSCategory findOne(ConnectionInterface $con = null) Return the first ChildSCategory matching the query
 * @method     ChildSCategory findOneOrCreate(ConnectionInterface $con = null) Return the first ChildSCategory matching the query, or a new ChildSCategory object populated from the query conditions when no match is found
 *
 * @method     ChildSCategory findOneById(int $id) Return the first ChildSCategory filtered by the id column
 * @method     ChildSCategory findOneByParentId(int $parent_id) Return the first ChildSCategory filtered by the parent_id column
 * @method     ChildSCategory findOneByRouteId(int $route_id) Return the first ChildSCategory filtered by the route_id column
 * @method     ChildSCategory findOneByExternalId(string $external_id) Return the first ChildSCategory filtered by the external_id column
 * @method     ChildSCategory findOneByActive(boolean $active) Return the first ChildSCategory filtered by the active column
 * @method     ChildSCategory findOneByImage(string $image) Return the first ChildSCategory filtered by the image column
 * @method     ChildSCategory findOneByPosition(int $position) Return the first ChildSCategory filtered by the position column
 * @method     ChildSCategory findOneByFullPathIds(string $full_path_ids) Return the first ChildSCategory filtered by the full_path_ids column
 * @method     ChildSCategory findOneByTpl(string $tpl) Return the first ChildSCategory filtered by the tpl column
 * @method     ChildSCategory findOneByOrderMethod(int $order_method) Return the first ChildSCategory filtered by the order_method column
 * @method     ChildSCategory findOneByShowsitetitle(int $showsitetitle) Return the first ChildSCategory filtered by the showsitetitle column
 * @method     ChildSCategory findOneByShowInMenu(boolean $show_in_menu) Return the first ChildSCategory filtered by the show_in_menu column
 * @method     ChildSCategory findOneByCreated(int $created) Return the first ChildSCategory filtered by the created column
 * @method     ChildSCategory findOneByUpdated(int $updated) Return the first ChildSCategory filtered by the updated column *

 * @method     ChildSCategory requirePk($key, ConnectionInterface $con = null) Return the ChildSCategory by primary key and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSCategory requireOne(ConnectionInterface $con = null) Return the first ChildSCategory matching the query and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildSCategory requireOneById(int $id) Return the first ChildSCategory filtered by the id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSCategory requireOneByParentId(int $parent_id) Return the first ChildSCategory filtered by the parent_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSCategory requireOneByRouteId(int $route_id) Return the first ChildSCategory filtered by the route_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSCategory requireOneByExternalId(string $external_id) Return the first ChildSCategory filtered by the external_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSCategory requireOneByActive(boolean $active) Return the first ChildSCategory filtered by the active column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSCategory requireOneByImage(string $image) Return the first ChildSCategory filtered by the image column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSCategory requireOneByPosition(int $position) Return the first ChildSCategory filtered by the position column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSCategory requireOneByFullPathIds(string $full_path_ids) Return the first ChildSCategory filtered by the full_path_ids column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSCategory requireOneByTpl(string $tpl) Return the first ChildSCategory filtered by the tpl column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSCategory requireOneByOrderMethod(int $order_method) Return the first ChildSCategory filtered by the order_method column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSCategory requireOneByShowsitetitle(int $showsitetitle) Return the first ChildSCategory filtered by the showsitetitle column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSCategory requireOneByShowInMenu(boolean $show_in_menu) Return the first ChildSCategory filtered by the show_in_menu column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSCategory requireOneByCreated(int $created) Return the first ChildSCategory filtered by the created column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSCategory requireOneByUpdated(int $updated) Return the first ChildSCategory filtered by the updated column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildSCategory[]|ObjectCollection find(ConnectionInterface $con = null) Return ChildSCategory objects based on current ModelCriteria
 * @method     ChildSCategory[]|ObjectCollection findById(int $id) Return ChildSCategory objects filtered by the id column
 * @method     ChildSCategory[]|ObjectCollection findByParentId(int $parent_id) Return ChildSCategory objects filtered by the parent_id column
 * @method     ChildSCategory[]|ObjectCollection findByRouteId(int $route_id) Return ChildSCategory objects filtered by the route_id column
 * @method     ChildSCategory[]|ObjectCollection findByExternalId(string $external_id) Return ChildSCategory objects filtered by the external_id column
 * @method     ChildSCategory[]|ObjectCollection findByActive(boolean $active) Return ChildSCategory objects filtered by the active column
 * @method     ChildSCategory[]|ObjectCollection findByImage(string $image) Return ChildSCategory objects filtered by the image column
 * @method     ChildSCategory[]|ObjectCollection findByPosition(int $position) Return ChildSCategory objects filtered by the position column
 * @method     ChildSCategory[]|ObjectCollection findByFullPathIds(string $full_path_ids) Return ChildSCategory objects filtered by the full_path_ids column
 * @method     ChildSCategory[]|ObjectCollection findByTpl(string $tpl) Return ChildSCategory objects filtered by the tpl column
 * @method     ChildSCategory[]|ObjectCollection findByOrderMethod(int $order_method) Return ChildSCategory objects filtered by the order_method column
 * @method     ChildSCategory[]|ObjectCollection findByShowsitetitle(int $showsitetitle) Return ChildSCategory objects filtered by the showsitetitle column
 * @method     ChildSCategory[]|ObjectCollection findByShowInMenu(boolean $show_in_menu) Return ChildSCategory objects filtered by the show_in_menu column
 * @method     ChildSCategory[]|ObjectCollection findByCreated(int $created) Return ChildSCategory objects filtered by the created column
 * @method     ChildSCategory[]|ObjectCollection findByUpdated(int $updated) Return ChildSCategory objects filtered by the updated column
 * @method     ChildSCategory[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 *
 */
abstract class SCategoryQuery extends ModelCriteria
{

    // delegate behavior

    protected $delegatedFields = [
        'EntityId' => 'Route',
        'Type' => 'Route',
        'ParentUrl' => 'Route',
        'Url' => 'Route',
    ];

protected $entityNotFoundExceptionClass = '\\Propel\\Runtime\\Exception\\EntityNotFoundException';

    /**
     * Initializes internal state of \Base\SCategoryQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'Shop', $modelName = '\\SCategory', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildSCategoryQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildSCategoryQuery
     */
    public static function create($modelAlias = null, Criteria $criteria = null)
    {
        if ($criteria instanceof ChildSCategoryQuery) {
            return $criteria;
        }
        $query = new ChildSCategoryQuery();
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
     * @return ChildSCategory|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(SCategoryTableMap::DATABASE_NAME);
        }

        $this->basePreSelect($con);

        if (
            $this->formatter || $this->modelAlias || $this->with || $this->select
            || $this->selectColumns || $this->asColumns || $this->selectModifiers
            || $this->map || $this->having || $this->joins
        ) {
            return $this->findPkComplex($key, $con);
        }

        if ((null !== ($obj = SCategoryTableMap::getInstanceFromPool(null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key)))) {
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
     * @return ChildSCategory A model object, or null if the key is not found
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT id, parent_id, route_id, external_id, active, image, position, full_path_ids, tpl, order_method, showsitetitle, show_in_menu, created, updated FROM shop_category WHERE id = :p0';
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
            /** @var ChildSCategory $obj */
            $obj = new ChildSCategory();
            $obj->hydrate($row);
            SCategoryTableMap::addInstanceToPool($obj, null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key);
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
     * @return ChildSCategory|array|mixed the result, formatted by the current formatter
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
     * @return $this|ChildSCategoryQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(SCategoryTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return $this|ChildSCategoryQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(SCategoryTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return $this|ChildSCategoryQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(SCategoryTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(SCategoryTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SCategoryTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the parent_id column
     *
     * Example usage:
     * <code>
     * $query->filterByParentId(1234); // WHERE parent_id = 1234
     * $query->filterByParentId(array(12, 34)); // WHERE parent_id IN (12, 34)
     * $query->filterByParentId(array('min' => 12)); // WHERE parent_id > 12
     * </code>
     *
     * @see       filterBySCategory()
     *
     * @param     mixed $parentId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildSCategoryQuery The current query, for fluid interface
     */
    public function filterByParentId($parentId = null, $comparison = null)
    {
        if (is_array($parentId)) {
            $useMinMax = false;
            if (isset($parentId['min'])) {
                $this->addUsingAlias(SCategoryTableMap::COL_PARENT_ID, $parentId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($parentId['max'])) {
                $this->addUsingAlias(SCategoryTableMap::COL_PARENT_ID, $parentId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SCategoryTableMap::COL_PARENT_ID, $parentId, $comparison);
    }

    /**
     * Filter the query on the route_id column
     *
     * Example usage:
     * <code>
     * $query->filterByRouteId(1234); // WHERE route_id = 1234
     * $query->filterByRouteId(array(12, 34)); // WHERE route_id IN (12, 34)
     * $query->filterByRouteId(array('min' => 12)); // WHERE route_id > 12
     * </code>
     *
     * @see       filterByRoute()
     *
     * @param     mixed $routeId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildSCategoryQuery The current query, for fluid interface
     */
    public function filterByRouteId($routeId = null, $comparison = null)
    {
        if (is_array($routeId)) {
            $useMinMax = false;
            if (isset($routeId['min'])) {
                $this->addUsingAlias(SCategoryTableMap::COL_ROUTE_ID, $routeId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($routeId['max'])) {
                $this->addUsingAlias(SCategoryTableMap::COL_ROUTE_ID, $routeId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SCategoryTableMap::COL_ROUTE_ID, $routeId, $comparison);
    }

    /**
     * Filter the query on the external_id column
     *
     * Example usage:
     * <code>
     * $query->filterByExternalId('fooValue');   // WHERE external_id = 'fooValue'
     * $query->filterByExternalId('%fooValue%', Criteria::LIKE); // WHERE external_id LIKE '%fooValue%'
     * </code>
     *
     * @param     string $externalId The value to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildSCategoryQuery The current query, for fluid interface
     */
    public function filterByExternalId($externalId = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($externalId)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SCategoryTableMap::COL_EXTERNAL_ID, $externalId, $comparison);
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
     * @return $this|ChildSCategoryQuery The current query, for fluid interface
     */
    public function filterByActive($active = null, $comparison = null)
    {
        if (is_string($active)) {
            $active = in_array(strtolower($active), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(SCategoryTableMap::COL_ACTIVE, $active, $comparison);
    }

    /**
     * Filter the query on the image column
     *
     * Example usage:
     * <code>
     * $query->filterByImage('fooValue');   // WHERE image = 'fooValue'
     * $query->filterByImage('%fooValue%', Criteria::LIKE); // WHERE image LIKE '%fooValue%'
     * </code>
     *
     * @param     string $image The value to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildSCategoryQuery The current query, for fluid interface
     */
    public function filterByImage($image = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($image)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SCategoryTableMap::COL_IMAGE, $image, $comparison);
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
     * @return $this|ChildSCategoryQuery The current query, for fluid interface
     */
    public function filterByPosition($position = null, $comparison = null)
    {
        if (is_array($position)) {
            $useMinMax = false;
            if (isset($position['min'])) {
                $this->addUsingAlias(SCategoryTableMap::COL_POSITION, $position['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($position['max'])) {
                $this->addUsingAlias(SCategoryTableMap::COL_POSITION, $position['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SCategoryTableMap::COL_POSITION, $position, $comparison);
    }

    /**
     * Filter the query on the full_path_ids column
     *
     * Example usage:
     * <code>
     * $query->filterByFullPathIds('fooValue');   // WHERE full_path_ids = 'fooValue'
     * $query->filterByFullPathIds('%fooValue%', Criteria::LIKE); // WHERE full_path_ids LIKE '%fooValue%'
     * </code>
     *
     * @param     string $fullPathIds The value to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildSCategoryQuery The current query, for fluid interface
     */
    public function filterByFullPathIds($fullPathIds = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($fullPathIds)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SCategoryTableMap::COL_FULL_PATH_IDS, $fullPathIds, $comparison);
    }

    /**
     * Filter the query on the tpl column
     *
     * Example usage:
     * <code>
     * $query->filterByTpl('fooValue');   // WHERE tpl = 'fooValue'
     * $query->filterByTpl('%fooValue%', Criteria::LIKE); // WHERE tpl LIKE '%fooValue%'
     * </code>
     *
     * @param     string $tpl The value to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildSCategoryQuery The current query, for fluid interface
     */
    public function filterByTpl($tpl = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($tpl)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SCategoryTableMap::COL_TPL, $tpl, $comparison);
    }

    /**
     * Filter the query on the order_method column
     *
     * Example usage:
     * <code>
     * $query->filterByOrderMethod(1234); // WHERE order_method = 1234
     * $query->filterByOrderMethod(array(12, 34)); // WHERE order_method IN (12, 34)
     * $query->filterByOrderMethod(array('min' => 12)); // WHERE order_method > 12
     * </code>
     *
     * @param     mixed $orderMethod The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildSCategoryQuery The current query, for fluid interface
     */
    public function filterByOrderMethod($orderMethod = null, $comparison = null)
    {
        if (is_array($orderMethod)) {
            $useMinMax = false;
            if (isset($orderMethod['min'])) {
                $this->addUsingAlias(SCategoryTableMap::COL_ORDER_METHOD, $orderMethod['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($orderMethod['max'])) {
                $this->addUsingAlias(SCategoryTableMap::COL_ORDER_METHOD, $orderMethod['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SCategoryTableMap::COL_ORDER_METHOD, $orderMethod, $comparison);
    }

    /**
     * Filter the query on the showsitetitle column
     *
     * Example usage:
     * <code>
     * $query->filterByShowsitetitle(1234); // WHERE showsitetitle = 1234
     * $query->filterByShowsitetitle(array(12, 34)); // WHERE showsitetitle IN (12, 34)
     * $query->filterByShowsitetitle(array('min' => 12)); // WHERE showsitetitle > 12
     * </code>
     *
     * @param     mixed $showsitetitle The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildSCategoryQuery The current query, for fluid interface
     */
    public function filterByShowsitetitle($showsitetitle = null, $comparison = null)
    {
        if (is_array($showsitetitle)) {
            $useMinMax = false;
            if (isset($showsitetitle['min'])) {
                $this->addUsingAlias(SCategoryTableMap::COL_SHOWSITETITLE, $showsitetitle['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($showsitetitle['max'])) {
                $this->addUsingAlias(SCategoryTableMap::COL_SHOWSITETITLE, $showsitetitle['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SCategoryTableMap::COL_SHOWSITETITLE, $showsitetitle, $comparison);
    }

    /**
     * Filter the query on the show_in_menu column
     *
     * Example usage:
     * <code>
     * $query->filterByShowInMenu(true); // WHERE show_in_menu = true
     * $query->filterByShowInMenu('yes'); // WHERE show_in_menu = true
     * </code>
     *
     * @param     boolean|string $showInMenu The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildSCategoryQuery The current query, for fluid interface
     */
    public function filterByShowInMenu($showInMenu = null, $comparison = null)
    {
        if (is_string($showInMenu)) {
            $showInMenu = in_array(strtolower($showInMenu), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(SCategoryTableMap::COL_SHOW_IN_MENU, $showInMenu, $comparison);
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
     * @return $this|ChildSCategoryQuery The current query, for fluid interface
     */
    public function filterByCreated($created = null, $comparison = null)
    {
        if (is_array($created)) {
            $useMinMax = false;
            if (isset($created['min'])) {
                $this->addUsingAlias(SCategoryTableMap::COL_CREATED, $created['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($created['max'])) {
                $this->addUsingAlias(SCategoryTableMap::COL_CREATED, $created['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SCategoryTableMap::COL_CREATED, $created, $comparison);
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
     * @return $this|ChildSCategoryQuery The current query, for fluid interface
     */
    public function filterByUpdated($updated = null, $comparison = null)
    {
        if (is_array($updated)) {
            $useMinMax = false;
            if (isset($updated['min'])) {
                $this->addUsingAlias(SCategoryTableMap::COL_UPDATED, $updated['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updated['max'])) {
                $this->addUsingAlias(SCategoryTableMap::COL_UPDATED, $updated['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SCategoryTableMap::COL_UPDATED, $updated, $comparison);
    }

    /**
     * Filter the query by a related \SCategory object
     *
     * @param \SCategory|ObjectCollection $sCategory The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildSCategoryQuery The current query, for fluid interface
     */
    public function filterBySCategory($sCategory, $comparison = null)
    {
        if ($sCategory instanceof \SCategory) {
            return $this
                ->addUsingAlias(SCategoryTableMap::COL_PARENT_ID, $sCategory->getId(), $comparison);
        } elseif ($sCategory instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(SCategoryTableMap::COL_PARENT_ID, $sCategory->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterBySCategory() only accepts arguments of type \SCategory or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the SCategory relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildSCategoryQuery The current query, for fluid interface
     */
    public function joinSCategory($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('SCategory');

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
            $this->addJoinObject($join, 'SCategory');
        }

        return $this;
    }

    /**
     * Use the SCategory relation SCategory object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \SCategoryQuery A secondary query class using the current class as primary query
     */
    public function useSCategoryQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinSCategory($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'SCategory', '\SCategoryQuery');
    }

    /**
     * Filter the query by a related \core\models\Route object
     *
     * @param \core\models\Route|ObjectCollection $route The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildSCategoryQuery The current query, for fluid interface
     */
    public function filterByRoute($route, $comparison = null)
    {
        if ($route instanceof \core\models\Route) {
            return $this
                ->addUsingAlias(SCategoryTableMap::COL_ROUTE_ID, $route->getId(), $comparison);
        } elseif ($route instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(SCategoryTableMap::COL_ROUTE_ID, $route->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByRoute() only accepts arguments of type \core\models\Route or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Route relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildSCategoryQuery The current query, for fluid interface
     */
    public function joinRoute($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Route');

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
            $this->addJoinObject($join, 'Route');
        }

        return $this;
    }

    /**
     * Use the Route relation Route object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \core\models\RouteQuery A secondary query class using the current class as primary query
     */
    public function useRouteQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinRoute($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Route', '\core\models\RouteQuery');
    }

    /**
     * Filter the query by a related \SCategory object
     *
     * @param \SCategory|ObjectCollection $sCategory the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSCategoryQuery The current query, for fluid interface
     */
    public function filterBySCategoryRelatedById($sCategory, $comparison = null)
    {
        if ($sCategory instanceof \SCategory) {
            return $this
                ->addUsingAlias(SCategoryTableMap::COL_ID, $sCategory->getParentId(), $comparison);
        } elseif ($sCategory instanceof ObjectCollection) {
            return $this
                ->useSCategoryRelatedByIdQuery()
                ->filterByPrimaryKeys($sCategory->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterBySCategoryRelatedById() only accepts arguments of type \SCategory or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the SCategoryRelatedById relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildSCategoryQuery The current query, for fluid interface
     */
    public function joinSCategoryRelatedById($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('SCategoryRelatedById');

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
            $this->addJoinObject($join, 'SCategoryRelatedById');
        }

        return $this;
    }

    /**
     * Use the SCategoryRelatedById relation SCategory object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \SCategoryQuery A secondary query class using the current class as primary query
     */
    public function useSCategoryRelatedByIdQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinSCategoryRelatedById($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'SCategoryRelatedById', '\SCategoryQuery');
    }

    /**
     * Filter the query by a related \SCategoryI18n object
     *
     * @param \SCategoryI18n|ObjectCollection $sCategoryI18n the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSCategoryQuery The current query, for fluid interface
     */
    public function filterBySCategoryI18n($sCategoryI18n, $comparison = null)
    {
        if ($sCategoryI18n instanceof \SCategoryI18n) {
            return $this
                ->addUsingAlias(SCategoryTableMap::COL_ID, $sCategoryI18n->getId(), $comparison);
        } elseif ($sCategoryI18n instanceof ObjectCollection) {
            return $this
                ->useSCategoryI18nQuery()
                ->filterByPrimaryKeys($sCategoryI18n->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterBySCategoryI18n() only accepts arguments of type \SCategoryI18n or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the SCategoryI18n relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildSCategoryQuery The current query, for fluid interface
     */
    public function joinSCategoryI18n($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('SCategoryI18n');

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
            $this->addJoinObject($join, 'SCategoryI18n');
        }

        return $this;
    }

    /**
     * Use the SCategoryI18n relation SCategoryI18n object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \SCategoryI18nQuery A secondary query class using the current class as primary query
     */
    public function useSCategoryI18nQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinSCategoryI18n($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'SCategoryI18n', '\SCategoryI18nQuery');
    }

    /**
     * Filter the query by a related \SProducts object
     *
     * @param \SProducts|ObjectCollection $sProducts the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSCategoryQuery The current query, for fluid interface
     */
    public function filterBySProducts($sProducts, $comparison = null)
    {
        if ($sProducts instanceof \SProducts) {
            return $this
                ->addUsingAlias(SCategoryTableMap::COL_ID, $sProducts->getCategoryId(), $comparison);
        } elseif ($sProducts instanceof ObjectCollection) {
            return $this
                ->useSProductsQuery()
                ->filterByPrimaryKeys($sProducts->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterBySProducts() only accepts arguments of type \SProducts or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the SProducts relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildSCategoryQuery The current query, for fluid interface
     */
    public function joinSProducts($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('SProducts');

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
            $this->addJoinObject($join, 'SProducts');
        }

        return $this;
    }

    /**
     * Use the SProducts relation SProducts object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \SProductsQuery A secondary query class using the current class as primary query
     */
    public function useSProductsQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinSProducts($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'SProducts', '\SProductsQuery');
    }

    /**
     * Filter the query by a related \ShopProductCategories object
     *
     * @param \ShopProductCategories|ObjectCollection $shopProductCategories the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSCategoryQuery The current query, for fluid interface
     */
    public function filterByShopProductCategories($shopProductCategories, $comparison = null)
    {
        if ($shopProductCategories instanceof \ShopProductCategories) {
            return $this
                ->addUsingAlias(SCategoryTableMap::COL_ID, $shopProductCategories->getCategoryId(), $comparison);
        } elseif ($shopProductCategories instanceof ObjectCollection) {
            return $this
                ->useShopProductCategoriesQuery()
                ->filterByPrimaryKeys($shopProductCategories->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByShopProductCategories() only accepts arguments of type \ShopProductCategories or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ShopProductCategories relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildSCategoryQuery The current query, for fluid interface
     */
    public function joinShopProductCategories($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ShopProductCategories');

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
            $this->addJoinObject($join, 'ShopProductCategories');
        }

        return $this;
    }

    /**
     * Use the ShopProductCategories relation ShopProductCategories object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \ShopProductCategoriesQuery A secondary query class using the current class as primary query
     */
    public function useShopProductCategoriesQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinShopProductCategories($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ShopProductCategories', '\ShopProductCategoriesQuery');
    }

    /**
     * Filter the query by a related \ShopProductPropertiesCategories object
     *
     * @param \ShopProductPropertiesCategories|ObjectCollection $shopProductPropertiesCategories the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSCategoryQuery The current query, for fluid interface
     */
    public function filterByShopProductPropertiesCategories($shopProductPropertiesCategories, $comparison = null)
    {
        if ($shopProductPropertiesCategories instanceof \ShopProductPropertiesCategories) {
            return $this
                ->addUsingAlias(SCategoryTableMap::COL_ID, $shopProductPropertiesCategories->getCategoryId(), $comparison);
        } elseif ($shopProductPropertiesCategories instanceof ObjectCollection) {
            return $this
                ->useShopProductPropertiesCategoriesQuery()
                ->filterByPrimaryKeys($shopProductPropertiesCategories->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByShopProductPropertiesCategories() only accepts arguments of type \ShopProductPropertiesCategories or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ShopProductPropertiesCategories relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildSCategoryQuery The current query, for fluid interface
     */
    public function joinShopProductPropertiesCategories($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ShopProductPropertiesCategories');

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
            $this->addJoinObject($join, 'ShopProductPropertiesCategories');
        }

        return $this;
    }

    /**
     * Use the ShopProductPropertiesCategories relation ShopProductPropertiesCategories object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \ShopProductPropertiesCategoriesQuery A secondary query class using the current class as primary query
     */
    public function useShopProductPropertiesCategoriesQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinShopProductPropertiesCategories($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ShopProductPropertiesCategories', '\ShopProductPropertiesCategoriesQuery');
    }

    /**
     * Filter the query by a related \smart_filter\models\SFilterPattern object
     *
     * @param \smart_filter\models\SFilterPattern|ObjectCollection $sFilterPattern the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSCategoryQuery The current query, for fluid interface
     */
    public function filterBySFilterPattern($sFilterPattern, $comparison = null)
    {
        if ($sFilterPattern instanceof \smart_filter\models\SFilterPattern) {
            return $this
                ->addUsingAlias(SCategoryTableMap::COL_ID, $sFilterPattern->getCategoryId(), $comparison);
        } elseif ($sFilterPattern instanceof ObjectCollection) {
            return $this
                ->useSFilterPatternQuery()
                ->filterByPrimaryKeys($sFilterPattern->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterBySFilterPattern() only accepts arguments of type \smart_filter\models\SFilterPattern or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the SFilterPattern relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildSCategoryQuery The current query, for fluid interface
     */
    public function joinSFilterPattern($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('SFilterPattern');

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
            $this->addJoinObject($join, 'SFilterPattern');
        }

        return $this;
    }

    /**
     * Use the SFilterPattern relation SFilterPattern object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \smart_filter\models\SFilterPatternQuery A secondary query class using the current class as primary query
     */
    public function useSFilterPatternQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinSFilterPattern($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'SFilterPattern', '\smart_filter\models\SFilterPatternQuery');
    }

    /**
     * Filter the query by a related SProducts object
     * using the shop_product_categories table as cross reference
     *
     * @param SProducts $sProducts the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSCategoryQuery The current query, for fluid interface
     */
    public function filterByProduct($sProducts, $comparison = Criteria::EQUAL)
    {
        return $this
            ->useShopProductCategoriesQuery()
            ->filterByProduct($sProducts, $comparison)
            ->endUse();
    }

    /**
     * Filter the query by a related SProperties object
     * using the shop_product_properties_categories table as cross reference
     *
     * @param SProperties $sProperties the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSCategoryQuery The current query, for fluid interface
     */
    public function filterByProperty($sProperties, $comparison = Criteria::EQUAL)
    {
        return $this
            ->useShopProductPropertiesCategoriesQuery()
            ->filterByProperty($sProperties, $comparison)
            ->endUse();
    }

    /**
     * Exclude object from result
     *
     * @param   ChildSCategory $sCategory Object to remove from the list of results
     *
     * @return $this|ChildSCategoryQuery The current query, for fluid interface
     */
    public function prune($sCategory = null)
    {
        if ($sCategory) {
            $this->addUsingAlias(SCategoryTableMap::COL_ID, $sCategory->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the shop_category table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(SCategoryTableMap::DATABASE_NAME);
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
            SCategoryTableMap::clearInstancePool();
            SCategoryTableMap::clearRelatedInstancePool();

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
            $con = Propel::getServiceContainer()->getWriteConnection(SCategoryTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(SCategoryTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            // cloning the Criteria in case it's modified by doSelect() or doSelectStmt()
            $c = clone $criteria;
            $affectedRows += $c->doOnDeleteCascade($con);

            SCategoryTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            SCategoryTableMap::clearRelatedInstancePool();

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
        $objects = ChildSCategoryQuery::create(null, $this)->find($con);
        foreach ($objects as $obj) {


            // delete related SCategory objects
            $query = new \SCategoryQuery;

            $query->add(SCategoryTableMap::COL_PARENT_ID, $obj->getId());
            $affectedRows += $query->delete($con);

            // delete related SCategoryI18n objects
            $query = new \SCategoryI18nQuery;

            $query->add(SCategoryI18nTableMap::COL_ID, $obj->getId());
            $affectedRows += $query->delete($con);

            // delete related SProducts objects
            $query = new \SProductsQuery;

            $query->add(SProductsTableMap::COL_CATEGORY_ID, $obj->getId());
            $affectedRows += $query->delete($con);

            // delete related ShopProductCategories objects
            $query = new \ShopProductCategoriesQuery;

            $query->add(ShopProductCategoriesTableMap::COL_CATEGORY_ID, $obj->getId());
            $affectedRows += $query->delete($con);

            // delete related ShopProductPropertiesCategories objects
            $query = new \ShopProductPropertiesCategoriesQuery;

            $query->add(ShopProductPropertiesCategoriesTableMap::COL_CATEGORY_ID, $obj->getId());
            $affectedRows += $query->delete($con);

            // delete related SFilterPattern objects
            $query = new \smart_filter\models\SFilterPatternQuery;

            $query->add(SFilterPatternTableMap::COL_CATEGORY_ID, $obj->getId());
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
     * @return    ChildSCategoryQuery The current query, for fluid interface
     */
    public function joinI18n($locale = 'ru', $relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $relationName = $relationAlias ? $relationAlias : 'SCategoryI18n';

        return $this
            ->joinSCategoryI18n($relationAlias, $joinType)
            ->addJoinCondition($relationName, $relationName . '.Locale = ?', $locale);
    }

    /**
     * Adds a JOIN clause to the query and hydrates the related I18n object.
     * Shortcut for $c->joinI18n($locale)->with()
     *
     * @param     string $locale Locale to use for the join condition, e.g. 'fr_FR'
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'. Defaults to left join.
     *
     * @return    $this|ChildSCategoryQuery The current query, for fluid interface
     */
    public function joinWithI18n($locale = 'ru', $joinType = Criteria::LEFT_JOIN)
    {
        $this
            ->joinI18n($locale, null, $joinType)
            ->with('SCategoryI18n');
        $this->with['SCategoryI18n']->setIsWithOneToMany(false);

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
     * @return    ChildSCategoryI18nQuery A secondary query class using the current class as primary query
     */
    public function useI18nQuery($locale = 'ru', $relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinI18n($locale, $relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'SCategoryI18n', '\SCategoryI18nQuery');
    }

    // timestampable behavior

    /**
     * Filter by the latest updated
     *
     * @param      int $nbDays Maximum age of the latest update in days
     *
     * @return     $this|ChildSCategoryQuery The current query, for fluid interface
     */
    public function recentlyUpdated($nbDays = 7)
    {
        return $this->addUsingAlias(SCategoryTableMap::COL_UPDATED, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by update date desc
     *
     * @return     $this|ChildSCategoryQuery The current query, for fluid interface
     */
    public function lastUpdatedFirst()
    {
        return $this->addDescendingOrderByColumn(SCategoryTableMap::COL_UPDATED);
    }

    /**
     * Order by update date asc
     *
     * @return     $this|ChildSCategoryQuery The current query, for fluid interface
     */
    public function firstUpdatedFirst()
    {
        return $this->addAscendingOrderByColumn(SCategoryTableMap::COL_UPDATED);
    }

    /**
     * Order by create date desc
     *
     * @return     $this|ChildSCategoryQuery The current query, for fluid interface
     */
    public function lastCreatedFirst()
    {
        return $this->addDescendingOrderByColumn(SCategoryTableMap::COL_CREATED);
    }

    /**
     * Filter by the latest created
     *
     * @param      int $nbDays Maximum age of in days
     *
     * @return     $this|ChildSCategoryQuery The current query, for fluid interface
     */
    public function recentlyCreated($nbDays = 7)
    {
        return $this->addUsingAlias(SCategoryTableMap::COL_CREATED, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by create date asc
     *
     * @return     $this|ChildSCategoryQuery The current query, for fluid interface
     */
    public function firstCreatedFirst()
    {
        return $this->addAscendingOrderByColumn(SCategoryTableMap::COL_CREATED);
    }

    // delegate behavior
    /**
    * Filter the query by entity_id column
    *
    * Example usage:
    * <code>
        * $query->filterByEntityId(1234); // WHERE entity_id = 1234
        * $query->filterByEntityId(array(12, 34)); // WHERE entity_id IN (12, 34)
        * $query->filterByEntityId(array('min' => 12)); // WHERE entity_id >= 12
        * </code>
    *
    * @param     mixed $value The value to use as filter.
    *              Use scalar values for equality.
    *              Use array values for in_array() equivalent.
    *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
    * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
    *
    * @return $this|ChildSCategoryQuery The current query, for fluid interface
    */
    public function filterByEntityId($value = null, $comparison = null)
    {
        return $this->useRouteQuery()->filterByEntityId($value, $comparison)->endUse();
    }

    /**
    * Adds an ORDER BY clause to the query
    * Usability layer on top of Criteria::addAscendingOrderByColumn() and Criteria::addDescendingOrderByColumn()
    * Infers $column and $order from $columnName and some optional arguments
    * Examples:
    *   $c->orderBy('Book.CreatedAt')
    *    => $c->addAscendingOrderByColumn(BookTableMap::CREATED_AT)
    *   $c->orderBy('Book.CategoryId', 'desc')
    *    => $c->addDescendingOrderByColumn(BookTableMap::CATEGORY_ID)
    *
    * @param string $order      The sorting order. Criteria::ASC by default, also accepts Criteria::DESC
    *
    * @return $this|ModelCriteria The current object, for fluid interface
    */
    public function orderByEntityId($order = Criteria::ASC)
    {
        return $this->useRouteQuery()->orderByEntityId($order)->endUse();
    }
    /**
    * Filter the query by type column
    *
    * Example usage:
    * <code>
        * $query->filterByType(1234); // WHERE type = 1234
        * $query->filterByType(array(12, 34)); // WHERE type IN (12, 34)
        * $query->filterByType(array('min' => 12)); // WHERE type >= 12
        * </code>
    *
    * @param     mixed $value The value to use as filter.
    *              Use scalar values for equality.
    *              Use array values for in_array() equivalent.
    *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
    * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
    *
    * @return $this|ChildSCategoryQuery The current query, for fluid interface
    */
    public function filterByType($value = null, $comparison = null)
    {
        return $this->useRouteQuery()->filterByType($value, $comparison)->endUse();
    }

    /**
    * Adds an ORDER BY clause to the query
    * Usability layer on top of Criteria::addAscendingOrderByColumn() and Criteria::addDescendingOrderByColumn()
    * Infers $column and $order from $columnName and some optional arguments
    * Examples:
    *   $c->orderBy('Book.CreatedAt')
    *    => $c->addAscendingOrderByColumn(BookTableMap::CREATED_AT)
    *   $c->orderBy('Book.CategoryId', 'desc')
    *    => $c->addDescendingOrderByColumn(BookTableMap::CATEGORY_ID)
    *
    * @param string $order      The sorting order. Criteria::ASC by default, also accepts Criteria::DESC
    *
    * @return $this|ModelCriteria The current object, for fluid interface
    */
    public function orderByType($order = Criteria::ASC)
    {
        return $this->useRouteQuery()->orderByType($order)->endUse();
    }
    /**
    * Filter the query by parent_url column
    *
    * Example usage:
    * <code>
        * $query->filterByParentUrl(1234); // WHERE parent_url = 1234
        * $query->filterByParentUrl(array(12, 34)); // WHERE parent_url IN (12, 34)
        * $query->filterByParentUrl(array('min' => 12)); // WHERE parent_url >= 12
        * </code>
    *
    * @param     mixed $value The value to use as filter.
    *              Use scalar values for equality.
    *              Use array values for in_array() equivalent.
    *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
    * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
    *
    * @return $this|ChildSCategoryQuery The current query, for fluid interface
    */
    public function filterByParentUrl($value = null, $comparison = null)
    {
        return $this->useRouteQuery()->filterByParentUrl($value, $comparison)->endUse();
    }

    /**
    * Adds an ORDER BY clause to the query
    * Usability layer on top of Criteria::addAscendingOrderByColumn() and Criteria::addDescendingOrderByColumn()
    * Infers $column and $order from $columnName and some optional arguments
    * Examples:
    *   $c->orderBy('Book.CreatedAt')
    *    => $c->addAscendingOrderByColumn(BookTableMap::CREATED_AT)
    *   $c->orderBy('Book.CategoryId', 'desc')
    *    => $c->addDescendingOrderByColumn(BookTableMap::CATEGORY_ID)
    *
    * @param string $order      The sorting order. Criteria::ASC by default, also accepts Criteria::DESC
    *
    * @return $this|ModelCriteria The current object, for fluid interface
    */
    public function orderByParentUrl($order = Criteria::ASC)
    {
        return $this->useRouteQuery()->orderByParentUrl($order)->endUse();
    }
    /**
    * Filter the query by url column
    *
    * Example usage:
    * <code>
        * $query->filterByUrl(1234); // WHERE url = 1234
        * $query->filterByUrl(array(12, 34)); // WHERE url IN (12, 34)
        * $query->filterByUrl(array('min' => 12)); // WHERE url >= 12
        * </code>
    *
    * @param     mixed $value The value to use as filter.
    *              Use scalar values for equality.
    *              Use array values for in_array() equivalent.
    *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
    * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
    *
    * @return $this|ChildSCategoryQuery The current query, for fluid interface
    */
    public function filterByUrl($value = null, $comparison = null)
    {
        return $this->useRouteQuery()->filterByUrl($value, $comparison)->endUse();
    }

    /**
    * Adds an ORDER BY clause to the query
    * Usability layer on top of Criteria::addAscendingOrderByColumn() and Criteria::addDescendingOrderByColumn()
    * Infers $column and $order from $columnName and some optional arguments
    * Examples:
    *   $c->orderBy('Book.CreatedAt')
    *    => $c->addAscendingOrderByColumn(BookTableMap::CREATED_AT)
    *   $c->orderBy('Book.CategoryId', 'desc')
    *    => $c->addDescendingOrderByColumn(BookTableMap::CATEGORY_ID)
    *
    * @param string $order      The sorting order. Criteria::ASC by default, also accepts Criteria::DESC
    *
    * @return $this|ModelCriteria The current object, for fluid interface
    */
    public function orderByUrl($order = Criteria::ASC)
    {
        return $this->useRouteQuery()->orderByUrl($order)->endUse();
    }

    /**
     * Adds a condition on a column based on a column phpName and a value
     * Uses introspection to translate the column phpName into a fully qualified name
     * Warning: recognizes only the phpNames of the main Model (not joined tables)
     * <code>
     * $c->filterBy('Title', 'foo');
     * </code>
     *
     * @see Criteria::add()
     *
     * @param string $column     A string representing thecolumn phpName, e.g. 'AuthorId'
     * @param mixed  $value      A value for the condition
     * @param string $comparison What to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ModelCriteria The current object, for fluid interface
     */
    public function filterBy($column, $value, $comparison = Criteria::EQUAL)
    {
        if (isset($this->delegatedFields[$column])) {
            $methodUse = "use{$this->delegatedFields[$column]}Query";

            return $this->{$methodUse}()->filterBy($column, $value, $comparison)->endUse();
        } else {
            return $this->add($this->getRealColumnName($column), $value, $comparison);
        }
    }

} // SCategoryQuery
