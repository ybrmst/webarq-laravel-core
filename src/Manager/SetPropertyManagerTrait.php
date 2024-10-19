<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 12/20/2016
 * Time: 10:16 AM
 */

namespace Webarq\Manager;


use Wa;

/**
 * Assign value into class property by pulling out their value from given options
 *
 * Class setPropertyManagerTrait
 * @package Webarq\Manager
 */
trait SetPropertyManagerTrait
{
    /**
     * @param array $options
     * @param bool|false $correction
     */
    protected function setPropertyFromOptions(array &$options = [], $correction = false)
    {
        if (true === $correction) {
            foreach ($options as $key => $value) {
                if (is_numeric($key)) {
// Remove numeric key member
                    unset($options[$key]);
                    $key = $value;
                }
                $options[$key] = $value;
            }
        }

        if ([] !== $options) {
            if ([] !== ($vars = get_class_vars(get_called_class()))) {
                foreach (array_keys($vars) as $key) {
                    $value = array_pull($options, snake_case($key, '-'), Wa::getGhost());
                    if (Wa::getGhost() !== $value) {
                        $this->{$key} = $value;
                    }
                }
            }
        }
    }
}