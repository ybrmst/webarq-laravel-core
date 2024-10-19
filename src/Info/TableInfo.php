<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 11/25/2016
 * Time: 10:28 AM
 */

namespace Webarq\Info;


use Wa;
use Webarq\Manager\SingletonManagerTrait;

/**
 * Helper class
 *
 * Class TableInfo
 * @package Webarq\Info
 */
class TableInfo
{
    use SingletonManagerTrait;

    /**
     * Table columns
     *
     * @var array object Webarq\Info\ColumnInfo
     */
    protected $columns = [];

    /**
     * Table extra information
     *
     * @var array
     */
    protected $extra = [];

    /**
     * Table column(s) that used to record any table history transaction.
     * It's like who doing what
     *
     * @var array
     */
    protected $histories = [];

    /**
     * Module name
     * In what module this table is register
     *
     * @var string
     */
    protected $module;

    /**
     * Table name
     *
     * @var string
     */
    protected $name;

    /**
     * Table primary column name
     *
     * @var object Webarq\Info\ColumnInfo
     */
    protected $primaryColumn;

    /**
     * Is table multilingual?
     *
     * @var bool
     */
    protected $multilingual = false;

    /**
     * Serialize table options
     *
     * @var array
     */
    protected $serialize;

    /**
     * @var string
     */
    protected $createTimeColumn;

    /**
     * @var string
     */
    protected $updateTimeColumn;

    /**
     * @var array
     */
    protected $foreign = [];

    /**
     * @var
     */
    protected $model;

    /**
     * @var
     */
    protected $modelDir;

    /**
     * @var null|\Webarq\Info\ColumnInfo
     */
    protected $sequence;

    /**
     * @var array
     */
    protected $uniqueColumns = [];

    /**
     * @var array
     */
    protected $uniquesColumns = [];

    /**
     * @var
     */
    protected $alias;

    /**
     * Create TableInfo instance
     *
     * @param $name
     * @param $module
     * @param array $options
     */
    public function __construct($name, $module, array $options = [])
    {
        $this->name = $name;
        $this->module = $module;

        $this->setup($options);

        $this->setSerialize();
    }

    /**
     * Setup class environment
     *
     * @param array $configs
     */
    protected function setup(array $configs)
    {
        if ([] !== $configs) {
            foreach ($configs as $i => $value) {
                if (is_numeric($i)) {
                    $this->setColumn($value);
                } elseif (property_exists($this, ($m = lcfirst(studly_case($i))))) {
                    $this->{$m} = $value;
                } else {
                    switch ($i) {
                        case 'timestamps':
                            $this->setColumn(config('webarq.data-type-master.createOn'));
                            $this->setColumn(config('webarq.data-type-master.lastUpdate'));
                            $this->createTimeColumn = config('webarq.data-type-master.createOn.name');
                            $this->updateTimeColumn = config('webarq.data-type-master.lastUpdate.name');
                            break;
                        case 'timestamp':
                            $this->setColumn(config('webarq.data-type-master.createOn'));
                            $this->createTimeColumn = config('webarq.data-type-master.createOn.name');
                            break;
                        default:
                            $this->extra[$i] = $value;
                            break;
                    }
                }
            }
        }
    }

    /**
     * Set table column
     *
     * @param array $options
     */
    protected function setColumn(array $options)
    {
        $column = Wa::load('info.column', $options);

        if ($column->isPrimary()) {
            $this->primaryColumn = $column;
        }
        if (true === $column->getExtra('multilingual')) {
            $this->multilingual = true;
        }

        switch ($column->getMaster()) {
            case 'sequence':
                $this->sequence = $column;
                break;
            case 'createOn':
                $this->createTimeColumn = $column->getName();
                break;
            case 'lastUpdate':
                $this->updateTimeColumn = $column->getName();
                break;
        }

        if ($column->isUnique()) {
            $this->uniqueColumns[] = $column->getName();
        }

        if ($column->isUniques()) {
            $this->uniquesColumns[] = $column->getName();
        }

        $this->columns[$column->getName()] = $column;
    }

    /**
     * Get column item by given $name
     *
     * @param $name
     * @return mixed
     */
    public function getColumn($name)
    {
        return array_get($this->columns, $name, null);
    }

    /**
     * Get all table columns
     *
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Get all unique table columns
     * @return array
     */
    public function getUniqueColumns()
    {
        return $this->uniqueColumns;
    }

    /**
     * Get all uniques table columns
     * @return array
     */
    public function getUniquesColumns()
    {
        return $this->uniquesColumns;
    }

    /**
     * @param $key
     * @param null $default
     * @return mixed
     */
    public function getHistory($key, $default = null)
    {
        return array_get($this->histories, $key, $default);
    }

    /**
     * Get all logs
     *
     * @return array
     */
    public function getHistories()
    {
        if (!is_array($this->histories)) {
            $this->histories = [];
        }

        return $this->histories;
    }

    /**
     * Get table module
     *
     * @return string
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * Get table name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get table primary column
     *
     * @return string
     */
    public function primaryColumn()
    {
        return $this->primaryColumn;
    }

    /**
     * Is table multilingual?
     *
     * @return bool
     */
    public function isMultiLingual()
    {
        return $this->multilingual && class_exists('Wl');
    }

    /**
     * Unserialize table options which already serialized
     *
     * @return array|string
     */
    public function getSerialize()
    {
        return $this->serialize;
    }

    /**
     * Set serialized columns and foreign
     */
    protected function setSerialize()
    {
        if ([] !== $this->columns) {
            $cols[] = $this->foreign;
            foreach ($this->columns as $name => $info) {
                $cols[$name] = $info->unserialize();
            }
            $this->serialize = serialize($cols);
        }
    }

    /**
     * Get table reference key
     *
     * @return string
     */
    public function getReferenceKeyName()
    {
        return str_singular($this->name) . '_id';
    }

    /**
     * @return mixed
     */
    public function getCreateTimeColumn()
    {
        return $this->createTimeColumn;
    }

    /**
     * @return mixed
     */
    public function getUpdateTimeColumn()
    {
        return $this->updateTimeColumn;
    }

    public function getForeignColumn()
    {
        return array_keys($this->foreign);
    }

    public function isFlushUpdate()
    {
        return true === $this->getExtra('flush-update');
    }

    /**
     * Get table extra information by given $key
     *
     * @param $key
     * @param null $default
     * @return mixed
     */
    public function getExtra($key, $default = null)
    {
        return array_get($this->extra, $key, $default);
    }

    /**
     * Initiate table model if exist
     */
    public function model()
    {
        if (null !== ($class = $this->getModel())) {
            if (null !== $this->modelDir) {
                $class = $this->modelDir . '.' . $class;
            }
            return Wa::model($class);
        }
    }

    /**
     * @return mixed
     */
    public function getModel()
    {
        if (null === $this->model || true === $this->model) {
            return str_singular($this->name);
        } elseif (is_string($this->model)) {
            return $this->model;
        }
    }

    /**
     * @return mixed
     */
    public function getModelDir()
    {
        return $this->modelDir;
    }

    /**
     * @return null|ColumnInfo
     */
    public function getSequence()
    {
        return $this->sequence;
    }

    /**
     * Get table alias
     *
     * @return mixed
     */
    public function getAlias()
    {
        if (null === $this->alias) {
            $this->alias = str_replace(['a', 'i', 'u', 'e', 'o', '_', '-'], '', $this->name);
        }

        return $this->alias;
    }
}

