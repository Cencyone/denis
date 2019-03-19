<?php

namespace Base;

use \SProductPropertiesData as ChildSProductPropertiesData;
use \SProductPropertiesDataQuery as ChildSProductPropertiesDataQuery;
use \SProperties as ChildSProperties;
use \SPropertiesQuery as ChildSPropertiesQuery;
use \SPropertyValue as ChildSPropertyValue;
use \SPropertyValueI18n as ChildSPropertyValueI18n;
use \SPropertyValueI18nQuery as ChildSPropertyValueI18nQuery;
use \SPropertyValueQuery as ChildSPropertyValueQuery;
use \Exception;
use \PDO;
use CMSFactory\PropelBaseModelClass;
use Map\SProductPropertiesDataTableMap;
use Map\SPropertyValueI18nTableMap;
use Map\SPropertyValueTableMap;
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

/**
 * Base class that represents a row from the 'shop_product_property_value' table.
 *
 *
 *
 * @package    propel.generator.shop.models.Base
 */
abstract class SPropertyValue extends PropelBaseModelClass implements ActiveRecordInterface
{
    /**
     * TableMap class name
     */
    const TABLE_MAP = '\\Map\\SPropertyValueTableMap';


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
     * The value for the property_id field.
     *
     * @var        int
     */
    protected $property_id;

    /**
     * The value for the position field.
     *
     * @var        int
     */
    protected $position;

    /**
     * @var        ChildSProperties
     */
    protected $aSProperties;

    /**
     * @var        ObjectCollection|ChildSPropertyValueI18n[] Collection to store aggregation of ChildSPropertyValueI18n objects.
     */
    protected $collSPropertyValueI18ns;
    protected $collSPropertyValueI18nsPartial;

    /**
     * @var        ObjectCollection|ChildSProductPropertiesData[] Collection to store aggregation of ChildSProductPropertiesData objects.
     */
    protected $collSProductPropertiesDatas;
    protected $collSProductPropertiesDatasPartial;

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
     * @var        array[ChildSPropertyValueI18n]
     */
    protected $currentTranslations;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildSPropertyValueI18n[]
     */
    protected $sPropertyValueI18nsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildSProductPropertiesData[]
     */
    protected $sProductPropertiesDatasScheduledForDeletion = null;

    /**
     * Initializes internal state of Base\SPropertyValue object.
     */
    public function __construct()
    {
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
     * Compares this with another <code>SPropertyValue</code> instance.  If
     * <code>obj</code> is an instance of <code>SPropertyValue</code>, delegates to
     * <code>equals(SPropertyValue)</code>.  Otherwise, returns <code>false</code>.
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
     * @return $this|SPropertyValue The current object, for fluid interface
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
     * Get the [property_id] column value.
     *
     * @return int
     */
    public function getPropertyId()
    {
        return $this->property_id;
    }

    /**
     * Get the [position] column value.
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set the value of [id] column.
     *
     * @param int $v new value
     * @return $this|\SPropertyValue The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[SPropertyValueTableMap::COL_ID] = true;
        }

        return $this;
    } // setId()

    /**
     * Set the value of [property_id] column.
     *
     * @param int $v new value
     * @return $this|\SPropertyValue The current object (for fluent API support)
     */
    public function setPropertyId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->property_id !== $v) {
            $this->property_id = $v;
            $this->modifiedColumns[SPropertyValueTableMap::COL_PROPERTY_ID] = true;
        }

        if ($this->aSProperties !== null && $this->aSProperties->getId() !== $v) {
            $this->aSProperties = null;
        }

        return $this;
    } // setPropertyId()

    /**
     * Set the value of [position] column.
     *
     * @param int $v new value
     * @return $this|\SPropertyValue The current object (for fluent API support)
     */
    public function setPosition($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->position !== $v) {
            $this->position = $v;
            $this->modifiedColumns[SPropertyValueTableMap::COL_POSITION] = true;
        }

        return $this;
    } // setPosition()

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

            $col = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : SPropertyValueTableMap::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : SPropertyValueTableMap::translateFieldName('PropertyId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->property_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : SPropertyValueTableMap::translateFieldName('Position', TableMap::TYPE_PHPNAME, $indexType)];
            $this->position = (null !== $col) ? (int) $col : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 3; // 3 = SPropertyValueTableMap::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException(sprintf('Error populating %s object', '\\SPropertyValue'), 0, $e);
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
        if ($this->aSProperties !== null && $this->property_id !== $this->aSProperties->getId()) {
            $this->aSProperties = null;
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
            $con = Propel::getServiceContainer()->getReadConnection(SPropertyValueTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildSPropertyValueQuery::create(null, $this->buildPkeyCriteria())->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
        $row = $dataFetcher->fetch();
        $dataFetcher->close();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aSProperties = null;
            $this->collSPropertyValueI18ns = null;

            $this->collSProductPropertiesDatas = null;

        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      ConnectionInterface $con
     * @return void
     * @throws PropelException
     * @see SPropertyValue::setDeleted()
     * @see SPropertyValue::isDeleted()
     */
    public function delete(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(SPropertyValueTableMap::DATABASE_NAME);
        }

        $con->transaction(function () use ($con) {
            $deleteQuery = ChildSPropertyValueQuery::create()
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
            $con = Propel::getServiceContainer()->getWriteConnection(SPropertyValueTableMap::DATABASE_NAME);
        }

        return $con->transaction(function () use ($con) {
            $ret = $this->preSave($con);
            $isInsert = $this->isNew();
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
            } else {
                $ret = $ret && $this->preUpdate($con);
            }
            if ($ret) {
                $affectedRows = $this->doSave($con);
                if ($isInsert) {
                    $this->postInsert($con);
                } else {
                    $this->postUpdate($con);
                }
                $this->postSave($con);
                SPropertyValueTableMap::addInstanceToPool($this);
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

            if ($this->aSProperties !== null) {
                if ($this->aSProperties->isModified() || $this->aSProperties->isNew()) {
                    $affectedRows += $this->aSProperties->save($con);
                }
                $this->setSProperties($this->aSProperties);
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

            if ($this->sPropertyValueI18nsScheduledForDeletion !== null) {
                if (!$this->sPropertyValueI18nsScheduledForDeletion->isEmpty()) {
                    \SPropertyValueI18nQuery::create()
                        ->filterByPrimaryKeys($this->sPropertyValueI18nsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->sPropertyValueI18nsScheduledForDeletion = null;
                }
            }

            if ($this->collSPropertyValueI18ns !== null) {
                foreach ($this->collSPropertyValueI18ns as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->sProductPropertiesDatasScheduledForDeletion !== null) {
                if (!$this->sProductPropertiesDatasScheduledForDeletion->isEmpty()) {
                    \SProductPropertiesDataQuery::create()
                        ->filterByPrimaryKeys($this->sProductPropertiesDatasScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->sProductPropertiesDatasScheduledForDeletion = null;
                }
            }

            if ($this->collSProductPropertiesDatas !== null) {
                foreach ($this->collSProductPropertiesDatas as $referrerFK) {
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

        $this->modifiedColumns[SPropertyValueTableMap::COL_ID] = true;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . SPropertyValueTableMap::COL_ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(SPropertyValueTableMap::COL_ID)) {
            $modifiedColumns[':p' . $index++]  = 'id';
        }
        if ($this->isColumnModified(SPropertyValueTableMap::COL_PROPERTY_ID)) {
            $modifiedColumns[':p' . $index++]  = 'property_id';
        }
        if ($this->isColumnModified(SPropertyValueTableMap::COL_POSITION)) {
            $modifiedColumns[':p' . $index++]  = 'position';
        }

        $sql = sprintf(
            'INSERT INTO shop_product_property_value (%s) VALUES (%s)',
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
                    case 'property_id':
                        $stmt->bindValue($identifier, $this->property_id, PDO::PARAM_INT);
                        break;
                    case 'position':
                        $stmt->bindValue($identifier, $this->position, PDO::PARAM_INT);
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
        $pos = SPropertyValueTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
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
                return $this->getPropertyId();
                break;
            case 2:
                return $this->getPosition();
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

        if (isset($alreadyDumpedObjects['SPropertyValue'][$this->hashCode()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['SPropertyValue'][$this->hashCode()] = true;
        $keys = SPropertyValueTableMap::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getPropertyId(),
            $keys[2] => $this->getPosition(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aSProperties) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'sProperties';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'shop_product_properties';
                        break;
                    default:
                        $key = 'SProperties';
                }

                $result[$key] = $this->aSProperties->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->collSPropertyValueI18ns) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'sPropertyValueI18ns';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'shop_product_property_value_i18ns';
                        break;
                    default:
                        $key = 'SPropertyValueI18ns';
                }

                $result[$key] = $this->collSPropertyValueI18ns->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collSProductPropertiesDatas) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'sProductPropertiesDatas';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'shop_product_properties_datas';
                        break;
                    default:
                        $key = 'SProductPropertiesDatas';
                }

                $result[$key] = $this->collSProductPropertiesDatas->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
     * @return $this|\SPropertyValue
     */
    public function setByName($name, $value, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = SPropertyValueTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

        return $this->setByPosition($pos, $value);
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param  int $pos position in xml schema
     * @param  mixed $value field value
     * @return $this|\SPropertyValue
     */
    public function setByPosition($pos, $value)
    {
        switch ($pos) {
            case 0:
                $this->setId($value);
                break;
            case 1:
                $this->setPropertyId($value);
                break;
            case 2:
                $this->setPosition($value);
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
        $keys = SPropertyValueTableMap::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setId($arr[$keys[0]]);
        }
        if (array_key_exists($keys[1], $arr)) {
            $this->setPropertyId($arr[$keys[1]]);
        }
        if (array_key_exists($keys[2], $arr)) {
            $this->setPosition($arr[$keys[2]]);
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
     * @return $this|\SPropertyValue The current object, for fluid interface
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
        $criteria = new Criteria(SPropertyValueTableMap::DATABASE_NAME);

        if ($this->isColumnModified(SPropertyValueTableMap::COL_ID)) {
            $criteria->add(SPropertyValueTableMap::COL_ID, $this->id);
        }
        if ($this->isColumnModified(SPropertyValueTableMap::COL_PROPERTY_ID)) {
            $criteria->add(SPropertyValueTableMap::COL_PROPERTY_ID, $this->property_id);
        }
        if ($this->isColumnModified(SPropertyValueTableMap::COL_POSITION)) {
            $criteria->add(SPropertyValueTableMap::COL_POSITION, $this->position);
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
        $criteria = ChildSPropertyValueQuery::create();
        $criteria->add(SPropertyValueTableMap::COL_ID, $this->id);

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
     * @param      object $copyObj An object of \SPropertyValue (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setPropertyId($this->getPropertyId());
        $copyObj->setPosition($this->getPosition());

        if ($deepCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);

            foreach ($this->getSPropertyValueI18ns() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addSPropertyValueI18n($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getSProductPropertiesDatas() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addSProductPropertiesData($relObj->copy($deepCopy));
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
     * @return \SPropertyValue Clone of current object.
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
     * Declares an association between this object and a ChildSProperties object.
     *
     * @param  ChildSProperties $v
     * @return $this|\SPropertyValue The current object (for fluent API support)
     * @throws PropelException
     */
    public function setSProperties(ChildSProperties $v = null)
    {
        if ($v === null) {
            $this->setPropertyId(NULL);
        } else {
            $this->setPropertyId($v->getId());
        }

        $this->aSProperties = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildSProperties object, it will not be re-added.
        if ($v !== null) {
            $v->addSPropertyValue($this);
        }


        return $this;
    }


    /**
     * Get the associated ChildSProperties object
     *
     * @param  ConnectionInterface $con Optional Connection object.
     * @return ChildSProperties The associated ChildSProperties object.
     * @throws PropelException
     */
    public function getSProperties(ConnectionInterface $con = null)
    {
        if ($this->aSProperties === null && ($this->property_id !== null)) {
            $this->aSProperties = ChildSPropertiesQuery::create()->findPk($this->property_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aSProperties->addSPropertyValues($this);
             */
        }

        return $this->aSProperties;
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
        if ('SPropertyValueI18n' == $relationName) {
            return $this->initSPropertyValueI18ns();
        }
        if ('SProductPropertiesData' == $relationName) {
            return $this->initSProductPropertiesDatas();
        }
    }

    /**
     * Clears out the collSPropertyValueI18ns collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addSPropertyValueI18ns()
     */
    public function clearSPropertyValueI18ns()
    {
        $this->collSPropertyValueI18ns = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collSPropertyValueI18ns collection loaded partially.
     */
    public function resetPartialSPropertyValueI18ns($v = true)
    {
        $this->collSPropertyValueI18nsPartial = $v;
    }

    /**
     * Initializes the collSPropertyValueI18ns collection.
     *
     * By default this just sets the collSPropertyValueI18ns collection to an empty array (like clearcollSPropertyValueI18ns());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initSPropertyValueI18ns($overrideExisting = true)
    {
        if (null !== $this->collSPropertyValueI18ns && !$overrideExisting) {
            return;
        }

        $collectionClassName = SPropertyValueI18nTableMap::getTableMap()->getCollectionClassName();

        $this->collSPropertyValueI18ns = new $collectionClassName;
        $this->collSPropertyValueI18ns->setModel('\SPropertyValueI18n');
    }

    /**
     * Gets an array of ChildSPropertyValueI18n objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildSPropertyValue is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildSPropertyValueI18n[] List of ChildSPropertyValueI18n objects
     * @throws PropelException
     */
    public function getSPropertyValueI18ns(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collSPropertyValueI18nsPartial && !$this->isNew();
        if (null === $this->collSPropertyValueI18ns || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collSPropertyValueI18ns) {
                // return empty collection
                $this->initSPropertyValueI18ns();
            } else {
                $collSPropertyValueI18ns = ChildSPropertyValueI18nQuery::create(null, $criteria)
                    ->filterBySPropertyValue($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collSPropertyValueI18nsPartial && count($collSPropertyValueI18ns)) {
                        $this->initSPropertyValueI18ns(false);

                        foreach ($collSPropertyValueI18ns as $obj) {
                            if (false == $this->collSPropertyValueI18ns->contains($obj)) {
                                $this->collSPropertyValueI18ns->append($obj);
                            }
                        }

                        $this->collSPropertyValueI18nsPartial = true;
                    }

                    return $collSPropertyValueI18ns;
                }

                if ($partial && $this->collSPropertyValueI18ns) {
                    foreach ($this->collSPropertyValueI18ns as $obj) {
                        if ($obj->isNew()) {
                            $collSPropertyValueI18ns[] = $obj;
                        }
                    }
                }

                $this->collSPropertyValueI18ns = $collSPropertyValueI18ns;
                $this->collSPropertyValueI18nsPartial = false;
            }
        }

        return $this->collSPropertyValueI18ns;
    }

    /**
     * Sets a collection of ChildSPropertyValueI18n objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $sPropertyValueI18ns A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildSPropertyValue The current object (for fluent API support)
     */
    public function setSPropertyValueI18ns(Collection $sPropertyValueI18ns, ConnectionInterface $con = null)
    {
        /** @var ChildSPropertyValueI18n[] $sPropertyValueI18nsToDelete */
        $sPropertyValueI18nsToDelete = $this->getSPropertyValueI18ns(new Criteria(), $con)->diff($sPropertyValueI18ns);


        //since at least one column in the foreign key is at the same time a PK
        //we can not just set a PK to NULL in the lines below. We have to store
        //a backup of all values, so we are able to manipulate these items based on the onDelete value later.
        $this->sPropertyValueI18nsScheduledForDeletion = clone $sPropertyValueI18nsToDelete;

        foreach ($sPropertyValueI18nsToDelete as $sPropertyValueI18nRemoved) {
            $sPropertyValueI18nRemoved->setSPropertyValue(null);
        }

        $this->collSPropertyValueI18ns = null;
        foreach ($sPropertyValueI18ns as $sPropertyValueI18n) {
            $this->addSPropertyValueI18n($sPropertyValueI18n);
        }

        $this->collSPropertyValueI18ns = $sPropertyValueI18ns;
        $this->collSPropertyValueI18nsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related SPropertyValueI18n objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related SPropertyValueI18n objects.
     * @throws PropelException
     */
    public function countSPropertyValueI18ns(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collSPropertyValueI18nsPartial && !$this->isNew();
        if (null === $this->collSPropertyValueI18ns || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collSPropertyValueI18ns) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getSPropertyValueI18ns());
            }

            $query = ChildSPropertyValueI18nQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterBySPropertyValue($this)
                ->count($con);
        }

        return count($this->collSPropertyValueI18ns);
    }

    /**
     * Method called to associate a ChildSPropertyValueI18n object to this object
     * through the ChildSPropertyValueI18n foreign key attribute.
     *
     * @param  ChildSPropertyValueI18n $l ChildSPropertyValueI18n
     * @return $this|\SPropertyValue The current object (for fluent API support)
     */
    public function addSPropertyValueI18n(ChildSPropertyValueI18n $l)
    {
        if ($l && $locale = $l->getLocale()) {
            $this->setLocale($locale);
            $this->currentTranslations[$locale] = $l;
        }
        if ($this->collSPropertyValueI18ns === null) {
            $this->initSPropertyValueI18ns();
            $this->collSPropertyValueI18nsPartial = true;
        }

        if (!$this->collSPropertyValueI18ns->contains($l)) {
            $this->doAddSPropertyValueI18n($l);

            if ($this->sPropertyValueI18nsScheduledForDeletion and $this->sPropertyValueI18nsScheduledForDeletion->contains($l)) {
                $this->sPropertyValueI18nsScheduledForDeletion->remove($this->sPropertyValueI18nsScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param ChildSPropertyValueI18n $sPropertyValueI18n The ChildSPropertyValueI18n object to add.
     */
    protected function doAddSPropertyValueI18n(ChildSPropertyValueI18n $sPropertyValueI18n)
    {
        $this->collSPropertyValueI18ns[]= $sPropertyValueI18n;
        $sPropertyValueI18n->setSPropertyValue($this);
    }

    /**
     * @param  ChildSPropertyValueI18n $sPropertyValueI18n The ChildSPropertyValueI18n object to remove.
     * @return $this|ChildSPropertyValue The current object (for fluent API support)
     */
    public function removeSPropertyValueI18n(ChildSPropertyValueI18n $sPropertyValueI18n)
    {
        if ($this->getSPropertyValueI18ns()->contains($sPropertyValueI18n)) {
            $pos = $this->collSPropertyValueI18ns->search($sPropertyValueI18n);
            $this->collSPropertyValueI18ns->remove($pos);
            if (null === $this->sPropertyValueI18nsScheduledForDeletion) {
                $this->sPropertyValueI18nsScheduledForDeletion = clone $this->collSPropertyValueI18ns;
                $this->sPropertyValueI18nsScheduledForDeletion->clear();
            }
            $this->sPropertyValueI18nsScheduledForDeletion[]= clone $sPropertyValueI18n;
            $sPropertyValueI18n->setSPropertyValue(null);
        }

        return $this;
    }

    /**
     * Clears out the collSProductPropertiesDatas collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addSProductPropertiesDatas()
     */
    public function clearSProductPropertiesDatas()
    {
        $this->collSProductPropertiesDatas = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collSProductPropertiesDatas collection loaded partially.
     */
    public function resetPartialSProductPropertiesDatas($v = true)
    {
        $this->collSProductPropertiesDatasPartial = $v;
    }

    /**
     * Initializes the collSProductPropertiesDatas collection.
     *
     * By default this just sets the collSProductPropertiesDatas collection to an empty array (like clearcollSProductPropertiesDatas());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initSProductPropertiesDatas($overrideExisting = true)
    {
        if (null !== $this->collSProductPropertiesDatas && !$overrideExisting) {
            return;
        }

        $collectionClassName = SProductPropertiesDataTableMap::getTableMap()->getCollectionClassName();

        $this->collSProductPropertiesDatas = new $collectionClassName;
        $this->collSProductPropertiesDatas->setModel('\SProductPropertiesData');
    }

    /**
     * Gets an array of ChildSProductPropertiesData objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildSPropertyValue is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildSProductPropertiesData[] List of ChildSProductPropertiesData objects
     * @throws PropelException
     */
    public function getSProductPropertiesDatas(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collSProductPropertiesDatasPartial && !$this->isNew();
        if (null === $this->collSProductPropertiesDatas || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collSProductPropertiesDatas) {
                // return empty collection
                $this->initSProductPropertiesDatas();
            } else {
                $collSProductPropertiesDatas = ChildSProductPropertiesDataQuery::create(null, $criteria)
                    ->filterBySPropertyValue($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collSProductPropertiesDatasPartial && count($collSProductPropertiesDatas)) {
                        $this->initSProductPropertiesDatas(false);

                        foreach ($collSProductPropertiesDatas as $obj) {
                            if (false == $this->collSProductPropertiesDatas->contains($obj)) {
                                $this->collSProductPropertiesDatas->append($obj);
                            }
                        }

                        $this->collSProductPropertiesDatasPartial = true;
                    }

                    return $collSProductPropertiesDatas;
                }

                if ($partial && $this->collSProductPropertiesDatas) {
                    foreach ($this->collSProductPropertiesDatas as $obj) {
                        if ($obj->isNew()) {
                            $collSProductPropertiesDatas[] = $obj;
                        }
                    }
                }

                $this->collSProductPropertiesDatas = $collSProductPropertiesDatas;
                $this->collSProductPropertiesDatasPartial = false;
            }
        }

        return $this->collSProductPropertiesDatas;
    }

    /**
     * Sets a collection of ChildSProductPropertiesData objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $sProductPropertiesDatas A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildSPropertyValue The current object (for fluent API support)
     */
    public function setSProductPropertiesDatas(Collection $sProductPropertiesDatas, ConnectionInterface $con = null)
    {
        /** @var ChildSProductPropertiesData[] $sProductPropertiesDatasToDelete */
        $sProductPropertiesDatasToDelete = $this->getSProductPropertiesDatas(new Criteria(), $con)->diff($sProductPropertiesDatas);


        $this->sProductPropertiesDatasScheduledForDeletion = $sProductPropertiesDatasToDelete;

        foreach ($sProductPropertiesDatasToDelete as $sProductPropertiesDataRemoved) {
            $sProductPropertiesDataRemoved->setSPropertyValue(null);
        }

        $this->collSProductPropertiesDatas = null;
        foreach ($sProductPropertiesDatas as $sProductPropertiesData) {
            $this->addSProductPropertiesData($sProductPropertiesData);
        }

        $this->collSProductPropertiesDatas = $sProductPropertiesDatas;
        $this->collSProductPropertiesDatasPartial = false;

        return $this;
    }

    /**
     * Returns the number of related SProductPropertiesData objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related SProductPropertiesData objects.
     * @throws PropelException
     */
    public function countSProductPropertiesDatas(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collSProductPropertiesDatasPartial && !$this->isNew();
        if (null === $this->collSProductPropertiesDatas || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collSProductPropertiesDatas) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getSProductPropertiesDatas());
            }

            $query = ChildSProductPropertiesDataQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterBySPropertyValue($this)
                ->count($con);
        }

        return count($this->collSProductPropertiesDatas);
    }

    /**
     * Method called to associate a ChildSProductPropertiesData object to this object
     * through the ChildSProductPropertiesData foreign key attribute.
     *
     * @param  ChildSProductPropertiesData $l ChildSProductPropertiesData
     * @return $this|\SPropertyValue The current object (for fluent API support)
     */
    public function addSProductPropertiesData(ChildSProductPropertiesData $l)
    {
        if ($this->collSProductPropertiesDatas === null) {
            $this->initSProductPropertiesDatas();
            $this->collSProductPropertiesDatasPartial = true;
        }

        if (!$this->collSProductPropertiesDatas->contains($l)) {
            $this->doAddSProductPropertiesData($l);

            if ($this->sProductPropertiesDatasScheduledForDeletion and $this->sProductPropertiesDatasScheduledForDeletion->contains($l)) {
                $this->sProductPropertiesDatasScheduledForDeletion->remove($this->sProductPropertiesDatasScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param ChildSProductPropertiesData $sProductPropertiesData The ChildSProductPropertiesData object to add.
     */
    protected function doAddSProductPropertiesData(ChildSProductPropertiesData $sProductPropertiesData)
    {
        $this->collSProductPropertiesDatas[]= $sProductPropertiesData;
        $sProductPropertiesData->setSPropertyValue($this);
    }

    /**
     * @param  ChildSProductPropertiesData $sProductPropertiesData The ChildSProductPropertiesData object to remove.
     * @return $this|ChildSPropertyValue The current object (for fluent API support)
     */
    public function removeSProductPropertiesData(ChildSProductPropertiesData $sProductPropertiesData)
    {
        if ($this->getSProductPropertiesDatas()->contains($sProductPropertiesData)) {
            $pos = $this->collSProductPropertiesDatas->search($sProductPropertiesData);
            $this->collSProductPropertiesDatas->remove($pos);
            if (null === $this->sProductPropertiesDatasScheduledForDeletion) {
                $this->sProductPropertiesDatasScheduledForDeletion = clone $this->collSProductPropertiesDatas;
                $this->sProductPropertiesDatasScheduledForDeletion->clear();
            }
            $this->sProductPropertiesDatasScheduledForDeletion[]= $sProductPropertiesData;
            $sProductPropertiesData->setSPropertyValue(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this SPropertyValue is new, it will return
     * an empty collection; or if this SPropertyValue has previously
     * been saved, it will retrieve related SProductPropertiesDatas from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in SPropertyValue.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildSProductPropertiesData[] List of ChildSProductPropertiesData objects
     */
    public function getSProductPropertiesDatasJoinSProperties(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildSProductPropertiesDataQuery::create(null, $criteria);
        $query->joinWith('SProperties', $joinBehavior);

        return $this->getSProductPropertiesDatas($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this SPropertyValue is new, it will return
     * an empty collection; or if this SPropertyValue has previously
     * been saved, it will retrieve related SProductPropertiesDatas from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in SPropertyValue.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildSProductPropertiesData[] List of ChildSProductPropertiesData objects
     */
    public function getSProductPropertiesDatasJoinProduct(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildSProductPropertiesDataQuery::create(null, $criteria);
        $query->joinWith('Product', $joinBehavior);

        return $this->getSProductPropertiesDatas($query, $con);
    }

    /**
     * Clears the current object, sets all attributes to their default values and removes
     * outgoing references as well as back-references (from other objects to this one. Results probably in a database
     * change of those foreign objects when you call `save` there).
     */
    public function clear()
    {
        if (null !== $this->aSProperties) {
            $this->aSProperties->removeSPropertyValue($this);
        }
        $this->id = null;
        $this->property_id = null;
        $this->position = null;
        $this->alreadyInSave = false;
        $this->clearAllReferences();
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
            if ($this->collSPropertyValueI18ns) {
                foreach ($this->collSPropertyValueI18ns as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collSProductPropertiesDatas) {
                foreach ($this->collSProductPropertiesDatas as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

        // i18n behavior
        $this->currentLocale = 'ru';
        $this->currentTranslations = null;

        $this->collSPropertyValueI18ns = null;
        $this->collSProductPropertiesDatas = null;
        $this->aSProperties = null;
    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(SPropertyValueTableMap::DEFAULT_STRING_FORMAT);
    }

    // i18n behavior

    /**
     * Sets the locale for translations
     *
     * @param     string $locale Locale to use for the translation, e.g. 'fr_FR'
     *
     * @return    $this|ChildSPropertyValue The current object (for fluent API support)
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
     * @return ChildSPropertyValueI18n */
    public function getTranslation($locale = 'ru', ConnectionInterface $con = null)
    {
        if (!isset($this->currentTranslations[$locale])) {
            if (null !== $this->collSPropertyValueI18ns) {
                foreach ($this->collSPropertyValueI18ns as $translation) {
                    if ($translation->getLocale() == $locale) {
                        $this->currentTranslations[$locale] = $translation;

                        return $translation;
                    }
                }
            }
            if ($this->isNew()) {
                $translation = new ChildSPropertyValueI18n();
                $translation->setLocale($locale);
            } else {
                $translation = ChildSPropertyValueI18nQuery::create()
                    ->filterByPrimaryKey(array($this->getPrimaryKey(), $locale))
                    ->findOneOrCreate($con);
                $this->currentTranslations[$locale] = $translation;
            }
            $this->addSPropertyValueI18n($translation);
        }

        return $this->currentTranslations[$locale];
    }

    /**
     * Remove the translation for a given locale
     *
     * @param     string $locale Locale to use for the translation, e.g. 'fr_FR'
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return    $this|ChildSPropertyValue The current object (for fluent API support)
     */
    public function removeTranslation($locale = 'ru', ConnectionInterface $con = null)
    {
        if (!$this->isNew()) {
            ChildSPropertyValueI18nQuery::create()
                ->filterByPrimaryKey(array($this->getPrimaryKey(), $locale))
                ->delete($con);
        }
        if (isset($this->currentTranslations[$locale])) {
            unset($this->currentTranslations[$locale]);
        }
        foreach ($this->collSPropertyValueI18ns as $key => $translation) {
            if ($translation->getLocale() == $locale) {
                unset($this->collSPropertyValueI18ns[$key]);
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
     * @return ChildSPropertyValueI18n */
    public function getCurrentTranslation(ConnectionInterface $con = null)
    {
        return $this->getTranslation($this->getLocale(), $con);
    }


        /**
         * Get the [value] column value.
         *
         * @return string
         */
        public function getValue()
        {
        return $this->getCurrentTranslation()->getValue();
    }


        /**
         * Set the value of [value] column.
         *
         * @param string $v new value
         * @return $this|\SPropertyValueI18n The current object (for fluent API support)
         */
        public function setValue($v)
        {    $this->getCurrentTranslation()->setValue($v);

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
