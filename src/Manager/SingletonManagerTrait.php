<?php
/**
 * Created by PhpStorm
 * Date: 19/10/2016
 * Time: 16:02
 * Author: Daniel Simangunsong
 *
 * Calm seas, never make skill full sailors
 */

namespace Webarq\Manager;


use Wa;

/**
 * trait SingletonManagerTrait
 * @package Webarq\Manager
 */
trait SingletonManagerTrait
{
    /**
     * @var Mixed
     */
    private static $instance;

    /**
     * @var array Object
     */
    private static $instances = array();

    /**
     * @param  string $name | array $configs
     * @param  array $configs, ...
     * @return SingletonManagerTrait|Mixed
     */
    public static function getInstance($name = null, $configs = [])
    {
        if (is_null($name)) {
            return new self();
        }
// Get arguments
        $args = func_get_args();
// Check for ghost parameter
        if (array_get($args, 1) === Wa::getGhost()) {
            $args = $args[0];
            $name = array_get($args, 0);
        }

// Name should be string
        if (is_array($name)) {
            $name = \File::name(get_called_class());
        }
        $class = get_called_class();

        if (!isset(self::$instances[$name]) || !self::$instances[$name] instanceof self) {
            switch(count($args)) {
                case 1:
                    $obj = new self($args[0]);
                    break;
                case 2 :
                    $obj = new self($args[0], $args[1]);
                    break;
                case 3 :
                    $obj = new self($args[0], $args[1], $args[2]);
                    break;
                case 4 :
                    $obj = new self($args[0], $args[1], $args[2], $args[3]);
                    break;
                case 5 :
                    $obj = new self($args[0], $args[1], $args[2], $args[3], $args[4]);
                    break;
                case 6 :
                    $obj = new self($args[0], $args[1], $args[2], $args[3], $args[4], $args[5]);
                    break;
                case 7 :
                    $obj = new self($args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6]);
                    break;
                default:
                    $obj = new \ReflectionClass($class);
                    $obj = $obj->newInstanceArgs($args);
                    break;
            }
            self::$instances[$name] = $obj;
        }

        return self::$instances[$name];
    }
}