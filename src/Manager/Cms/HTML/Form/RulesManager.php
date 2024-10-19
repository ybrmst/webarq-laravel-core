<?php
/**
 * Created by PhpStorm
 * Date: 30/12/2016
 * Time: 12:51
 * Author: Daniel Simangunsong
 *
 * Calm seas, never make skill full sailors
 */

namespace Webarq\Manager\Cms\HTML\Form;


use Illuminate\Support\Arr;
use Request;

/**
 * Class RulesManager
 *
 * Generate input rules from input attributes
 *
 * @package Webarq\Manager\Cms\HTML\Form
 */
class RulesManager
{
    /**
     * Input rules item
     *
     * @var array
     */
    protected $items = [];

    /**
     * Input attributes
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Input value
     *
     * @var mixed
     */
    protected $value;

    /**
     * @var object Webarq\Info\ColumnInfo
     */
    protected $column;

    /**
     * @var object Webarq\Info\TableInfo
     */
    protected $table;

    /**
     * Editing row id
     *
     * @var number
     */
    protected $rowId;

    /**
     * Create RulesManager instance
     *
     * Calling all methods which are ended with "Rule" string
     *
     * @param array $attributes
     */
    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
        $this->table = array_get($this->attributes, 'table');
        $this->column = array_get($this->attributes, 'column');

        $this->whileAttr(array_pull($attributes, 'while', []));

        $this->finalize();
    }

    /**
     * Checking for attribute while some post value matched
     *
     * @param array $condition
     */
    protected function whileAttr(array $condition)
    {
        if ([] !== $condition) {
            foreach ($condition as $input => $setting) {
                foreach ($setting as $value => $rule) {
                    if ($this->modifyValue($value) == $this->modifyValue(Request::input($input))) {
                        $this->attributes = Arr::merge($this->attributes, $rule);
                    }
                }
            }
        }
    }

    /**
     * @param $value
     * @return mixed
     */
    protected function modifyValue($value)
    {
        if (is_numeric($value)) {
            return (int)$value;
        } elseif (is_string($value) && '' !== trim($value)) {
            return strtolower($value);
        }

        return $value;
    }

    /**
     * Finalize collected rule, do auto correction
     */
    protected function finalize()
    {
        foreach (get_class_methods($this) as $method) {
            if ('Rule' === substr($method, -4)) {
                $this->{$method}();
            }
        }

// Check if rules attributes is given
        if (null !== ($rule = array_get($this->attributes, 'rules'))) {
            $items = explode('|', $rule);
            foreach ($items as $item) {
                $item = explode(':', $item, 2);
                $this->items[$item[0]] = array_get($item, 1, '');
            }
        }
// @todo find an eloquent way to handle input array validation
        if (isset($this->attributes['multiple']) || Arr::inArray($this->attributes, 'multiple')) {

            foreach (['numeric', 'integer', 'max', 'min'] as $key) {
                if (null !== ($x = array_pull($this->items, $key))) {
                    $this->items[$key . 'Array'] = $x;
                }
            }
        }
    }

    /**
     * Rule item getter
     *
     * @param $key
     * @param null $default
     * @return mixed
     */
    public function getItem($key, $default = null)
    {
        return array_get($this->items, $key, $default);
    }

    /**
     * Rule item setter
     *
     * @param $key
     * @param $str
     */
    public function setItem($key, $str)
    {
        $this->items[$key] = $str;
    }

    /**
     * @param mixed $value
     * @param number $rowId
     * @return $this
     */
    public function setValue($value, $rowId)
    {
        $this->value = $value;

        $this->setRowId($rowId);

        $this->finalize();

        return $this;
    }

    /**
     * @return number
     */
    public function getRowId()
    {
        return $this->rowId;
    }

    /**
     * Set row id
     *
     * @param $id
     * @return $this
     */
    public function setRowId($id)
    {
        $this->rowId = $id;

        return $this;
    }

    /**
     * Convert rule items into laravel string format
     *
     * @param string $separator
     * @return string
     */
    public function toString($separator = '|')
    {
        $string = 'bail';
        foreach ($this->items as $key => $option) {
            $string .= $separator . (is_numeric($key) ? '' : $key);
            if ('' !== $option) {
                if (!is_numeric($key)) {
                    $string .= ':';
                }
                $string .= $option;
            }
        }
        return trim($string, $separator);
    }

    /**
     * Collect require rule
     */
    protected function requireRule()
    {
        if (true === $this->getAttribute('notnull')) {
            $this->items['required'] = '';
        }
    }

    /**
     * Get attribute item by given key
     *
     * @param $key
     * @param null $default
     * @return mixed
     */
    public function getAttribute($key, $default = null)
    {
        return array_get($this->attributes, $key, $default);
    }

    /**
     * Collect numeric rule
     */
    protected function numericRule()
    {
        if (null !== $this->getAttribute('numeric')
                || str_contains($this->getAttribute('db-type', 'not-found'), 'int')
        ) {
            $this->items['numeric'] = '';
        }
    }

    /**
     * Collect max rule
     */
    protected function maxRule()
    {
        if (null !== ($max = $this->getAttribute('max', $this->getAttribute('length'))) && is_numeric($max)) {
            $this->items['max'] = $max;
        }
    }

    /**
     * Collect min rule
     */
    protected function minRule()
    {
        if (null !== ($min = $this->getAttribute('min')) && is_numeric($min)) {
            $this->items['min'] = $min;
        }
    }

    /**
     * Collect unique rule
     */
    protected function uniqueRule()
    {
        if (null !== ($unique = $this->getAttribute('unique'))) {
            if (true === $unique) {
                $unique = $this->getAttribute('table')->getName() . ',' . $this->getAttribute('column')->getName();
// Set param value in to rule string
//                if (null !== $this->value) {
//                    $unique .= ',' . $this->value;
//                }
// Set param row id in to rule string
                if (null !== $this->rowId) {
                    $unique .= ',' . $this->rowId;
                }
            }

            $this->items['unique'] = $unique;
        }
    }

    /**
     * Collect uniques rule
     */
    protected function uniquesRule()
    {

    }

    protected function fileRule()
    {
        if (null !== ($files = $this->getAttribute('file')) && is_array($files)) {
            array_forget($files, ['upload-dir', 'resize', 'prefix', 'file-name', 'preview']);

            foreach ($files as $key => $value) {
                if (is_numeric($key) || 'type' === $key) {
                    $this->items[] = $value;
                } else {
                    $this->items[$key] = (is_array($value) ? implode(',', $value) : $value);
                }
            }

            if (0 === (int)array_get($this->items, 'max')) {
                unset($this->items['max']);
            }
        }
    }

    protected function existenceRule()
    {
        if (null !== ($option = $this->getAttribute('existent'))
                && null !== ($max = $this->getAttribute('existent.' . $this->value))
        ) {
            $this->items['existent'] = $this->getAttribute('table')->getName()
                    . ',' . (int)$max
                    . ',' . $this->rowId;
        }
    }
}