<?php

namespace smart_filter\models\Base;

use \Exception;
use \PDO;
use \SCategory;
use \SCategoryQuery;
use CMSFactory\PropelBaseModelClass;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveRecord\ActiveRecordInterface;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\BadMethodCallException;
use Propel\Runtime\Exception\LogicException;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Parser\AbstractParser;
use smart_filter\models\SFilterPattern as ChildSFilterPattern;
use smart_filter\models\SFilterPatternI18n as ChildSFilterPatternI18n;
use smart_filter\models\SFilterPatternI18nQuery as ChildSFilterPatternI18nQuery;
use smart_filter\models\SFilterPatternQuery as ChildSFilterPatternQuery;
use smart_filter\models\Map\SFilterPatternI18nTableMap;
use smart_filter\models\Map\SFilterPatternTableMap;

/**
 * Base class that represents a row from the 'smart_filter_patterns' table.
 *
 *
 *
 * @package    propel.generator.smart_filter.models.Base
 */
abstract class SFilterPattern extends PropelBaseModelClass implements ActiveRecordInterface
{
    /**
     * TableMap class name
     */
    const TABLE_MAP = '\\smart_filter\\models\\Map\\SFilterPatternTableMap';


    /**
     * attribute to determine if this object has previously been saved.
     * @var boolean
     */
    protected $new = true;

    /**
     * attribute to determine whether this object has been deleted.
     * @var boolean
     */
    protected $deleted = false;

    /**
     * The columns that have been modified in current object.
     * Tracking modified columns allows us to only update modified columns.
     * @var array
     */
    protected $modifiedColumns = array();

    /**
     * The (virtual) columns that are added at runtime
     * The formatters can add supplementary columns based on a resultset
     * @var array
     */
    protected $virtualColumns = array();

    /**
     * The value for the id field.
     *
     * @var        int
     */
    protected $id;

    /**
     * The value for the category_id field.
     *
     * @var        int
     */
    protected $category_id;

    /**
     * The value for the active field.
     *
     * @var        boolean
     */
    protected $active;

    /**
     * The value for the url_pattern field.
     *
     * @var        string
     */
    protected $url_pattern;

    /**
     * The value for the data field.
     *
     * @var        string
     */
    protected $data;

    /**
     * The value for the meta_index field.
     *
     * Note: this column has a database default value of: (expression) null
     * @var        int
     */
    protected $meta_index;

    /**
     * The value for the meta_follow field.
     *
     * Note: this column has a database default value of: (expression) null
     * @var        int
     */
    protected $meta_follow;

    /**
     * The value for the created field.
     *
     * @var        int
     */
    protected $created;

    /**
     * The value for the updated field.
     *
     * @var        int
     */
    protected $updated;

    /**
     * @var        SCategory
     */
    protected $aCategory;

    /**
     * @var        ObjectCollection|ChildSFilterPatternI18n[] Collection to store aggregation of ChildSFilterPatternI18n objects.
     */
    protected $collSFilterPatternI18ns;
    protected $collSFilterPatternI18nsPartial;

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     *
     * @var boolean
     */
    protected $alreadyInSave = false;

    // i18n behavior

    /**
     * Current locale
     * @var        string
     */
    protected $currentLocale = 'ru';

    /**
     * Current translation objects
     * @var        array[ChildSFilterPatternI18n]
     */
    protected $currentTranslations;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildSFilterPatternI18n[]
     */
    protected $sFilterPatternI18nsScheduledForDeletion = null;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see __construct()
     */
    public function applyDefaultValues()
    {
    }

    /**
     * Initializes internal state of smart_filter\models\Base\SFilterPattern object.
     * @see applyDefaults()
     */
    public function __construct()
    {
        $this->applyDefaultValues();
    }

    /**
     * Returns whether the object has been modified.
     *
     * @return boolean True if the object has been modified.
     */
    public function isModified()
    {
        return !!$this->modifiedColumns;
    }

    /**
     * Has specified column been modified?
     *
     * @param  string  $col column fully qualified name (TableMap::TYPE_COLNAME), e.g. Book::AUTHOR_ID
     * @return boolean True if $col has been modified.
     */
    public function isColumnModified($col)
    {
        return $this->modifiedColumns && isset($this->modifiedColumns[$col]);
    }

    /**
     * Get the columns that have been modified in this object.
     * @return array A unique list of the modified column names for this object.
     */
    public function getModifiedColumns()
    {
        return $this->modifiedColumns ? array_keys($this->modifiedColumns) : [];
    }

    /**
     * Returns whether the object has ever been saved.  This will
     * be false, if the object was retrieved from storage or was created
     * and then saved.
     *
     * @return boolean true, if the object has never been persisted.
     */
    public function isNew()
    {
        return $this->new;
    }

    /**
     * Setter for the isNew attribute.  This method will be called
     * by Propel-generated children and objects.
     *
     * @param boolean $b the state of the object.
     */
    public function setNew($b)
    {
        $this->new = (boolean) $b;
    }

    /**
     * Whether this object has been deleted.
     * @return boolean The deleted state of this object.
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * Specify whether this object has been deleted.
     * @param  boolean $b The deleted state of this object.
     * @return void
     */
    public function setDeleted($b)
    {
        $this->deleted = (boolean) $b;
    }

    /**
     * Sets the modified state for the object to be false.
     * @param  string $col If supplied, only the specified column is reset.
     * @return void
     */
    public function resetModified($col = null)
    {
        if (null !== $col) {
            if (isset($this->modifiedColumns[$col])) {
                unset($this->modifiedColumns[$col]);
            }
        } else {
            $this->modifiedColumns = array();
        }
    }

    /**
     * Compares this with another <code>SFilterPattern</code> instance.  If
     * <code>obj</code> is an instance of <code>SFilterPattern</code>, delegates to
     * <code>equals(SFilterPattern)</code>.  Otherwise, returns <code>false</code>.
     *
     * @param  mixed   $obj The object to compare to.
     * @return boolean Whether equal to the object specified.
     */
    public function equals($obj)
    {
        if (!$obj instanceof static) {
            return false;
        }

        if ($this === $obj) {
            return true;
        }

        if (null === $this->getPrimaryKey() || null === $obj->getPrimaryKey()) {
            return false;
        }

        return $this->getPrimaryKey() === $obj->getPrimaryKey();
    }

    /**
     * Get the associative array of the virtual columns in this object
     *
     * @return array
     */
    public function getVirtualColumns()
    {
        return $this->virtualColumns;
    }

    /**
     * Checks the existence of a virtual column in this object
     *
     * @param  string  $name The virtual column name
     * @return boolean
     */
    public function hasVirtualColumn($name)
    {
        return array_key_exists($name, $this->virtualColumns);
    }

    /**
     * Get the value of a virtual column in this object
     *
     * @param  string $name The virtual column name
     * @return mixed
     *
     * @throws PropelException
     */
    public function getVirtualColumn($name)
    {
        if (!$this->hasVirtualColumn($name)) {
            throw new PropelException(sprintf('Cannot get value of inexistent virtual column %s.', $name));
        }

        return $this->virtualColumns[$name];
    }

    /**
     * Set the value of a virtual column in this object
     *
     * @param string $name  The virtual column name
     * @param mixed  $value The value to give to the virtual column
     *
     * @return $this|SFilterPattern The current object, for fluid interface
     */
    public function setVirtualColumn($name, $value)
    {
        $this->virtualColumns[$name] = $value;

        return $this;
    }

    /**
     * Logs a message using Propel::log().
     *
     * @param  string  $msg
     * @param  int     $priority One of the Propel::LOG_* logging levels
     * @return boolean
     */
    protected function log($msg, $priority = Propel::LOG_INFO)
    {
        return Propel::log(get_class($this) . ': ' . $msg, $priority);
    }

    /**
     * Export the current object properties to a string, using a given parser format
     * <code>
     * $book = BookQuery::create()->findPk(9012);
     * echo $book->exportTo('JSON');
     *  => {"Id":9012,"Title":"Don Juan","ISBN":"0140422161","Price":12.99,"PublisherId":1234,"AuthorId":5678}');
     * </code>
     *
     * @param  mixed   $parser                 A AbstractParser instance, or a format name ('XML', 'YAML', 'JSON', 'CSV')
     * @param  boolean $includeLazyLoadColumns (optional) Whether to include lazy load(ed) columns. Defaults to TRUE.
     * @return string  The exported data
     */
    public function exportTo($parser, $includeLazyLoadColumns = true)
    {
        if (!$parser instanceof AbstractParser) {
            $parser = AbstractParser::getParser($parser);
        }

        return $parser->fromArray($this->toArray(TableMap::TYPE_PHPNAME, $includeLazyLoadColumns, array(), true));
    }

    /**
     * Clean up internal collections prior to serializing
     * Avoids recursive loops that turn into segmentation faults when serializing
     */
    public function __sleep()
    {
        $this->clearAllReferences();

        $cls = new \ReflectionClass($this);
        $propertyNames = [];
        $serializableProperties = array_diff($cls->getProperties(), $cls->getProperties(\ReflectionProperty::IS_STATIC));

        foreach($serializableProperties as $property) {
            $propertyNames[] = $property->getName();
        }

        return $propertyNames;
    }

    /**
     * Get the [id] column value.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the [category_id] column value.
     *
     * @return int
     */
    public function getCategoryId()
    {
        return $this->category_id;
    }

    /**
     * Get the [active] column value.
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Get the [active] column value.
     *
     * @return boolean
     */
    public function isActive()
    {
        return $this->getActive();
    }

    /**
     * Get the [url_pattern] column value.
     *
     * @return string
     */
    public function getUrlPattern()
    {
        return $this->url_pattern;
    }

    /**
     * Get the [data] column value.
     *
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Get the [meta_index] column value.
     *
     * @return string
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function getMetaIndex()
    {
        if (null === $this->meta_index) {
            return null;
        }
        $valueSet = SFilterPatternTableMap::getValueSet(SFilterPatternTableMap::COL_META_INDEX);
        if (!isset($valueSet[$this->meta_index])) {
            throw new PropelException('Unknown stored enum key: ' . $this->meta_index);
        }

        return $valueSet[$this->meta_index];
    }

    /**
     * Get the [meta_follow] column value.
     *
     * @return string
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function getMetaFollow()
    {
        if (null === $this->meta_follow) {
            return null;
        }
        $valueSet = SFilterPatternTableMap::getValueSet(SFilterPatternTableMap::COL_META_FOLLOW);
        if (!isset($valueSet[$this->meta_follow])) {
            throw new PropelException('Unknown stored enum key: ' . $this->meta_follow);
        }

        return $valueSet[$this->meta_follow];
    }

    /**
     * Get the [created] column value.
     *
     * @return int
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Get the [updated] column value.
     *
     * @return int
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set the value of [id] column.
     *
     * @param int $v new value
     * @return $this|\smart_filter\models\SFilterPattern The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[SFilterPatternTableMap::COL_ID] = true;
        }

        return $this;
    } // setId()

    /**
     * Set the value of [category_id] column.
     *
     * @param int $v new value
     * @return $this|\smart_filter\models\SFilterPattern The current object (for fluent API support)
     */
    public function setCategoryId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->category_id !== $v) {
            $this->category_id = $v;
            $this->modifiedColumns[SFilterPatternTableMap::COL_CATEGORY_ID] = true;
        }

        if ($this->aCategory !== null && $this->aCategory->getId() !== $v) {
            $this->aCategory = null;
        }

        return $this;
    } // setCategoryId()

    /**
     * Sets the value of the [active] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param  boolean|integer|string $v The new value
     * @return $this|\smart_filter\models\SFilterPattern The current object (for fluent API support)
     */
    public function setActive($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->active !== $v) {
            $this->active = $v;
            $this->modifiedColumns[SFilterPatternTableMap::COL_ACTIVE] = true;
        }

        return $this;
    } // setActive()

    /**
     * Set the value of [url_pattern] column.
     *
     * @param string $v new value
     * @return $this|\smart_filter\models\SFilterPattern The current object (for fluent API support)
     */
    public function setUrlPattern($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->url_pattern !== $v) {
            $this->url_pattern = $v;
            $this->modifiedColumns[SFilterPatternTableMap::COL_URL_PATTERN] = true;
        }

        return $this;
    } // setUrlPattern()

    /**
     * Set the value of [data] column.
     *
     * @param string $v new value
     * @return $this|\smart_filter\models\SFilterPattern The current object (for fluent API support)
     */
    public function setData($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->data !== $v) {
            $this->data = $v;
            $this->modifiedColumns[SFilterPatternTableMap::COL_DATA] = true;
        }

        return $this;
    } // setData()

    /**
     * Set the value of [meta_index] column.
     *
     * @param  string $v new value
     * @return $this|\smart_filter\models\SFilterPattern The current object (for fluent API support)
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function setMetaIndex($v)
    {
        if ($v !== null) {
            $valueSet = SFilterPatternTableMap::getValueSet(SFilterPatternTableMap::COL_META_INDEX);
            if (!in_array($v, $valueSet)) {
                throw new PropelException(sprintf('Value "%s" is not accepted in this enumerated column', $v));
            }
            $v = array_search($v, $valueSet);
        }

        if ($this->meta_index !== $v) {
            $this->meta_index = $v;
            $this->modifiedColumns[SFilterPatternTableMap::COL_META_INDEX] = true;
        }

        return $this;
    } // setMetaIndex()

    /**
     * Set the value of [meta_follow] column.
     *
     * @param  string $v new value
     * @return $this|\smart_filter\models\SFilterPattern The current object (for fluent API support)
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function setMetaFollow($v)
    {
        if ($v !== null) {
            $valueSet = SFilterPatternTableMap::getValueSet(SFilterPatternTableMap::COL_META_FOLLOW);
            if (!in_array($v, $valueSet)) {
                throw new PropelException(sprintf('Value "%s" is not accepted in this enumerated column', $v));
            }
            $v = array_search($v, $valueSet);
        }

        if ($this->meta_follow !== $v) {
            $this->meta_follow = $v;
            $this->modifiedColumns[SFilterPatternTableMap::COL_META_FOLLOW] = true;
        }

        return $this;
    } // setMetaFollow()

    /**
     * Set the value of [created] column.
     *
     * @param int $v new value
     * @return $this|\smart_filter\models\SFilterPattern The current object (for fluent API support)
     */
    public function setCreated($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->created !== $v) {
            $this->created = $v;
            $this->modifiedColumns[SFilterPatternTableMap::COL_CREATED] = true;
        }

        return $this;
    } // setCreated()

    /**
     * Set the value of [updated] column.
     *
     * @param int $v new value
     * @return $this|\smart_filter\models\SFilterPattern The current object (for fluent API support)
     */
    public function setUpdated($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->updated !== $v) {
            $this->updated = $v;
            $this->modifiedColumns[SFilterPatternTableMap::COL_UPDATED] = true;
        }

        return $this;
    } // setUpdated()

    /**
     * Indicates whether the columns in this object are only set to default values.
     *
     * This method can be used in conjunction with isModified() to indicate whether an object is both
     * modified _and_ has some values set which are non-default.
     *
     * @return boolean Whether the columns in this object are only been set with default values.
     */
    public function hasOnlyDefaultValues()
    {
        // otherwise, everything was equal, so return TRUE
        return true;
    } // hasOnlyDefaultValues()

    /**
     * Hydrates (populates) the object variables with values from the database resultset.
     *
     * An offset (0-based "start column") is specified so that objects can be hydrated
     * with a subset of the columns in the resultset rows.  This is needed, for example,
     * for results of JOIN queries where the resultset row includes columns from two or
     * more tables.
     *
     * @param array   $row       The row returned by DataFetcher->fetch().
     * @param int     $startcol  0-based offset column which indicates which restultset column to start with.
     * @param boolean $rehydrate Whether this object is being re-hydrated from the database.
     * @param string  $indexType The index type of $row. Mostly DataFetcher->getIndexType().
                                  One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                            TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *
     * @return int             next starting column
     * @throws PropelException - Any caught Exception will be rewrapped as a PropelException.
     */
    public function hydrate($row, $startcol = 0, $rehydrate = false, $indexType = TableMap::TYPE_NUM)
    {
        try {

            $col = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : SFilterPatternTableMap::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : SFilterPatternTableMap::translateFieldName('CategoryId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->category_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : SFilterPatternTableMap::translateFieldName('Active', TableMap::TYPE_PHPNAME, $indexType)];
            $this->active = (null !== $col) ? (boolean) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 3 + $startcol : SFilterPatternTableMap::translateFieldName('UrlPattern', TableMap::TYPE_PHPNAME, $indexType)];
            $this->url_pattern = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 4 + $startcol : SFilterPatternTableMap::translateFieldName('Data', TableMap::TYPE_PHPNAME, $indexType)];
            $this->data = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 5 + $startcol : SFilterPatternTableMap::translateFieldName('MetaIndex', TableMap::TYPE_PHPNAME, $indexType)];
            $this->meta_index = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 6 + $startcol : SFilterPatternTableMap::translateFieldName('MetaFollow', TableMap::TYPE_PHPNAME, $indexType)];
            $this->meta_follow = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 7 + $startcol : SFilterPatternTableMap::translateFieldName('Created', TableMap::TYPE_PHPNAME, $indexType)];
            $this->created = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 8 + $startcol : SFilterPatternTableMap::translateFieldName('Updated', TableMap::TYPE_PHPNAME, $indexType)];
            $this->updated = (null !== $col) ? (int) $col : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 9; // 9 = SFilterPatternTableMap::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException(sprintf('Error populating %s object', '\\smart_filter\\models\\SFilterPattern'), 0, $e);
        }
    }

    /**
     * Checks and repairs the internal consistency of the object.
     *
     * This method is executed after an already-instantiated object is re-hydrated
     * from the database.  It exists to check any foreign keys to make sure that
     * the objects related to the current object are correct based on foreign key.
     *
     * You can override this method in the stub class, but you should always invoke
     * the base method from the overridden method (i.e. parent::ensureConsistency()),
     * in case your model changes.
     *
     * @throws PropelException
     */
    public function ensureConsistency()
    {
        if ($this->aCategory !== null && $this->category_id !== $this->aCategory->getId()) {
            $this->aCategory = null;
        }
    } // ensureConsistency

    /**
     * Reloads this object from datastore based on primary key and (optionally) resets all associated objects.
     *
     * This will only work if the object has been saved and has a valid primary key set.
     *
     * @param      boolean $deep (optional) Whether to also de-associated any related objects.
     * @param      ConnectionInterface $con (optional) The ConnectionInterface connection to use.
     * @return void
     * @throws PropelException - if this object is deleted, unsaved or doesn't have pk match in db
     */
    public function reload($deep = false, ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("Cannot reload a deleted object.");
        }

        if ($this->isNew()) {
            throw new PropelException("Cannot reload an unsaved object.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(SFilterPatternTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildSFilterPatternQuery::create(null, $this->buildPkeyCriteria())->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
        $row = $dataFetcher->fetch();
        $dataFetcher->close();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aCategory = null;
            $this->collSFilterPatternI18ns = null;

        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      ConnectionInterface $con
     * @return void
     * @throws PropelException
     * @see SFilterPattern::setDeleted()
     * @see SFilterPattern::isDeleted()
     */
    public function delete(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(SFilterPatternTableMap::DATABASE_NAME);
        }

        $con->transaction(function () use ($con) {
            $deleteQuery = ChildSFilterPatternQuery::create()
                ->filterByPrimaryKey($this->getPrimaryKey());
            $ret = $this->preDelete($con);
            if ($ret) {
                $deleteQuery->delete($con);
                $this->postDelete($con);
                $this->setDeleted(true);
            }
        });
    }

    /**
     * Persists this object to the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All modified related objects will also be persisted in the doSave()
     * method.  This method wraps all precipitate database operations in a
     * single transaction.
     *
     * @param      ConnectionInterface $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see doSave()
     */
    public function save(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("You cannot save an object that has been deleted.");
        }

        if ($this->alreadyInSave) {
            return 0;
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(SFilterPatternTableMap::DATABASE_NAME);
        }

        return $con->transaction(function () use ($con) {
            $ret = $this->preSave($con);
            $isInsert = $this->isNew();
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
                // timestampable behavior

                if (!$this->isColumnModified(SFilterPatternTableMap::COL_CREATED)) {
                    $this->setCreated(time());
                }
                if (!$this->isColumnModified(SFilterPatternTableMap::COL_UPDATED)) {
                    $this->setUpdated(time());
                }
            } else {
                $ret = $ret && $this->preUpdate($con);
                // timestampable behavior
                if ($this->isModified() && !$this->isColumnModified(SFilterPatternTableMap::COL_UPDATED)) {
                    $this->setUpdated(time());
                }
            }
            if ($ret) {
                $affectedRows = $this->doSave($con);
                if ($isInsert) {
                    $this->postInsert($con);
                } else {
                    $this->postUpdate($con);
                }
                $this->postSave($con);
                SFilterPatternTableMap::addInstanceToPool($this);
            } else {
                $affectedRows = 0;
            }

            return $affectedRows;
        });
    }

    /**
     * Performs the work of inserting or updating the row in the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All related objects are also updated in this method.
     *
     * @param      ConnectionInterface $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see save()
     */
    protected function doSave(ConnectionInterface $con)
    {
        $affectedRows = 0; // initialize var to track total num of affected rows
        if (!$this->alreadyInSave) {
            $this->alreadyInSave = true;

            // We call the save method on the following object(s) if they
            // were passed to this object by their corresponding set
            // method.  This object relates to these object(s) by a
            // foreign key reference.

            if ($this->aCategory !== null) {
                if ($this->aCategory->isModified() || $this->aCategory->isNew()) {
                    $affectedRows += $this->aCategory->save($con);
                }
                $this->setCategory($this->aCategory);
            }

            if ($this->isNew() || $this->isModified()) {
                // persist changes
                if ($this->isNew()) {
                    $this->doInsert($con);
                    $affectedRows += 1;
                } else {
                    $affectedRows += $this->doUpdate($con);
                }
                $this->resetModified();
            }

            if ($this->sFilterPatternI18nsScheduledForDeletion !== null) {
                if (!$this->sFilterPatternI18nsScheduledForDeletion->isEmpty()) {
                    \smart_filter\models\SFilterPatternI18nQuery::create()
                        ->filterByPrimaryKeys($this->sFilterPatternI18nsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->sFilterPatternI18nsScheduledForDeletion = null;
                }
            }

            if ($this->collSFilterPatternI18ns !== null) {
                foreach ($this->collSFilterPatternI18ns as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            $this->alreadyInSave = false;

        }

        return $affectedRows;
    } // doSave()

    /**
     * Insert the row in the database.
     *
     * @param      ConnectionInterface $con
     *
     * @throws PropelException
     * @see doSave()
     */
    protected function doInsert(ConnectionInterface $con)
    {
        $modifiedColumns = array();
        $index = 0;

        $this->modifiedColumns[SFilterPatternTableMap::COL_ID] = true;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . SFilterPatternTableMap::COL_ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(SFilterPatternTableMap::COL_ID)) {
            $modifiedColumns[':p' . $index++]  = 'id';
        }
        if ($this->isColumnModified(SFilterPatternTableMap::COL_CATEGORY_ID)) {
            $modifiedColumns[':p' . $index++]  = 'category_id';
        }
        if ($this->isColumnModified(SFilterPatternTableMap::COL_ACTIVE)) {
            $modifiedColumns[':p' . $index++]  = 'active';
        }
        if ($this->isColumnModified(SFilterPatternTableMap::COL_URL_PATTERN)) {
            $modifiedColumns[':p' . $index++]  = 'url_pattern';
        }
        if ($this->isColumnModified(SFilterPatternTableMap::COL_DATA)) {
            $modifiedColumns[':p' . $index++]  = 'data';
        }
        if ($this->isColumnModified(SFilterPatternTableMap::COL_META_INDEX)) {
            $modifiedColumns[':p' . $index++]  = 'meta_index';
        }
        if ($this->isColumnModified(SFilterPatternTableMap::COL_META_FOLLOW)) {
            $modifiedColumns[':p' . $index++]  = 'meta_follow';
        }
        if ($this->isColumnModified(SFilterPatternTableMap::COL_CREATED)) {
            $modifiedColumns[':p' . $index++]  = 'created';
        }
        if ($this->isColumnModified(SFilterPatternTableMap::COL_UPDATED)) {
            $modifiedColumns[':p' . $index++]  = 'updated';
        }

        $sql = sprintf(
            'INSERT INTO smart_filter_patterns (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case 'id':
                        $stmt->bindValue($identifier, $this->id, PDO::PARAM_INT);
                        break;
                    case 'category_id':
                        $stmt->bindValue($identifier, $this->category_id, PDO::PARAM_INT);
                        break;
                    case 'active':
                        $stmt->bindValue($identifier, (int) $this->active, PDO::PARAM_INT);
                        break;
                    case 'url_pattern':
                        $stmt->bindValue($identifier, $this->url_pattern, PDO::PARAM_STR);
                        break;
                    case 'data':
                        $stmt->bindValue($identifier, $this->data, PDO::PARAM_STR);
                        break;
                    case 'meta_index':
                        $stmt->bindValue($identifier, $this->meta_index, PDO::PARAM_INT);
                        break;
                    case 'meta_follow':
                        $stmt->bindValue($identifier, $this->meta_follow, PDO::PARAM_INT);
                        break;
                    case 'created':
                        $stmt->bindValue($identifier, $this->created, PDO::PARAM_INT);
                        break;
                    case 'updated':
                        $stmt->bindValue($identifier, $this->updated, PDO::PARAM_INT);
                        break;
                }
            }
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute INSERT statement [%s]', $sql), 0, $e);
        }

        try {
            $pk = $con->lastInsertId();
        } catch (Exception $e) {
            throw new PropelException('Unable to get autoincrement id.', 0, $e);
        }
        $this->setId($pk);

        $this->setNew(false);
    }

    /**
     * Update the row in the database.
     *
     * @param      ConnectionInterface $con
     *
     * @return Integer Number of updated rows
     * @see doSave()
     */
    protected function doUpdate(ConnectionInterface $con)
    {
        $selectCriteria = $this->buildPkeyCriteria();
        $valuesCriteria = $this->buildCriteria();

        return $selectCriteria->doUpdate($valuesCriteria, $con);
    }

    /**
     * Retrieves a field from the object by name passed in as a string.
     *
     * @param      string $name name
     * @param      string $type The type of fieldname the $name is of:
     *                     one of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                     TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                     Defaults to TableMap::TYPE_PHPNAME.
     * @return mixed Value of field.
     */
    public function getByName($name, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = SFilterPatternTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
        $field = $this->getByPosition($pos);

        return $field;
    }

    /**
     * Retrieves a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param      int $pos position in xml schema
     * @return mixed Value of field at $pos
     */
    public function getByPosition($pos)
    {
        switch ($pos) {
            case 0:
                return $this->getId();
                break;
            case 1:
                return $this->getCategoryId();
                break;
            case 2:
                return $this->getActive();
                break;
            case 3:
                return $this->getUrlPattern();
                break;
            case 4:
                return $this->getData();
                break;
            case 5:
                return $this->getMetaIndex();
                break;
            case 6:
                return $this->getMetaFollow();
                break;
            case 7:
                return $this->getCreated();
                break;
            case 8:
                return $this->getUpdated();
                break;
            default:
                return null;
                break;
        } // switch()
    }

    /**
     * Exports the object as an array.
     *
     * You can specify the key type of the array by passing one of the class
     * type constants.
     *
     * @param     string  $keyType (optional) One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME,
     *                    TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                    Defaults to TableMap::TYPE_PHPNAME.
     * @param     boolean $includeLazyLoadColumns (optional) Whether to include lazy loaded columns. Defaults to TRUE.
     * @param     array $alreadyDumpedObjects List of objects to skip to avoid recursion
     * @param     boolean $includeForeignObjects (optional) Whether to include hydrated related objects. Default to FALSE.
     *
     * @return array an associative array containing the field names (as keys) and field values
     */
    public function toArray($keyType = TableMap::TYPE_PHPNAME, $includeLazyLoadColumns = true, $alreadyDumpedObjects = array(), $includeForeignObjects = false)
    {

        if (isset($alreadyDumpedObjects['SFilterPattern'][$this->hashCode()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['SFilterPattern'][$this->hashCode()] = true;
        $keys = SFilterPatternTableMap::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getCategoryId(),
            $keys[2] => $this->getActive(),
            $keys[3] => $this->getUrlPattern(),
            $keys[4] => $this->getData(),
            $keys[5] => $this->getMetaIndex(),
            $keys[6] => $this->getMetaFollow(),
            $keys[7] => $this->getCreated(),
            $keys[8] => $this->getUpdated(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aCategory) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'sCategory';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'shop_category';
                        break;
                    default:
                        $key = 'Category';
                }

                $result[$key] = $this->aCategory->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->collSFilterPatternI18ns) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'sFilterPatternI18ns';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'smart_filter_patterns_i18ns';
                        break;
                    default:
                        $key = 'SFilterPatternI18ns';
                }

                $result[$key] = $this->collSFilterPatternI18ns->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
        }

        return $result;
    }

    /**
     * Sets a field from the object by name passed in as a string.
     *
     * @param  string $name
     * @param  mixed  $value field value
     * @param  string $type The type of fieldname the $name is of:
     *                one of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                Defaults to TableMap::TYPE_PHPNAME.
     * @return $this|\smart_filter\models\SFilterPattern
     */
    public function setByName($name, $value, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = SFilterPatternTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

        return $this->setByPosition($pos, $value);
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param  int $pos position in xml schema
     * @param  mixed $value field value
     * @return $this|\smart_filter\models\SFilterPattern
     */
    public function setByPosition($pos, $value)
    {
        switch ($pos) {
            case 0:
                $this->setId($value);
                break;
            case 1:
                $this->setCategoryId($value);
                break;
            case 2:
                $this->setActive($value);
                break;
            case 3:
                $this->setUrlPattern($value);
                break;
            case 4:
                $this->setData($value);
                break;
            case 5:
                $valueSet = SFilterPatternTableMap::getValueSet(SFilterPatternTableMap::COL_META_INDEX);
                if (isset($valueSet[$value])) {
                    $value = $valueSet[$value];
                }
                $this->setMetaIndex($value);
                break;
            case 6:
                $valueSet = SFilterPatternTableMap::getValueSet(SFilterPatternTableMap::COL_META_FOLLOW);
                if (isset($valueSet[$value])) {
                    $value = $valueSet[$value];
                }
                $this->setMetaFollow($value);
                break;
            case 7:
                $this->setCreated($value);
                break;
            case 8:
                $this->setUpdated($value);
                break;
        } // switch()

        return $this;
    }

    /**
     * Populates the object using an array.
     *
     * This is particularly useful when populating an object from one of the
     * request arrays (e.g. $_POST).  This method goes through the column
     * names, checking to see whether a matching key exists in populated
     * array. If so the setByName() method is called for that column.
     *
     * You can specify the key type of the array by additionally passing one
     * of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME,
     * TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     * The default key type is the column's TableMap::TYPE_PHPNAME.
     *
     * @param      array  $arr     An array to populate the object from.
     * @param      string $keyType The type of keys the array uses.
     * @return void
     */
    public function fromArray($arr, $keyType = TableMap::TYPE_PHPNAME)
    {
        $keys = SFilterPatternTableMap::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setId($arr[$keys[0]]);
        }
        if (array_key_exists($keys[1], $arr)) {
            $this->setCategoryId($arr[$keys[1]]);
        }
        if (array_key_exists($keys[2], $arr)) {
            $this->setActive($arr[$keys[2]]);
        }
        if (array_key_exists($keys[3], $arr)) {
            $this->setUrlPattern($arr[$keys[3]]);
        }
        if (array_key_exists($keys[4], $arr)) {
            $this->setData($arr[$keys[4]]);
        }
        if (array_key_exists($keys[5], $arr)) {
            $this->setMetaIndex($arr[$keys[5]]);
        }
        if (array_key_exists($keys[6], $arr)) {
            $this->setMetaFollow($arr[$keys[6]]);
        }
        if (array_key_exists($keys[7], $arr)) {
            $this->setCreated($arr[$keys[7]]);
        }
        if (array_key_exists($keys[8], $arr)) {
            $this->setUpdated($arr[$keys[8]]);
        }
    }

     /**
     * Populate the current object from a string, using a given parser format
     * <code>
     * $book = new Book();
     * $book->importFrom('JSON', '{"Id":9012,"Title":"Don Juan","ISBN":"0140422161","Price":12.99,"PublisherId":1234,"AuthorId":5678}');
     * </code>
     *
     * You can specify the key type of the array by additionally passing one
     * of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME,
     * TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     * The default key type is the column's TableMap::TYPE_PHPNAME.
     *
     * @param mixed $parser A AbstractParser instance,
     *                       or a format name ('XML', 'YAML', 'JSON', 'CSV')
     * @param string $data The source data to import from
     * @param string $keyType The type of keys the array uses.
     *
     * @return $this|\smart_filter\models\SFilterPattern The current object, for fluid interface
     */
    public function importFrom($parser, $data, $keyType = TableMap::TYPE_PHPNAME)
    {
        if (!$parser instanceof AbstractParser) {
            $parser = AbstractParser::getParser($parser);
        }

        $this->fromArray($parser->toArray($data), $keyType);

        return $this;
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(SFilterPatternTableMap::DATABASE_NAME);

        if ($this->isColumnModified(SFilterPatternTableMap::COL_ID)) {
            $criteria->add(SFilterPatternTableMap::COL_ID, $this->id);
        }
        if ($this->isColumnModified(SFilterPatternTableMap::COL_CATEGORY_ID)) {
            $criteria->add(SFilterPatternTableMap::COL_CATEGORY_ID, $this->category_id);
        }
        if ($this->isColumnModified(SFilterPatternTableMap::COL_ACTIVE)) {
            $criteria->add(SFilterPatternTableMap::COL_ACTIVE, $this->active);
        }
        if ($this->isColumnModified(SFilterPatternTableMap::COL_URL_PATTERN)) {
            $criteria->add(SFilterPatternTableMap::COL_URL_PATTERN, $this->url_pattern);
        }
        if ($this->isColumnModified(SFilterPatternTableMap::COL_DATA)) {
            $criteria->add(SFilterPatternTableMap::COL_DATA, $this->data);
        }
        if ($this->isColumnModified(SFilterPatternTableMap::COL_META_INDEX)) {
            $criteria->add(SFilterPatternTableMap::COL_META_INDEX, $this->meta_index);
        }
        if ($this->isColumnModified(SFilterPatternTableMap::COL_META_FOLLOW)) {
            $criteria->add(SFilterPatternTableMap::COL_META_FOLLOW, $this->meta_follow);
        }
        if ($this->isColumnModified(SFilterPatternTableMap::COL_CREATED)) {
            $criteria->add(SFilterPatternTableMap::COL_CREATED, $this->created);
        }
        if ($this->isColumnModified(SFilterPatternTableMap::COL_UPDATED)) {
            $criteria->add(SFilterPatternTableMap::COL_UPDATED, $this->updated);
        }

        return $criteria;
    }

    /**
     * Builds a Criteria object containing the primary key for this object.
     *
     * Unlike buildCriteria() this method includes the primary key values regardless
     * of whether or not they have been modified.
     *
     * @throws LogicException if no primary key is defined
     *
     * @return Criteria The Criteria object containing value(s) for primary key(s).
     */
    public function buildPkeyCriteria()
    {
        $criteria = ChildSFilterPatternQuery::create();
        $criteria->add(SFilterPatternTableMap::COL_ID, $this->id);

        return $criteria;
    }

    /**
     * If the primary key is not null, return the hashcode of the
     * primary key. Otherwise, return the hash code of the object.
     *
     * @return int Hashcode
     */
    public function hashCode()
    {
        $validPk = null !== $this->getId();

        $validPrimaryKeyFKs = 0;
        $primaryKeyFKs = [];

        if ($validPk) {
            return crc32(json_encode($this->getPrimaryKey(), JSON_UNESCAPED_UNICODE));
        } elseif ($validPrimaryKeyFKs) {
            return crc32(json_encode($primaryKeyFKs, JSON_UNESCAPED_UNICODE));
        }

        return spl_object_hash($this);
    }

    /**
     * Returns the primary key for this object (row).
     * @return int
     */
    public function getPrimaryKey()
    {
        return $this->getId();
    }

    /**
     * Generic method to set the primary key (id column).
     *
     * @param       int $key Primary key.
     * @return void
     */
    public function setPrimaryKey($key)
    {
        $this->setId($key);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {
        return null === $this->getId();
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of \smart_filter\models\SFilterPattern (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setCategoryId($this->getCategoryId());
        $copyObj->setActive($this->getActive());
        $copyObj->setUrlPattern($this->getUrlPattern());
        $copyObj->setData($this->getData());
        $copyObj->setMetaIndex($this->getMetaIndex());
        $copyObj->setMetaFollow($this->getMetaFollow());
        $copyObj->setCreated($this->getCreated());
        $copyObj->setUpdated($this->getUpdated());

        if ($deepCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);

            foreach ($this->getSFilterPatternI18ns() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addSFilterPatternI18n($relObj->copy($deepCopy));
                }
            }

        } // if ($deepCopy)

        if ($makeNew) {
            $copyObj->setNew(true);
            $copyObj->setId(NULL); // this is a auto-increment column, so set to default value
        }
    }

    /**
     * Makes a copy of this object that will be inserted as a new row in table when saved.
     * It creates a new object filling in the simple attributes, but skipping any primary
     * keys that are defined for the table.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param  boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @return \smart_filter\models\SFilterPattern Clone of current object.
     * @throws PropelException
     */
    public function copy($deepCopy = false)
    {
        // we use get_class(), because this might be a subclass
        $clazz = get_class($this);
        $copyObj = new $clazz();
        $this->copyInto($copyObj, $deepCopy);

        return $copyObj;
    }

    /**
     * Declares an association between this object and a SCategory object.
     *
     * @param  SCategory $v
     * @return $this|\smart_filter\models\SFilterPattern The current object (for fluent API support)
     * @throws PropelException
     */
    public function setCategory(SCategory $v = null)
    {
        if ($v === null) {
            $this->setCategoryId(NULL);
        } else {
            $this->setCategoryId($v->getId());
        }

        $this->aCategory = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the SCategory object, it will not be re-added.
        if ($v !== null) {
            $v->addSFilterPattern($this);
        }


        return $this;
    }


    /**
     * Get the associated SCategory object
     *
     * @param  ConnectionInterface $con Optional Connection object.
     * @return SCategory The associated SCategory object.
     * @throws PropelException
     */
    public function getCategory(ConnectionInterface $con = null)
    {
        if ($this->aCategory === null && ($this->category_id !== null)) {
            $this->aCategory = SCategoryQuery::create()->findPk($this->category_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aCategory->addSFilterPatterns($this);
             */
        }

        return $this->aCategory;
    }


    /**
     * Initializes a collection based on the name of a relation.
     * Avoids crafting an 'init[$relationName]s' method name
     * that wouldn't work when StandardEnglishPluralizer is used.
     *
     * @param      string $relationName The name of the relation to initialize
     * @return void
     */
    public function initRelation($relationName)
    {
        if ('SFilterPatternI18n' == $relationName) {
            return $this->initSFilterPatternI18ns();
        }
    }

    /**
     * Clears out the collSFilterPatternI18ns collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addSFilterPatternI18ns()
     */
    public function clearSFilterPatternI18ns()
    {
        $this->collSFilterPatternI18ns = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collSFilterPatternI18ns collection loaded partially.
     */
    public function resetPartialSFilterPatternI18ns($v = true)
    {
        $this->collSFilterPatternI18nsPartial = $v;
    }

    /**
     * Initializes the collSFilterPatternI18ns collection.
     *
     * By default this just sets the collSFilterPatternI18ns collection to an empty array (like clearcollSFilterPatternI18ns());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initSFilterPatternI18ns($overrideExisting = true)
    {
        if (null !== $this->collSFilterPatternI18ns && !$overrideExisting) {
            return;
        }

        $collectionClassName = SFilterPatternI18nTableMap::getTableMap()->getCollectionClassName();

        $this->collSFilterPatternI18ns = new $collectionClassName;
        $this->collSFilterPatternI18ns->setModel('\smart_filter\models\SFilterPatternI18n');
    }

    /**
     * Gets an array of ChildSFilterPatternI18n objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildSFilterPattern is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildSFilterPatternI18n[] List of ChildSFilterPatternI18n objects
     * @throws PropelException
     */
    public function getSFilterPatternI18ns(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collSFilterPatternI18nsPartial && !$this->isNew();
        if (null === $this->collSFilterPatternI18ns || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collSFilterPatternI18ns) {
                // return empty collection
                $this->initSFilterPatternI18ns();
            } else {
                $collSFilterPatternI18ns = ChildSFilterPatternI18nQuery::create(null, $criteria)
                    ->filterBySFilterPattern($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collSFilterPatternI18nsPartial && count($collSFilterPatternI18ns)) {
                        $this->initSFilterPatternI18ns(false);

                        foreach ($collSFilterPatternI18ns as $obj) {
                            if (false == $this->collSFilterPatternI18ns->contains($obj)) {
                                $this->collSFilterPatternI18ns->append($obj);
                            }
                        }

                        $this->collSFilterPatternI18nsPartial = true;
                    }

                    return $collSFilterPatternI18ns;
                }

                if ($partial && $this->collSFilterPatternI18ns) {
                    foreach ($this->collSFilterPatternI18ns as $obj) {
                        if ($obj->isNew()) {
                            $collSFilterPatternI18ns[] = $obj;
                        }
                    }
                }

                $this->collSFilterPatternI18ns = $collSFilterPatternI18ns;
                $this->collSFilterPatternI18nsPartial = false;
            }
        }

        return $this->collSFilterPatternI18ns;
    }

    /**
     * Sets a collection of ChildSFilterPatternI18n objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $sFilterPatternI18ns A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildSFilterPattern The current object (for fluent API support)
     */
    public function setSFilterPatternI18ns(Collection $sFilterPatternI18ns, ConnectionInterface $con = null)
    {
        /** @var ChildSFilterPatternI18n[] $sFilterPatternI18nsToDelete */
        $sFilterPatternI18nsToDelete = $this->getSFilterPatternI18ns(new Criteria(), $con)->diff($sFilterPatternI18ns);


        //since at least one column in the foreign key is at the same time a PK
        //we can not just set a PK to NULL in the lines below. We have to store
        //a backup of all values, so we are able to manipulate these items based on the onDelete value later.
        $this->sFilterPatternI18nsScheduledForDeletion = clone $sFilterPatternI18nsToDelete;

        foreach ($sFilterPatternI18nsToDelete as $sFilterPatternI18nRemoved) {
            $sFilterPatternI18nRemoved->setSFilterPattern(null);
        }

        $this->collSFilterPatternI18ns = null;
        foreach ($sFilterPatternI18ns as $sFilterPatternI18n) {
            $this->addSFilterPatternI18n($sFilterPatternI18n);
        }

        $this->collSFilterPatternI18ns = $sFilterPatternI18ns;
        $this->collSFilterPatternI18nsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related SFilterPatternI18n objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related SFilterPatternI18n objects.
     * @throws PropelException
     */
    public function countSFilterPatternI18ns(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collSFilterPatternI18nsPartial && !$this->isNew();
        if (null === $this->collSFilterPatternI18ns || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collSFilterPatternI18ns) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getSFilterPatternI18ns());
            }

            $query = ChildSFilterPatternI18nQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterBySFilterPattern($this)
                ->count($con);
        }

        return count($this->collSFilterPatternI18ns);
    }

    /**
     * Method called to associate a ChildSFilterPatternI18n object to this object
     * through the ChildSFilterPatternI18n foreign key attribute.
     *
     * @param  ChildSFilterPatternI18n $l ChildSFilterPatternI18n
     * @return $this|\smart_filter\models\SFilterPattern The current object (for fluent API support)
     */
    public function addSFilterPatternI18n(ChildSFilterPatternI18n $l)
    {
        if ($l && $locale = $l->getLocale()) {
            $this->setLocale($locale);
            $this->currentTranslations[$locale] = $l;
        }
        if ($this->collSFilterPatternI18ns === null) {
            $this->initSFilterPatternI18ns();
            $this->collSFilterPatternI18nsPartial = true;
        }

        if (!$this->collSFilterPatternI18ns->contains($l)) {
            $this->doAddSFilterPatternI18n($l);

            if ($this->sFilterPatternI18nsScheduledForDeletion and $this->sFilterPatternI18nsScheduledForDeletion->contains($l)) {
                $this->sFilterPatternI18nsScheduledForDeletion->remove($this->sFilterPatternI18nsScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param ChildSFilterPatternI18n $sFilterPatternI18n The ChildSFilterPatternI18n object to add.
     */
    protected function doAddSFilterPatternI18n(ChildSFilterPatternI18n $sFilterPatternI18n)
    {
        $this->collSFilterPatternI18ns[]= $sFilterPatternI18n;
        $sFilterPatternI18n->setSFilterPattern($this);
    }

    /**
     * @param  ChildSFilterPatternI18n $sFilterPatternI18n The ChildSFilterPatternI18n object to remove.
     * @return $this|ChildSFilterPattern The current object (for fluent API support)
     */
    public function removeSFilterPatternI18n(ChildSFilterPatternI18n $sFilterPatternI18n)
    {
        if ($this->getSFilterPatternI18ns()->contains($sFilterPatternI18n)) {
            $pos = $this->collSFilterPatternI18ns->search($sFilterPatternI18n);
            $this->collSFilterPatternI18ns->remove($pos);
            if (null === $this->sFilterPatternI18nsScheduledForDeletion) {
                $this->sFilterPatternI18nsScheduledForDeletion = clone $this->collSFilterPatternI18ns;
                $this->sFilterPatternI18nsScheduledForDeletion->clear();
            }
            $this->sFilterPatternI18nsScheduledForDeletion[]= clone $sFilterPatternI18n;
            $sFilterPatternI18n->setSFilterPattern(null);
        }

        return $this;
    }

    /**
     * Clears the current object, sets all attributes to their default values and removes
     * outgoing references as well as back-references (from other objects to this one. Results probably in a database
     * change of those foreign objects when you call `save` there).
     */
    public function clear()
    {
        if (null !== $this->aCategory) {
            $this->aCategory->removeSFilterPattern($this);
        }
        $this->id = null;
        $this->category_id = null;
        $this->active = null;
        $this->url_pattern = null;
        $this->data = null;
        $this->meta_index = null;
        $this->meta_follow = null;
        $this->created = null;
        $this->updated = null;
        $this->alreadyInSave = false;
        $this->clearAllReferences();
        $this->applyDefaultValues();
        $this->resetModified();
        $this->setNew(true);
        $this->setDeleted(false);
    }

    /**
     * Resets all references and back-references to other model objects or collections of model objects.
     *
     * This method is used to reset all php object references (not the actual reference in the database).
     * Necessary for object serialisation.
     *
     * @param      boolean $deep Whether to also clear the references on all referrer objects.
     */
    public function clearAllReferences($deep = false)
    {
        if ($deep) {
            if ($this->collSFilterPatternI18ns) {
                foreach ($this->collSFilterPatternI18ns as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

        // i18n behavior
        $this->currentLocale = 'ru';
        $this->currentTranslations = null;

        $this->collSFilterPatternI18ns = null;
        $this->aCategory = null;
    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(SFilterPatternTableMap::DEFAULT_STRING_FORMAT);
    }

    // i18n behavior

    /**
     * Sets the locale for translations
     *
     * @param     string $locale Locale to use for the translation, e.g. 'fr_FR'
     *
     * @return    $this|ChildSFilterPattern The current object (for fluent API support)
     */
    public function setLocale($locale = 'ru')
    {
        $this->currentLocale = $locale;

        return $this;
    }

    /**
     * Gets the locale for translations
     *
     * @return    string $locale Locale to use for the translation, e.g. 'fr_FR'
     */
    public function getLocale()
    {
        return $this->currentLocale;
    }

    /**
     * Returns the current translation for a given locale
     *
     * @param     string $locale Locale to use for the translation, e.g. 'fr_FR'
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return ChildSFilterPatternI18n */
    public function getTranslation($locale = 'ru', ConnectionInterface $con = null)
    {
        if (!isset($this->currentTranslations[$locale])) {
            if (null !== $this->collSFilterPatternI18ns) {
                foreach ($this->collSFilterPatternI18ns as $translation) {
                    if ($translation->getLocale() == $locale) {
                        $this->currentTranslations[$locale] = $translation;

                        return $translation;
                    }
                }
            }
            if ($this->isNew()) {
                $translation = new ChildSFilterPatternI18n();
                $translation->setLocale($locale);
            } else {
                $translation = ChildSFilterPatternI18nQuery::create()
                    ->filterByPrimaryKey(array($this->getPrimaryKey(), $locale))
                    ->findOneOrCreate($con);
                $this->currentTranslations[$locale] = $translation;
            }
            $this->addSFilterPatternI18n($translation);
        }

        return $this->currentTranslations[$locale];
    }

    /**
     * Remove the translation for a given locale
     *
     * @param     string $locale Locale to use for the translation, e.g. 'fr_FR'
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return    $this|ChildSFilterPattern The current object (for fluent API support)
     */
    public function removeTranslation($locale = 'ru', ConnectionInterface $con = null)
    {
        if (!$this->isNew()) {
            ChildSFilterPatternI18nQuery::create()
                ->filterByPrimaryKey(array($this->getPrimaryKey(), $locale))
                ->delete($con);
        }
        if (isset($this->currentTranslations[$locale])) {
            unset($this->currentTranslations[$locale]);
        }
        foreach ($this->collSFilterPatternI18ns as $key => $translation) {
            if ($translation->getLocale() == $locale) {
                unset($this->collSFilterPatternI18ns[$key]);
                break;
            }
        }

        return $this;
    }

    /**
     * Returns the current translation
     *
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return ChildSFilterPatternI18n */
    public function getCurrentTranslation(ConnectionInterface $con = null)
    {
        return $this->getTranslation($this->getLocale(), $con);
    }


        /**
         * Get the [h1] column value.
         *
         * @return string
         */
        public function getH1()
        {
        return $this->getCurrentTranslation()->getH1();
    }


        /**
         * Set the value of [h1] column.
         *
         * @param string $v new value
         * @return $this|\smart_filter\models\SFilterPatternI18n The current object (for fluent API support)
         */
        public function setH1($v)
        {    $this->getCurrentTranslation()->setH1($v);

        return $this;
    }


        /**
         * Get the [meta_title] column value.
         *
         * @return string
         */
        public function getMetaTitle()
        {
        return $this->getCurrentTranslation()->getMetaTitle();
    }


        /**
         * Set the value of [meta_title] column.
         *
         * @param string $v new value
         * @return $this|\smart_filter\models\SFilterPatternI18n The current object (for fluent API support)
         */
        public function setMetaTitle($v)
        {    $this->getCurrentTranslation()->setMetaTitle($v);

        return $this;
    }


        /**
         * Get the [meta_description] column value.
         *
         * @return string
         */
        public function getMetaDescription()
        {
        return $this->getCurrentTranslation()->getMetaDescription();
    }


        /**
         * Set the value of [meta_description] column.
         *
         * @param string $v new value
         * @return $this|\smart_filter\models\SFilterPatternI18n The current object (for fluent API support)
         */
        public function setMetaDescription($v)
        {    $this->getCurrentTranslation()->setMetaDescription($v);

        return $this;
    }


        /**
         * Get the [meta_keywords] column value.
         *
         * @return string
         */
        public function getMetaKeywords()
        {
        return $this->getCurrentTranslation()->getMetaKeywords();
    }


        /**
         * Set the value of [meta_keywords] column.
         *
         * @param string $v new value
         * @return $this|\smart_filter\models\SFilterPatternI18n The current object (for fluent API support)
         */
        public function setMetaKeywords($v)
        {    $this->getCurrentTranslation()->setMetaKeywords($v);

        return $this;
    }


        /**
         * Get the [seo_text] column value.
         *
         * @return string
         */
        public function getSeoText()
        {
        return $this->getCurrentTranslation()->getSeoText();
    }


        /**
         * Set the value of [seo_text] column.
         *
         * @param string $v new value
         * @return $this|\smart_filter\models\SFilterPatternI18n The current object (for fluent API support)
         */
        public function setSeoText($v)
        {    $this->getCurrentTranslation()->setSeoText($v);

        return $this;
    }


        /**
         * Get the [name] column value.
         *
         * @return string
         */
        public function getName()
        {
        return $this->getCurrentTranslation()->getName();
    }


        /**
         * Set the value of [name] column.
         *
         * @param string $v new value
         * @return $this|\smart_filter\models\SFilterPatternI18n The current object (for fluent API support)
         */
        public function setName($v)
        {    $this->getCurrentTranslation()->setName($v);

        return $this;
    }

    // timestampable behavior

    /**
     * Mark the current object so that the update date doesn't get updated during next save
     *
     * @return     $this|ChildSFilterPattern The current object (for fluent API support)
     */
    public function keepUpdateDateUnchanged()
    {
        $this->modifiedColumns[SFilterPatternTableMap::COL_UPDATED] = true;

        return $this;
    }

    /**
     * Code to be run before persisting the object
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preSave(ConnectionInterface $con = null)
    {
        if (is_callable('parent::preSave')) {
            return parent::preSave($con);
        }
        return true;
    }

    /**
     * Code to be run after persisting the object
     * @param ConnectionInterface $con
     */
    public function postSave(ConnectionInterface $con = null)
    {
        if (is_callable('parent::postSave')) {
            parent::postSave($con);
        }
    }

    /**
     * Code to be run before inserting to database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preInsert(ConnectionInterface $con = null)
    {
        if (is_callable('parent::preInsert')) {
            return parent::preInsert($con);
        }
        return true;
    }

    /**
     * Code to be run after inserting to database
     * @param ConnectionInterface $con
     */
    public function postInsert(ConnectionInterface $con = null)
    {
        if (is_callable('parent::postInsert')) {
            parent::postInsert($con);
        }
    }

    /**
     * Code to be run before updating the object in database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preUpdate(ConnectionInterface $con = null)
    {
        if (is_callable('parent::preUpdate')) {
            return parent::preUpdate($con);
        }
        return true;
    }

    /**
     * Code to be run after updating the object in database
     * @param ConnectionInterface $con
     */
    public function postUpdate(ConnectionInterface $con = null)
    {
        if (is_callable('parent::postUpdate')) {
            parent::postUpdate($con);
        }
    }

    /**
     * Code to be run before deleting the object in database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preDelete(ConnectionInterface $con = null)
    {
        if (is_callable('parent::preDelete')) {
            return parent::preDelete($con);
        }
        return true;
    }

    /**
     * Code to be run after deleting the object in database
     * @param ConnectionInterface $con
     */
    public function postDelete(ConnectionInterface $con = null)
    {
        if (is_callable('parent::postDelete')) {
            parent::postDelete($con);
        }
    }


    /**
     * Derived method to catches calls to undefined methods.
     *
     * Provides magic import/export method support (fromXML()/toXML(), fromYAML()/toYAML(), etc.).
     * Allows to define default __call() behavior if you overwrite __call()
     *
     * @param string $name
     * @param mixed  $params
     *
     * @return array|string
     */
    public function __call($name, $params)
    {
        if (0 === strpos($name, 'get')) {
            $virtualColumn = substr($name, 3);
            if ($this->hasVirtualColumn($virtualColumn)) {
                return $this->getVirtualColumn($virtualColumn);
            }

            $virtualColumn = lcfirst($virtualColumn);
            if ($this->hasVirtualColumn($virtualColumn)) {
                return $this->getVirtualColumn($virtualColumn);
            }
        }

        if (0 === strpos($name, 'from')) {
            $format = substr($name, 4);

            return $this->importFrom($format, reset($params));
        }

        if (0 === strpos($name, 'to')) {
            $format = substr($name, 2);
            $includeLazyLoadColumns = isset($params[0]) ? $params[0] : true;

            return $this->exportTo($format, $includeLazyLoadColumns);
        }

        throw new BadMethodCallException(sprintf('Call to undefined method: %s.', $name));
    }

}
