<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/18/2017
 * Time: 3:47 PM
 */

namespace Webarq\Manager\Cms\Query;


use Illuminate\Database\Eloquent\Model;
use Wa;
use Webarq\Info\TableInfo;
use Webarq\Manager\AdminManager;
use Webarq\Model\NoModel;

abstract class QueryManager
{
    protected $admin;

    /**
     * @var array
     */
    protected $post = [];

    /**
     * Transaction type, create|edit
     *
     * @var
     */
    protected $formType;

    /**
     * Transaction master table
     *
     * @var string
     */
    protected $master;

    /**
     * @var
     */
    protected $model;

    /**
     * @param AdminManager $admin
     * @param array $post
     * @param null $master
     * @param Model|null $model
     */
    public function __construct(AdminManager $admin, array $post, $master = null, Model $model = null)
    {
        $this->admin = $admin;
        $this->post = $post;
        $this->model = $model;

        $this->setMaster($master);
    }

    /**
     * @param $master
     */
    protected function setMaster($master)
    {
        if (null === $master && [] !== $this->post) {
            foreach ($this->post as $table => $rows) {
                if (null === $master || 0 === strpos($master, str_singular($table))) {
                    $master = $table;
                }
            }
        }

        $this->master = $master;
    }

    /**
     * @param TableInfo $table
     * @param array $row
     */
    protected function addCreateTime(TableInfo $table, array &$row = [])
    {
        if (null !== $table->getCreateTimeColumn() && !isset($row[$table->getCreateTimeColumn()])) {
            $row[$table->getCreateTimeColumn()] = date('Y-m-d H:i:s');
        }
    }

    /**
     * @param TableInfo $table
     * @param array $row
     * @return array
     */
    protected function addUpdateTime(TableInfo $table, array &$row = [])
    {
        if (null !== $table->getUpdateTimeColumn() && !isset($row[$table->getUpdateTimeColumn()])) {
            $row[$table->getUpdateTimeColumn()] = date('Y-m-d H:i:s');
        }
    }

    protected function initiateModel($table, $primary = null)
    {
        return NoModel::instance($table, $primary ?: Wa::table($table)->primaryColumn()->getName());
    }

    protected function rowBinder(Model $model, array $row)
    {
        foreach ($row as $column => $value) {
            $model->{$column} = $value;
        }
    }

    /**
     * @param Model $model
     * @param array $where
     * @return Model
     */
    protected function buildWhere(Model $model, array $where)
    {
        foreach ($where as $column => $value) {
            if (is_array($value)) {
                if (is_numeric($column)) {
                    $model = $model->where(function ($query) use ($value) {
                        foreach ($value as $key => $str) {
                            if (is_array($str)) {
                                $query->orWhereIn($key, $str);
                            } else {
                                $query->orWhere($key, $str);
                            }
                        }
                    });
                } else {
                    $model = $model->whereIn($column, $value);
                }
            } else {
                $model = $model->where($column, $value);
            }
        }

        return $model;
    }
}