<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/5/2017
 * Time: 12:45 PM
 */

namespace Webarq\Model;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class NoModel extends Model
{
    /**
     * The name of the "created at" column.
     *
     * @var string
     */
    const CREATED_AT = null;

    /**
     * The name of the "updated at" column.
     *
     * @var string
     */
    const UPDATED_AT = null;

    /*
     |
     | Start of table of model modification
     | Be wise to use this, lad :)
     |
     */
    protected static $_table;

    protected static $_primaryKey = 'id';

    /**
     * Create NoModel instance
     *
     * @param string $table Table name
     * @param string $key Table primary key column name
     * @return static
     */
    public static function instance($table, $key = 'id')
    {
        $instance = new static;
        $instance->setTable($table);
        $instance->setKeyName($key);
        return $instance;
    }

    /**
     * Set model table
     *
     * @param string $table
     */
    public function setTable($table)
    {
        static::$_table = $table;
    }

    /**
     * Set table primary key
     *
     * @param string $key
     */
    public function setKeyName($key)
    {
        static::$_primaryKey = $key;
    }

    /**
     * Get model table
     *
     * @return mixed
     */
    public function getTable()
    {
        return static::$_table;
    }

    /**
     * Get table primary key
     *
     * @return string
     */
    public function getKeyName()
    {
        return static::$_primaryKey;
    }
    /*
     | End of table of model modification
     */

    /**
     * @param array $options
     * @return NoModel
     */
    public function optionQueryBuilder(array $options)
    {
        $model = clone $this;
        $model = $model->select(array_get($options, 'columns', '*'));
// Build limit query
        if (null !== ($var = array_get($options, 'limit'))) {
            if (is_array($var)) {
                $model->offset($var[0])->limit($var[1]);
            } else {
                $model->limit($var);
            }
        }

        if ([] !== ($var = array_get($options, 'where', []))) {
            $this->whereQueryBuilder($model, $var);
        }

        return $model;
    }

    /**
     * @param Builder $model
     * @param array|callable $where
     * @return $this
     */
    public function whereQueryBuilder(Builder $model, $where = [])
    {
        if (!is_string($where) && is_callable($where)) {
            $model->where($where);
        } elseif (is_array($where)) {
            foreach ($where as $column => $value) {
                if (is_array($value)) {
                    $model->whereIn($column, $value);
                } else {
                    $model->where($column, $value);
                }
            }
        }

        return $this;
    }

    public function sequenceQueryBuilder(Builder $model, $sequence = [])
    {
        if (!is_array($sequence)) {
            $sequence = explode(',', $sequence);
        }

        foreach ($sequence as $column => $dir) {
            if (is_numeric($column)) {
                if (false !== strpos($dir, ':')) {
                    list($column, $dir) = explode(':', $dir);
                } else {
                    $column = $dir;
                    $dir = 'asc';
                }
            }

            $model->orderBy($column, $dir);
        }

        return $this;
    }
}