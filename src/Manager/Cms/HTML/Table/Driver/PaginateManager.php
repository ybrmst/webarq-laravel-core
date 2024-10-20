<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/9/2017
 * Time: 4:01 PM
 */

namespace Webarq\Manager\Cms\HTML\Table\Driver;


use DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Wa;
use Webarq\Info\TableInfo;
use Webarq\Model\NoModel;

class PaginateManager extends AbstractManager
{

    /**
     * @var \Illuminate\Database\Query\Builder
     */
    protected $builder;

    /**
     * @var null|number
     */
    protected $limit;

    /**
     * @var LengthAwarePaginator
     */
    protected $get = false;

    /**
     * @var array
     */
    protected $sequence = [];

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var
     */
    protected $table;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * Create builder instance
     *
     * @param TableInfo $table
     * @param array $options
     * @param int|number $limit
     */
    public function __construct(TableInfo $table, array $options = [], $limit = 2)
    {
        $this->table = $table;
        $this->limit = $limit;
        $this->options = $options;

        $columns = $this->getColumns();

        $joins = $keys = [];

        foreach ($columns as $i => &$name) {
// This is a relation object
            if (Str::startsWith($name, '.')) {
                unset($columns[$i]);
                continue;
            }
// When column name containing "dot" sign it will be assuming that the columns belong to another table,
// which is string before the "dot" sign in this case, therefore need to collect join information
            if (str_contains($name, '.')) {
// Separate table name from column name
                list($t1, $n1) = explode('.', $name);
// Join information
                $joins[$t1][] = $n1;
// On information
                if (isset($this->options[$name]['on'])) {
                    $keys[$t1] = $this->options[$name];
                }
                unset($columns[$i]);
            } else {
                $name = $this->table->getAlias() . '.' . $name;
            }
        }

// Load respected model class
        if (null === ($model = Wa::model(str_singular($table->getName())))) {
            $model = NoModel::instance($table->getName(), $table->primaryColumn()->getName());
        }

        if (!method_exists($model, 'cmsDataListingController')) {
            $this->builder = $model
                    ->from($table->getName() . ' as ' . $table->getAlias())
                    ->select([] === $columns ? '*' : $columns)
                    ->addSelect($table->getAlias() . '.' . $table->primaryColumn()->getName());

            $this->buildJoin($joins, $keys);
        } else {
            $this->builder = $model->{'cmsDataListingController'}();
        }
    }

    protected function getColumns()
    {
        $columns = [];

        foreach ($this->options as $key => $setting) {
            $columns[] = is_numeric($key) ? $setting : $key;
        }

        return $columns;
    }

    protected function buildJoin(array $options, array $keys = [])
    {
        if ([] !== $options) {
//            Alias collection
            $aliases = [];
            foreach ($options as $table => $columns) {
                list($object, $alias) = $this->getTableAlias($table);

                if (null !== $object) {
//                    Push the alias
                    $aliases[] = $alias;
                    $op = '=';

                    if (isset($keys[$table]['on'])) {
                        $l = count($keys[$table]['on']);
                        if (2 === $l) {
                            list($lc, $rc) = $keys[$table]['on'];
                        } elseif (3 == $l) {
                            list($lc, $op, $rc) = $keys[$table]['on'];
                        } else {
                            abort(500, 'Please check your setting');
                        }
                    } elseif (Str::isStartsWith($this->table->getName(), $object->getName())) {
                        $lc = $alias . '.' . $object->primaryColumn()->getName();
                        $rc = $this->table->getAlias() . '.' . $object->getReferenceKeyName();
                    } else {
                        $lc = $this->table->getAlias() . '.' . $this->table->primaryColumn()->getName();
                        $rc = $alias . '.' . $this->table->getReferenceKeyName();
                    }

                    $this->builder->join($object->getName() . ' as ' . $alias, $lc, $op, $rc);

                    foreach ($columns as $name) {
                        $this->builder->addSelect($alias . '.' . $name);
                    }
                } elseif (in_array($table, $aliases)) {
                    foreach ($columns as $name) {
                        $this->builder->addSelect($table . '.' . $name);
                    }
                }
            }
        }
    }

    /**
     * @param $table
     * @return array
     */
    protected function getTableAlias($table)
    {
        $items = explode(' as ', $table);
        $table = Wa::table($items[0]) ?: Wa::table(str_plural($items[0]));

        if (null !== $table) {
            $items[0] = $table;

            if (!isset($items[1])) {
                $items[1] = $table->getAlias();
            }

            return $items;
        }

        return [null, null];
    }

    /**
     * @param $sequences
     * @return $this
     */
    public function buildSequence($sequences)
    {
        if (!is_array($sequences)) {
            $sequences = [$sequences];
        }

        foreach ($sequences as $column => $direction) {
            if (is_numeric($column)) {
                $column = $direction;
                $direction = 'asc';
            }

            if (!str_contains($column, '.')) {
                $column = $this->table->getAlias() . '.' . $column;
            }

            $this->builder->orderBy($column, $direction);
        }

        return $this;
    }

    /**
     * @param array $columns
     * @param $query
     * @return $this
     */
    public function buildSearch(array $columns, $query)
    {
        if (null !== $query && '' !== trim($query)) {
            $method = 'orWhere';
            if (1 === (int)last($columns) || true === last($columns)) {
                array_pop($columns);
                $method = 'where';
            }

            foreach ($columns as $column) {
                if (is_array($column)) {

                } else {
                    if (false !== strpos($column, '.')) {
                        list($t1, $n1) = explode('.', $column);
                        $t1 = trim($t1);
// Get table manager
                        $object = Wa::table($t1) ?: Wa::table(str_plural($t1));

                        $column = (null !== $object ? $object->getAlias() : $t1) . '.' . trim($n1);

                        if (null !== $t1) {

                        } else {
                            continue;
                        }
                    } else {
                        $column = $this->table->getAlias() . '.' . trim($column);
                    }

                    $this->builder->{$method}(trim($column), 'like', '%' . $query . '%');
                }
            }
        }

        return $this;
    }

    /**
     * @param $where
     */
    public function buildWhere($where)
    {
        if (is_callable($where) && !is_string($where)) {
            $where($this->builder);
        } elseif (is_array($where)) {
            foreach ($where as $column => $value) {
                if (is_array($value)) {
                    $this->builder->whereIn($column, $value);
                } else {
                    $this->builder->where($column, $value);
                }
            }
        }
    }

    /**
     * @param null|string $view
     * @param array $queries
     * @return string
     */
    public function paginate($view = null, array $queries = [])
    {
// Only show pagination when it is more than 1 page
        if ($this->get instanceof LengthAwarePaginator) {
            $get = $this->get;

            if ([] !== $queries) {
                foreach ($queries as $key => $value) {
                    if (is_numeric($key)) {
                        $key = $value;
                        $value = \Request::input($key);
                    }

                    if (!empty($value)) {
                        $get->addQuery($key, $value);
                    }
                }
            }

            return $get->render($view);
        }
    }

    /**
     * @inheritdoc
     */
    public function getRows()
    {
        if (false === $this->get) {
            $this->get = !empty($this->limit) ? $this->builder->paginate($this->limit) : $this->builder->get();

            if ($this->get->count()) {
                return $this->get;
            }

            return [];
        }

        return $this->get;
    }
}