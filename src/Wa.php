<?php
/**
 * Created by PhpStorm
 * Date: 19/10/2016
 * Time: 13:39
 * Author: Daniel Simangunsong
 *
 * Calm seas, never make skill full sailors
 */

namespace Webarq;


use DB;
use File;
use Illuminate\Support\Str;
use Webarq\Info\ModuleInfo;
use Webarq\Info\TableInfo;
use Webarq\Reader\ModuleConfigReader;

/**
 * Helper class. This is break the SOLID Principals but i can not do anything about that. I think this is the most
 * suitable way
 *
 * Class Wa
 * @package Webarq
 */
class Wa
{
    protected $instances = [];

    protected $space = 'Webarq';

    /**
     * Shortcut to load some module configuration
     * Even it is rare, we able to translate our configuration, just prefix the key name with trans::
     *
     * @param $key
     * @param null|mixed $default
     * @return mixed
     */
    public function config($key, $default = null)
    {
        $trans = false;

        if (starts_with($key, 'trans::')) {
            $key = substr($key, 7);

            $trans = true;
        }

        $key = explode('.', $key, 2);

        if (null !== ($module = $this->module($key[0]))) {
            if ($trans && class_exists('Wl')) {
                $k = array_get($key, 1) . '_' . app()->getLocale();
                $v = $module->getConfig($k, $default);
                if (app()->getLocale() !== \Wl::getSystem() && '' === $v) {
                    return $module->getConfig(array_get($key, 1) . '_' . \Wl::getSystem(), $default);
                }

                return $v;
            }

            return $module->getConfig(array_get($key, 1), $default);
        }
    }

    /**
     * Load config-module configuration
     *
     * @param $name
     * @return object Webarq\Info\ModuleInfo
     */
    public function module($name)
    {
        if (in_array($name, $this->modules())) {
            return ModuleInfo::getInstance($name, ModuleConfigReader::get($name, []));
        }
    }

    /**
     * Get config modules
     *
     * @return array
     */
    public function modules()
    {
        return config('webarq.modules', []);
    }

    /**
     * @param string $name
     * @return null|object
     */
    public function model($name)
    {
        return $this->load('model.' . str_replace('_', ' ', $name));
    }

    /**
     * Load new given class name. To load class without normalize class name, set "false" as first parameter,
     * following by intended parameter,
     *
     * @param string|false $class Class name
     * @param null|mixed $args [, mixed $args ...]
     * @return object|null
     */
    public function load($class, $args = null)
    {
        $args = func_get_args();
// Remove class argument
        array_shift($args);
        if (false === $class) {
            $class = array_shift($args);
        } else {
            $class = $this->normalizeClass($class);
        }

        if ($this->getGhost() === array_get($args, 1)) {
            $args = $args[0];
        }

// Prioritize app layer
        if (file_exists(app_path(str_replace('\\', DIRECTORY_SEPARATOR, $class)) . '.php') && class_exists('App\\' . $class)) {
            $class = 'App\\' . $class;
        } elseif (!class_exists($class)) {
            return null;
        }

        switch (count($args)) {
            case 0:
                return new $class();
            case 1:
                return new $class($args[0]);
            case 2:
                return new $class($args[0], $args[1]);
            case 3:
                return new $class($args[0], $args[1], $args[2]);
            case 4:
                return new $class($args[0], $args[1], $args[2], $args[3]);
            case 5:
                return new $class($args[0], $args[1], $args[2], $args[3], $args[4]);
            case 6:
                return new $class($args[0], $args[1], $args[2], $args[3], $args[4], $args[5]);
            default:
                $o = new \ReflectionClass($class);
                $i = $o->newInstanceArgs($args);
                return $i;
        }
    }

    /**
     * Normally all class using studly case string format
     * ".", "\", and "/" will be use as directory separator
     *
     * @param string $path
     * @return string Full class path
     */
    public function normalizeClass($path)
    {
        $s = $path;
        $path = str_replace(['\\', '/', '_'], '.', $path);
        $path = str_replace('-', ' ', $path);
        $path = explode('.', $path);
        if (count($path) > 1) {
            $path = $this->compilePathName($path);
            $class = implode('\\', $path);
// Suffixed class with root name space
            if (!ends_with($class, '$')) {
                $class .= $path[0];
            } else {
                $class = substr($class, 0, -1);
            }
        } else {
            $class = studly_case(current($path));
        }

        return $this->space . '\\' . $class;
    }

    private function compilePathName(array $path)
    {
        foreach ($path as &$item) {
// Do not modified item value
            if (ends_with($item, '!')) {
                $item = substr($item, 0, -1);
            } else {
                $item = studly_case($item);
            }
        }
        return $path;
    }

    public function getGhost()
    {
        return config('webarq.system.ghost');
    }

    /**
     * Manager class loader
     *
     * @param $class
     * @param array $args , ... [$args]
     * @return object
     */
    public function manager($class, $args = [])
    {
        $args = func_get_args();
// Remove class argument
        array_shift($args);
        return $this->load('manager.' . $class, $args, $this->getGhost());
    }

    public function element($content, $container, array $attr = [])
    {
        if (is_bool($attr)) {
            $attr = [];
        }
        return $this->html('element', $content, $container, $attr)->toHtml();
    }

    /**
     * HTML Manager class loader
     *
     * @param $class
     * @param array $args , ... [$args]
     * @return object
     */
    public function html($class, $args = [])
    {
        $args = func_get_args();
// Remove class argument
        array_shift($args);
        return $this->load('manager.HTML!.' . $class, $args, $this->getGhost());
    }

    /**
     * Formatted permission into acceptable format
     *
     * @param array $permissions
     * @param $module
     * @param $panel
     * @return array
     */
    public function formatPermissions($permissions = [], $module, $panel)
    {
// Permissions should be array
        if (!is_array($permissions)) {
            $permissions = [$permissions];
        }

        foreach ($permissions as &$permission) {
            if (is_bool($permission)) {
                continue;
            }
            $permission = trim($permission, '.');
            switch (substr_count($permission, '.')) {
                case 1 :
                    $permission = $module . '.' . $permission;
                    break;
                case 0 :
                    $permission = $module . '.' . $panel . '.' . $permission;
                    break;
            }
        }

        return $permissions;
    }

    /**
     * Shortcut for calling modifier manager
     *
     * @param mixed $pattern
     * @param mixed $value
     * @return mixed
     */
    public function modifier($pattern = null, $value = null)
    {
        if (!is_string($pattern) && is_callable($pattern)) {
            return $pattern($value);
        }

        $class = $this->instance('manager.value modifier');

        if (null !== $pattern) {
            $pattern = trim($pattern);
            $options = explode('|', $pattern);

            foreach ($options as $pattern) {
                $params = explode(':', $pattern);

                $method = array_pull($params, 0);
                array_unshift($params, $value);

                if (is_callable($method)) {
                    try {
                        $value = call_user_func_array($method, $params);
                    } catch (\Exception $e) {
                        $value = strip_tags($value);
                    }
                } elseif (method_exists(new Str(), $method)) {
                    $value = call_user_func_array(['Illuminate\Support\Str', $method], $params);
                } elseif ($class->hasMacro($method)) {
                    $value = call_user_func_array([$class, $method], $params);
                } else {
                    try {
                        list($class, $method) = explode('.', $method);

                        $value = app($class)->{$method}(... $params);
                    } catch (\Exception $e) {
                    }
                }
            }

            return $value;
        }

        return $class;
    }

    /**
     * Load class instance
     *
     * @param $class
     * @param mixed $arg [,... $arg, $arg] Uncountable argument
     * @return mixed
     */
    public function instance($class, $arg = [])
    {
// Get loaded class
        if (isset($this->instances[$class])) {
            return $this->instances[$class];
        } else {
// Load new instances
// Get actual arguments
            $args = func_get_args();
// Remove class argument
            array_shift($args);
            if ($this->getGhost() === array_get($args, 1)) {
                $args = $args[0];
            }
// Get class full path
            $fp = $this->normalizeClass($class);
// Prioritize app layer
            if (file_exists(app_path(str_replace('\\', DIRECTORY_SEPARATOR, $fp)) . '.php')
                    && class_exists('App\\' . $fp)
            ) {
                $fp = 'App\\' . $fp;
            }

            if (class_exists($fp)) {
                if (method_exists($fp, 'getInstance')) {
                    $this->instances[$class] = $fp::getInstance($args, $this->getGhost());
                } else {
                    $this->instances[$class] = $this->load(false, $fp, $args, $this->getGhost());
                }
                return $this->instances[$class];
            } else {
                abort(500, 'Class ' . $class . ' not found on this system');
            }
        }
    }

    /**
     * Shortcut for calling cms panel manager
     *
     * @return mixed
     */
    public function panel()
    {
        return $this->instance('manager.cms.panel', \Auth::user());
    }

    /**
     * @param $themes
     * @param $view
     * @param bool|true $object
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getThemesView($themes, $view, $object = true)
    {
        if (view()->exists('vendor.webarq.themes.' . $themes . '.' . $view)) {
            $path = 'vendor.webarq.themes.' . $themes . '.' . $view;
        } elseif (view()->exists('vendor.webarq.themes.default.' . $view)) {
            $path = 'vendor.webarq.themes.default.' . $view;
        } elseif (view()->exists('webarq::themes.' . $themes . '.' . $view)) {
            $path = 'webarq::themes.' . $themes . '.' . $view;
        } elseif (view()->exists('webarq::themes.default.' . $view)) {
            $path = 'webarq::themes.default.' . $view;
        } else {
            return view($themes . '.' . $view);
        }

        return $object ? view($path) : $path;
    }

    /**
     * @param $str
     * @param array $parameters
     * @return string|\Symfony\Component\Translation\TranslatorInterface
     */
    public function trans($str, $parameters = [])
    {
        $trans = trans($str, $parameters);
        if ($str === $trans) {
            if (false !== strpos($str, '.')) {
                $str = trim(strrchr($str, '.'), '.');
            }
            $trans = title_case(str_replace(['_', '-'], ' ', $str));
        }
        return $trans;
    }

    /**
     * Copy a directory from one location to another.
     * Cloning the code from laravel copyDirectory but change the third parameter in to force option
     *
     * @param  string $directory
     * @param  string $destination
     * @param  bool $force
     * @return bool
     */
    public function copyDirectory($directory, $destination, $force = false)
    {
        if (File::isDirectory(($directory))) {
            return File::copyDirectory($directory, $destination);
        } elseif (!File::isFile($destination) || $force) {
            return File::copy($directory, $destination);
        } elseif (!File::isDirectory($directory)) {
            return false;
        }

        $options = \FilesystemIterator::SKIP_DOTS;

        // If the destination directory does not actually exist, we will go ahead and
        // create it recursively, which just gets the destination prepared to copy
        // the files over. Once we make the directory we'll proceed the copying.
        if (!File::isDirectory($destination)) {
            File::makeDirectory($destination, 0777, true);
        }

        $items = new \FilesystemIterator($directory, $options);

        foreach ($items as $item) {
            // As we spin through items, we will check to see if the current file is actually
            // a directory or a file. When it is actually a directory we will need to call
            // back into this function recursively to keep copying these nested folders.
            $target = $destination . '/' . $item->getBasename();

            if ($item->isDir()) {
                $path = $item->getPathname();

                if (!$this->copyDirectory($path, $target, $force)) {
                    return false;
                }
            }

            // If the current items is just a regular file, we will just copy this to the new
            // location and keep looping. If for some reason the copy fails we'll bail out
            // and return false, so the developer is aware that the copy process failed.
            else {
                if ('Thumbs.db' === $item->getFileName()) {
                    continue;
                }

                if ((is_file($target) && true !== $force) || !File::copy($item->getPathname(), $target)
                ) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @param $name
     * @param null $module
     * @param array $columns
     * @return null
     */
    public function table($name, $module = null, array $columns = [])
    {
// Ups, this is something unusual
        if (is_array($module)) {
            $columns = $module;
            $module = null;
        }
// Table should have module
        if (!isset($module)) {
            if ([] !== ($modules = $this->modules())) {
                foreach ($modules as $item) {
                    $manager = $this->module($item);
                    if ($manager->hasTable($name)) {
                        $module = $manager->getName();
                        break;
                    }
                }
            }
        }

        if (!isset($module) || !Wa::module($module)->hasTable($name)) {
            return null;
        }

        return TableInfo::getInstance($name, $module, $columns);
    }

    public function tableAliases($table, $array = false)
    {
        $table = explode(' as ', $table, 2);
        if (!isset($table[1])) {
            $table = [$table[0], $table[0]];
        }

        return false === $array ? array_get($table, 1) : $table;
    }

    public function getQueryLog($last = false)
    {
        $logs = $last ? [last(DB::getQueryLog())] : DB::getQueryLog();

        foreach ($logs as &$item) {
            $item = vsprintf(str_replace('?', '%s', $item['query']), $item['bindings']);
        }


        return $logs;
    }

    public function menu()
    {
        $manager = $this->instance('manager.site.menu');

        return $manager;
    }

    /**
     * Like php abs internal function, but only affect numbers
     *
     * @param $value
     * @return mixed
     */
    public function abs($value)
    {
        if (is_numeric($value)) {
            $value = abs($value);
        }

        return $value;
    }
}