<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 11/24/2016
 * Time: 6:31 PM
 */

namespace Webarq\Manager;

/**
 * This class will read given files which is exists in "config-module" directory.
 * To use this class, you should call it  by it is static "get" method, instead of
 * initiate it as a new object. If there is no matched item/file, than will return
 * given $default value
 *
 * But, when you needed the instances of ConfigManager it self, then you should set
 * $default in to true and "$path" value must be only valid file
 *
 * How to use:
 *   ConfigManager:get('coolFileName.coolKeyName')
 *
 * There is more, if you need to get multiple configuration value at once, separate
 * your {coolKeyName} with "," and this automatically prohibited the use of "," sign
 * in your configuration key name.
 *
 * Look at your code now, it would be transform into:
 *   ConfigManager::get('coolFileName.coolKeyName,awesomeKeyName,thirdKey');
 *
 * And for default value in case intended item not found could be assign by the next
 * parameter.
 * Voila, this is your final code:
 *   ConfigManager::get('coolFileName.coolKeyName,awesomeKeyName', {default value})
 *
 * Class ConfigManager
 * @package Webarq\Manager
 * @throws \Exception
 * @throws \Throwable
 */
class ConfigManager
{
    /**
     * Successfully ConfigManager instances
     *
     * @var array Successfully loaded object instances
     */
    protected static $instances = [];

    /**
     * Configurations item collections
     *
     * @var array Configuration collections
     */
    protected $collections = [];

    /**
     * Create ConfigManager instance
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
            $this->collections = include $file;
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
                abort(404, 'Before some installation, you need to publish configuration files,'
                        . 'by run "wa:publish modules" command');
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
            if (file_exists($file = $root . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $path) . '.php')
                    || [] === $path
            ) {
                if ([] !== $path) {
                    $instance = implode('.', $path);
                    if (!isset(self::$instances[$instance])) {
                        self::$instances[$instance] = new ConfigManager($file);
                    }

                    return self::determineReturn(self::$instances[$instance], implode('.', array_reverse($key)), $default);
                }
                return $default;
            } else {
                $key[] = array_pop($path);
            }
        }
    }

    /**
     * Determine configuration value that should return based on given path
     *
     * @param ConfigManager $manager
     * @param array $key
     * @param mixed $default
     * @return array|mixed|ConfigManager
     */
    protected static function determineReturn(ConfigManager $manager, $key, $default)
    {
        if (isset($key) && '' !== $key) {
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