<?php

namespace smart_filter\models\Map;

use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\InstancePoolTrait;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\DataFetcher\DataFetcherInterface;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\RelationMap;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Map\TableMapTrait;
use smart_filter\models\SFilterPattern;
use smart_filter\models\SFilterPatternQuery;


/**
 * This class defines the structure of the 'smart_filter_patterns' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 */
class SFilterPatternTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'smart_filter.models.Map.SFilterPatternTableMap';

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'Shop';

    /**
     * The table name for this class
     */
    const TABLE_NAME = 'smart_filter_patterns';

    /**
     * The related Propel class for this table
     */
    const OM_CLASS = '\\smart_filter\\models\\SFilterPattern';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'smart_filter.models.SFilterPattern';

    /**
     * The total number of columns
     */
    const NUM_COLUMNS = 9;

    /**
     * The number of lazy-loaded columns
     */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /**
     * The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS)
     */
    const NUM_HYDRATE_COLUMNS = 9;

    /**
     * the column name for the id field
     */
    const COL_ID = 'smart_filter_patterns.id';

    /**
     * the column name for the category_id field
     */
    const COL_CATEGORY_ID = 'smart_filter_patterns.category_id';

    /**
     * the column name for the active field
     */
    const COL_ACTIVE = 'smart_filter_patterns.active';

    /**
     * the column name for the url_pattern field
     */
    const COL_URL_PATTERN = 'smart_filter_patterns.url_pattern';

    /**
     * the column name for the data field
     */
    const COL_DATA = 'smart_filter_patterns.data';

    /**
     * the column name for the meta_index field
     */
    const COL_META_INDEX = 'smart_filter_patterns.meta_index';

    /**
     * the column name for the meta_follow field
     */
    const COL_META_FOLLOW = 'smart_filter_patterns.meta_follow';

    /**
     * the column name for the created field
     */
    const COL_CREATED = 'smart_filter_patterns.created';

    /**
     * the column name for the updated field
     */
    const COL_UPDATED = 'smart_filter_patterns.updated';

    /**
     * The default string format for model objects of the related table
     */
    const DEFAULT_STRING_FORMAT = 'YAML';

    /** The enumerated values for the meta_index field */
    const COL_META_INDEX_INDEX = 'index';
    const COL_META_INDEX_NOINDEX = 'noindex';

    /** The enumerated values for the meta_follow field */
    const COL_META_FOLLOW_FOLLOW = 'follow';
    const COL_META_FOLLOW_NOFOLLOW = 'nofollow';

    // i18n behavior

    /**
     * The default locale to use for translations.
     *
     * @var string
     */
    const DEFAULT_LOCALE = 'ru';

    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
     */
    protected static $fieldNames = array (
        self::TYPE_PHPNAME       => array('Id', 'CategoryId', 'Active', 'UrlPattern', 'Data', 'MetaIndex', 'MetaFollow', 'Created', 'Updated', ),
        self::TYPE_CAMELNAME     => array('id', 'categoryId', 'active', 'urlPattern', 'data', 'metaIndex', 'metaFollow', 'created', 'updated', ),
        self::TYPE_COLNAME       => array(SFilterPatternTableMap::COL_ID, SFilterPatternTableMap::COL_CATEGORY_ID, SFilterPatternTableMap::COL_ACTIVE, SFilterPatternTableMap::COL_URL_PATTERN, SFilterPatternTableMap::COL_DATA, SFilterPatternTableMap::COL_META_INDEX, SFilterPatternTableMap::COL_META_FOLLOW, SFilterPatternTableMap::COL_CREATED, SFilterPatternTableMap::COL_UPDATED, ),
        self::TYPE_FIELDNAME     => array('id', 'category_id', 'active', 'url_pattern', 'data', 'meta_index', 'meta_follow', 'created', 'updated', ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        self::TYPE_PHPNAME       => array('Id' => 0, 'CategoryId' => 1, 'Active' => 2, 'UrlPattern' => 3, 'Data' => 4, 'MetaIndex' => 5, 'MetaFollow' => 6, 'Created' => 7, 'Updated' => 8, ),
        self::TYPE_CAMELNAME     => array('id' => 0, 'categoryId' => 1, 'active' => 2, 'urlPattern' => 3, 'data' => 4, 'metaIndex' => 5, 'metaFollow' => 6, 'created' => 7, 'updated' => 8, ),
        self::TYPE_COLNAME       => array(SFilterPatternTableMap::COL_ID => 0, SFilterPatternTableMap::COL_CATEGORY_ID => 1, SFilterPatternTableMap::COL_ACTIVE => 2, SFilterPatternTableMap::COL_URL_PATTERN => 3, SFilterPatternTableMap::COL_DATA => 4, SFilterPatternTableMap::COL_META_INDEX => 5, SFilterPatternTableMap::COL_META_FOLLOW => 6, SFilterPatternTableMap::COL_CREATED => 7, SFilterPatternTableMap::COL_UPDATED => 8, ),
        self::TYPE_FIELDNAME     => array('id' => 0, 'category_id' => 1, 'active' => 2, 'url_pattern' => 3, 'data' => 4, 'meta_index' => 5, 'meta_follow' => 6, 'created' => 7, 'updated' => 8, ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, )
    );

    /** The enumerated values for this table */
    protected static $enumValueSets = array(
                SFilterPatternTableMap::COL_META_INDEX => array(
                            self::COL_META_INDEX_INDEX,
            self::COL_META_INDEX_NOINDEX,
        ),
                SFilterPatternTableMap::COL_META_FOLLOW => array(
                            self::COL_META_FOLLOW_FOLLOW,
            self::COL_META_FOLLOW_NOFOLLOW,
        ),
    );

    /**
     * Gets the list of values for all ENUM and SET columns
     * @return array
     */
    public static function getValueSets()
    {
      return static::$enumValueSets;
    }

    /**
     * Gets the list of values for an ENUM or SET column
     * @param string $colname
     * @return array list of possible values for the column
     */
    public static function getValueSet($colname)
    {
        $valueSets = self::getValueSets();

        return $valueSets[$colname];
    }

    /**
     * Initialize the table attributes and columns
     * Relations are not initialized by this method since they are lazy loaded
     *
     * @return void
     * @throws PropelException
     */
    public function initialize()
    {
        // attributes
        $this->setName('smart_filter_patterns');
        $this->setPhpName('SFilterPattern');
        $this->setIdentifierQuoting(false);
        $this->setClassName('\\smart_filter\\models\\SFilterPattern');
        $this->setPackage('smart_filter.models');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, 11, null);
        $this->addForeignKey('category_id', 'CategoryId', 'INTEGER', 'shop_category', 'id', true, 11, null);
        $this->addColumn('active', 'Active', 'BOOLEAN', false, 1, null);
        $this->addColumn('url_pattern', 'UrlPattern', 'VARCHAR', false, 255, null);
        $this->addColumn('data', 'Data', 'VARCHAR', false, 255, null);
        $this->addColumn('meta_index', 'MetaIndex', 'ENUM', false, null, 'null');
        $this->getColumn('meta_index')->setValueSet(array (
  0 => 'index',
  1 => 'noindex',
));
        $this->addColumn('meta_follow', 'MetaFollow', 'ENUM', false, null, 'null');
        $this->getColumn('meta_follow')->setValueSet(array (
  0 => 'follow',
  1 => 'nofollow',
));
        $this->addColumn('created', 'Created', 'INTEGER', false, 11, null);
        $this->addColumn('updated', 'Updated', 'INTEGER', false, 11, null);
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Category', '\\SCategory', RelationMap::MANY_TO_ONE, array (
  0 =>
  array (
    0 => ':category_id',
    1 => ':id',
  ),
), 'CASCADE', 'CASCADE', null, false);
        $this->addRelation('SFilterPatternI18n', '\\smart_filter\\models\\SFilterPatternI18n', RelationMap::ONE_TO_MANY, array (
  0 =>
  array (
    0 => ':id',
    1 => ':id',
  ),
), 'CASCADE', null, 'SFilterPatternI18ns', false);
    } // buildRelations()

    /**
     *
     * Gets the list of behaviors registered for this table
     *
     * @return array Associative array (name => parameters) of behaviors
     */
    public function getBehaviors()
    {
        return array(
            'i18n' => array('i18n_table' => '%TABLE%_i18n', 'i18n_phpname' => '%PHPNAME%I18n', 'i18n_columns' => 'h1, meta_title, meta_description, meta_keywords, seo_text, name', 'i18n_pk_column' => '', 'locale_column' => 'locale', 'locale_length' => '5', 'default_locale' => 'ru', 'locale_alias' => '', ),
            'timestampable' => array('create_column' => 'created', 'update_column' => 'updated', 'disable_created_at' => 'false', 'disable_updated_at' => 'false', ),
        );
    } // getBehaviors()
    /**
     * Method to invalidate the instance pool of all tables related to smart_filter_patterns     * by a foreign key with ON DELETE CASCADE
     */
    public static function clearRelatedInstancePool()
    {
        // Invalidate objects in related instance pools,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
        SFilterPatternI18nTableMap::clearInstancePool();
    }

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param array  $row       resultset row.
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM
     *
     * @return string The primary key hash of the row
     */
    public static function getPrimaryKeyHashFromRow($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        // If the PK cannot be derived from the row, return NULL.
        if ($row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)] === null) {
            return null;
        }

        return null === $row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)] || is_scalar($row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)]) || is_callable([$row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)], '__toString']) ? (string) $row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)] : $row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
    }

    /**
     * Retrieves the primary key from the DB resultset row
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, an array of the primary key columns will be returned.
     *
     * @param array  $row       resultset row.
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM
     *
     * @return mixed The primary key of the row
     */
    public static function getPrimaryKeyFromRow($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        return (int) $row[
            $indexType == TableMap::TYPE_NUM
                ? 0 + $offset
                : self::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)
        ];
    }

    /**
     * The class that the tableMap will make instances of.
     *
     * If $withPrefix is true, the returned path
     * uses a dot-path notation which is translated into a path
     * relative to a location on the PHP include_path.
     * (e.g. path.to.MyClass -> 'path/to/MyClass.php')
     *
     * @param boolean $withPrefix Whether or not to return the path with the class name
     * @return string path.to.ClassName
     */
    public static function getOMClass($withPrefix = true)
    {
        return $withPrefix ? SFilterPatternTableMap::CLASS_DEFAULT : SFilterPatternTableMap::OM_CLASS;
    }

    /**
     * Populates an object of the default type or an object that inherit from the default.
     *
     * @param array  $row       row returned by DataFetcher->fetch().
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType The index type of $row. Mostly DataFetcher->getIndexType().
                                 One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     * @return array           (SFilterPattern object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = SFilterPatternTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = SFilterPatternTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + SFilterPatternTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = SFilterPatternTableMap::OM_CLASS;
            /** @var SFilterPattern $obj */
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            SFilterPatternTableMap::addInstanceToPool($obj, $key);
        }

        return array($obj, $col);
    }

    /**
     * The returned array will contain objects of the default type or
     * objects that inherit from the default.
     *
     * @param DataFetcherInterface $dataFetcher
     * @return array
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function populateObjects(DataFetcherInterface $dataFetcher)
    {
        $results = array();

        // set the class once to avoid overhead in the loop
        $cls = static::getOMClass(false);
        // populate the object(s)
        while ($row = $dataFetcher->fetch()) {
            $key = SFilterPatternTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = SFilterPatternTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                /** @var SFilterPattern $obj */
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                SFilterPatternTableMap::addInstanceToPool($obj, $key);
            } // if key exists
        }

        return $results;
    }
    /**
     * Add all the columns needed to create a new object.
     *
     * Note: any columns that were marked with lazyLoad="true" in the
     * XML schema will not be added to the select list and only loaded
     * on demand.
     *
     * @param Criteria $criteria object containing the columns to add.
     * @param string   $alias    optional table alias
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function addSelectColumns(Criteria $criteria, $alias = null)
    {
        if (null === $alias) {
            $criteria->addSelectColumn(SFilterPatternTableMap::COL_ID);
            $criteria->addSelectColumn(SFilterPatternTableMap::COL_CATEGORY_ID);
            $criteria->addSelectColumn(SFilterPatternTableMap::COL_ACTIVE);
            $criteria->addSelectColumn(SFilterPatternTableMap::COL_URL_PATTERN);
            $criteria->addSelectColumn(SFilterPatternTableMap::COL_DATA);
            $criteria->addSelectColumn(SFilterPatternTableMap::COL_META_INDEX);
            $criteria->addSelectColumn(SFilterPatternTableMap::COL_META_FOLLOW);
            $criteria->addSelectColumn(SFilterPatternTableMap::COL_CREATED);
            $criteria->addSelectColumn(SFilterPatternTableMap::COL_UPDATED);
        } else {
            $criteria->addSelectColumn($alias . '.id');
            $criteria->addSelectColumn($alias . '.category_id');
            $criteria->addSelectColumn($alias . '.active');
            $criteria->addSelectColumn($alias . '.url_pattern');
            $criteria->addSelectColumn($alias . '.data');
            $criteria->addSelectColumn($alias . '.meta_index');
            $criteria->addSelectColumn($alias . '.meta_follow');
            $criteria->addSelectColumn($alias . '.created');
            $criteria->addSelectColumn($alias . '.updated');
        }
    }

    /**
     * Returns the TableMap related to this object.
     * This method is not needed for general use but a specific application could have a need.
     * @return TableMap
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function getTableMap()
    {
        return Propel::getServiceContainer()->getDatabaseMap(SFilterPatternTableMap::DATABASE_NAME)->getTable(SFilterPatternTableMap::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
        $dbMap = Propel::getServiceContainer()->getDatabaseMap(SFilterPatternTableMap::DATABASE_NAME);
        if (!$dbMap->hasTable(SFilterPatternTableMap::TABLE_NAME)) {
            $dbMap->addTableObject(new SFilterPatternTableMap());
        }
    }

    /**
     * Performs a DELETE on the database, given a SFilterPattern or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or SFilterPattern object or primary key or array of primary keys
     *              which is used to create the DELETE statement
     * @param  ConnectionInterface $con the connection to use
     * @return int             The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                         if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
     public static function doDelete($values, ConnectionInterface $con = null)
     {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(SFilterPatternTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \smart_filter\models\SFilterPattern) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(SFilterPatternTableMap::DATABASE_NAME);
            $criteria->add(SFilterPatternTableMap::COL_ID, (array) $values, Criteria::IN);
        }

        $query = SFilterPatternQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) {
            SFilterPatternTableMap::clearInstancePool();
        } elseif (!is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) {
                SFilterPatternTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the smart_filter_patterns table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return SFilterPatternQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a SFilterPattern or Criteria object.
     *
     * @param mixed               $criteria Criteria or SFilterPattern object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(SFilterPatternTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from SFilterPattern object
        }

        if ($criteria->containsKey(SFilterPatternTableMap::COL_ID) && $criteria->keyContainsValue(SFilterPatternTableMap::COL_ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.SFilterPatternTableMap::COL_ID.')');
        }


        // Set the correct dbName
        $query = SFilterPatternQuery::create()->mergeWith($criteria);

        // use transaction because $criteria could contain info
        // for more than one table (I guess, conceivably)
        return $con->transaction(function () use ($con, $query) {
            return $query->doInsert($con);
        });
    }

} // SFilterPatternTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
SFilterPatternTableMap::buildTableMap();
