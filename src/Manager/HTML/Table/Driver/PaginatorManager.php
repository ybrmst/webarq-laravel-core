<?php
/**
 * Created by PhpStorm
 * Date: 08/01/2017
 * Time: 22:09
 * Author: Daniel Simangunsong
 *
 * Calm seas, never make skill full sailors
 */

namespace Webarq\Manager\HTML\Table\Driver;


use DB;
use Illuminate\Pagination\LengthAwarePaginator;

class PaginatorManager extends DriverAbstractManager
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
    protected $get;

    /**
     * Create builder instance
     *
     * @param string $table
     * @param array $columns
     * @param int|number $limit
     */
    public function __construct($table, $columns = [], $limit = 2)
    {
        $this->builder = DB::table($table)->select($columns);

        $this->limit = $limit;
    }

    /**
     * @inheritdoc
     */
    public function sampling()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getRows()
    {
        $this->get = $this->builder->paginate($this->limit);

        if ($this->get->count()) {
            $data = $this->get->toArray();
            foreach ($data['data'] as &$item) {
                $item = (array) $item;
            }

            return $data['data'];
        }

        return [];
    }

    /**
     * @param null|string $view
     * @return string
     */
    public function paginator($view = null)
    {
        if ($this->get instanceof LengthAwarePaginator) {
            return $this->get->render($view);
        }
    }
}