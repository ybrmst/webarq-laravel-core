<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 11/23/2016
 * Time: 6:27 PM
 */

namespace Webarq\Manager;


/**
 * Wa::crud('insert', $post, function($post){
 *      $post->updateData('someKey', 'someValue');
 * })
 *
 * Class CRUDManager
 * @package Webarq\Manager
 */
class QueryManager
{
    /**
     * Row data
     *
     * @var array
     */
    protected $row = [];

    /**
     * Update data object value
     *
     * @param $key
     * @param $value
     * @param null|string $modifier Modifier method name
     */
    public function updateRow($key, $value, $modifier = null)
    {
        if (isset($modifier)) {

        }
        array_set($this->row, $key, $value);
    }

    public function finalizeRow()
    {

    }
}