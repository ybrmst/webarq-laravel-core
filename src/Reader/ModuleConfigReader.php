<?php
/**
 * Created by PhpStorm
 * Date: 18/02/2017
 * Time: 15:56
 * Author: Daniel Simangunsong
 *
 * Calm seas, never make skill full sailors
 */

namespace Webarq\Reader;

/**
 * This class will read given files which is exists in "config-module" directory.
 * To use this class, you should call it  by it is static "get" method, instead of
 * initiate it as a new object. If there is no matched item/file, than will return
 * given $default value
 *
 * But, when you needed the instances of ModuleConfigReader it self, then you should set
 * $default in to true and "$path" value must be only valid file
 *
 * How to use:
 *   ModuleConfigReader:get('coolFileName.coolKeyName')
 *
 * There is more, if you need to get multiple configuration value at once, separate
 * your {coolKeyName} with "," and this automatically prohibited the use of "," sign
 * in your configuration key name.
 *
 * Look at your code now, it would be transform into:
 *   ModuleConfigReader::get('coolFileName.coolKeyName,awesomeKeyName,thirdKey');
 *
 * And for default value in case intended item not found could be assign by the next
 * parameter.
 * Voila, this is your final code:
 *   ModuleConfigReader::get('coolFileName.coolKeyName,awesomeKeyName', {default value})
 *
 * Class ModuleConfigReader
 * @package Webarq\Manager
 * @throws \Exception
 * @throws \Throwable
 */
class ModuleConfigReader
{
    /**
     * Successfully loaded ModuleConfigReader
     *
     * @var array
     */
    protected static $instances = [];

    /**
     * Configurations item collections
     *
     * @var array Configuration collections
     */
    protected $collections = [];

    /**
     * Create ModuleConfigReader instance
     *
     * @param $file
     */
    public function __construct($file)
    {
        $this->read($file);
    }

    /**
     * Read file
     *
     * @param $file
     */
    protected function read($file)
    {
        if (is_file($file)) {
            $read = (include_once $file);

            if (is_array($read)) {
                $this->collections = $read;
            }
        }
    }

    /**
     * Get configuration value by given $path
     *
     * @param $path
     * @param mixed $default
     * @return null
     * @throws \Exception
     * @throws \Throwable
     */
    public static function get($path, $default = null)
    {
// Determine root configuration directory
        $root = app_path() . DIRECTORY_SEPARATOR
                . '..' . DIRECTORY_SEPARATOR
                . 'modules';
        if (!is_dir($root)) {
            if ('local' !== getenv('APP_ENV')) {
                return [];
            } else {
                $root = __DIR__ . DIRECTORY_SEPARATOR
                        . '..' . DIRECTORY_SEPARATOR
                        . '..' . DIRECTORY_SEPARATOR
                        . 'modules';
            }
        }
// Check for manager instance, before load a new one
        if (isset(self::$instances[$path])) {
            return self::determineReturn(self::$instances[$path], null, $default);
        }

// Array path file
        $path = explode('.', $path);
        $key = [];
        while (true) {
// Found a configuration file
            if (file_exists($file = $root . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $path) . '.php')) {
                $instance = implode('.', $path);
                if (!isset(self::$instances[$instance])) {
                    self::$instances[$instance] = new ModuleConfigReader($file);
                }

                return self::determineReturn(self::$instances[$instance], implode('.', array_reverse($key)), $default);
            } elseif ([] !== $path) {
                $key[] = array_pop($path);
            } else {
                return $default;
            }
        }
    }

    /**
     * Determine configuration value that should return based on given path
     *
     * @param ModuleConfigReader $manager
     * @param array $key
     * @param mixed $default
     * @return array|mixed|ModuleConfigReader
     */
    protected static function determineReturn(ModuleConfigReader $manager, $key, $default)
    {
        if (null !== $key && '' !== $key) {
            return $manager->getCollection($key, $default);
        } elseif (true !== $default) {
            return $manager->getCollections();
        }
        return $manager;
    }

    /**
     * Get collection value by $key
     *
     * @param $key
     * @param null $default
     * @return mixed
     */
    public function getCollection($key, $default = null)
    {
        if (str_contains($key, ',')) {
            $root = '';
            if (false !== ($dot = strrpos($key, '.'))) {
                $root = substr($key, 0, $dot) . '.';
                $key = substr($key, $dot + 1);
            }
            $tmp = [];
            foreach (explode(',', $key) as $index) {
                $tmp[$index] = array_get($this->collections, $root . $index, $default);
            }
            return $tmp;
        } else {
            return array_get($this->collections, $key, $default);
        }
    }

    /**
     * Get collections
     *
     * @return array
     */
    public function getCollections()
    {
        return $this->collections;
    }

    /**
     * Set collection
     *
     * @param $key
     * @param $value
     */
    public function setCollection($key, $value)
    {
        array_set($this->collections, $key, $value);
    }
}