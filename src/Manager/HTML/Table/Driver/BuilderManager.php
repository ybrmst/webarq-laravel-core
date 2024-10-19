<?php
/**
 * Created by PhpStorm
 * Date: 08/01/2017
 * Time: 18:56
 * Author: Daniel Simangunsong
 *
 * Calm seas, never make skill full sailors
 */

namespace Webarq\Manager\HTML\Table\Driver;


use DB;

class BuilderManager extends DriverAbstractManager
{
    /**
     * @var \Illuminate\Database\Query\Builder
     */
    protected $builder;

    /**
     * Create builder instance
     *
     * @param string $table
     * @param array $columns
     * @param null|number $limit
     * @param null|number $offset
     */
    public function __construct($table, $columns = [], $limit = null, $offset = null)
    {
        $this->builder = DB::table($table)->select($columns);

        if (is_numeric($limit)) {
            $this->builder->limit($limit);
        }

        if (is_numeric($offset)) {
            $this->builder->offset($offset);
        }
    }

    /**
     * @return \Illuminate\Database\Query\Builder
     */
    public function builder()
    {
        return $this->builder;
    }

    /**
     * @inheritdoc
     */
    public function getRows()
    {
        $get = $this->builder->get();

        if ($get->count()) {
            $get = $get->toArray();

            foreach ($get as &$item) {
                $item = (array) $item;
            }

            return $get;
        }

        return [];
    }
}