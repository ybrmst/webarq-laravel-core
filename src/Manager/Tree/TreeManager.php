<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 2/13/2017
 * Time: 7:36 PM
 */

namespace Webarq\Manager\Tree;


class TreeManager
{
    /**
     * @var
     */
    protected $parent;

    /**
     * @var
     */
    protected $index;

    /**
     * @var array
     */
    protected $collections = [];

    /**
     * @param array $raw Indexed array of raw data
     * @param string $parent Parent column name
     * @param string $index Column to check
     */
    public function __construct(array $raw, $parent = 'parent_id', $index = 'id')
    {
        $this->index = $index;
        $this->parent = $parent;
    }

}