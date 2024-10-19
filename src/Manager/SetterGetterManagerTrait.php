<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 11/11/2016
 * Time: 3:33 PM
 */

namespace Webarq\Manager;


use Illuminate\Support\Str;

/**
 * Class SetterGetterManagerTrait
 * @package Webarq\Manager
 */
trait SetterGetterManagerTrait {
    /**
     * @var array
     */
    protected $getter = [];

    /**
     * @var array
     */
    protected $setter = [];

    /**
     * @param string $key
     */
    public function __get($key)
    {
        if ([] !== $this->getter && in_array($key,$this->getter))
        {
            return $this->{$key};
        }
        elseif (method_exists($this,($method = Str::camel('get ' . $key))))
        {
            return $this->{$method}();
        }
        else
        {
            abort(500, 'Inaccessible ' . $key . ' property');
        }
    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function __set($key,$value)
    {
        if ([] !== $this->setter && in_array($key,$this->setter))
        {
            $this->{$key} = $value;
        }
        elseif (method_exists($this,($method = camel_case('set ' . $key))))
        {
            $this->{$method}($value);
        }
        else
        {
            abort(500, 'Inaccessible ' . $key . ' property');
        }
    }
}