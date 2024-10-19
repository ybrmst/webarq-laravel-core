<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 11/24/2016
 * Time: 2:45 PM
 */

namespace Webarq\Info;


use Illuminate\Support\Arr;
use Wa;
use Webarq\Manager\SetPropertyManagerTrait;
use Webarq\Manager\SingletonManagerTrait as Singleton;
use Webarq\Reader\ModuleConfigReader;

/**
 * Helper class
 *
 * Class ModuleInfo
 * @package Webarq\Info
 */
class ModuleInfo
{
    use Singleton, SetPropertyManagerTrait;

    /**
     * Module configuration
     *
     * @var array
     */
    protected $configs = [];

    /**
     * Module name
     *
     * @var string
     */
    protected $name;

    /**
     * Module title
     *
     * @var
     */
    protected $title;

    /**
     * Module registered panel menu
     *
     * @var array
     */
    protected $panels = [];

    /**
     * Module registered tables
     *
     * @var array object Webarq\Info\TableInfo
     */
    protected $tables = [];

    /**
     * Module icon, use for display panel
     *
     * @var
     */
    protected $icon;

    /**
     * @var bool
     */
    protected $ignored = false;

    /**
     * Create ModuleInfo instance
     *
     * @param $name
     * @param array $configs
     */
    public function __construct($name, array $configs = [])
    {
        $this->name = $name;

        if ([] === $configs) {
            $configs = ModuleConfigReader::get($name, []);
        }

        $this->setup($configs);
    }

    /**
     * Setup module by given options
     *
     * @param array $options
     */
    protected function setup(array $options)
    {
        $this->setupTables(array_pull($options, 'tables', []));

        $this->setupPanels(array_pull($options, 'panels', []));

        $this->setPropertyFromOptions($options);

        $this->mergeConfig();
    }

    /**
     * Set module tables information
     *
     * @param array $tables
     */
    private function setupTables(array $tables)
    {
        if ([] !== $tables) {
            foreach ($tables as $name) {
                $this->tables[$name] = TableInfo::getInstance(
                        $name, $this->name, ModuleConfigReader::get($this->name . '.tables.' . $name, []));
            }
        }
    }

    /**
     * Set module panels
     *
     * @param array $options
     */
    private function setupPanels(array $options)
    {
        if ([] !== $options) {
            foreach ($options as $key => $value) {
                if (is_numeric($key)) {
                    $key = $value;
                    $value = ModuleConfigReader::get($this->name . '.panel.' . $key, []);
                }
                $this->panels[$key] = Wa::load('info.panel', $key, $this->name, $value);
            }
        }
    }

    protected function mergeConfig()
    {
// Check for configuration table
        if (null !== ($model = Wa::model('configuration')) && [] !== ModuleConfigReader::get('payload', [])) {
            $items = $model->select('key', 'setting')
                    ->where('key', '!=', '48d35125f4a3c2c005d5b0697463c4651704b427')
                    ->where('module', '=', $this->name)
                    ->get()
                    ->all();

            if ([] !== $items) {
                foreach ($items as $item) {
                    array_set($this->configs, snake_case($item->key, '.'), $item->setting);
                }
            }
        }
    }

    /**
     * Get module configuration item, by given $key
     *
     * @param $key
     * @param null $default
     * @return mixed
     */
    public function getConfig($key, $default = null)
    {
        return array_get($this->configs, $key, $default);
    }

    /**
     * Get all module configuration
     *
     * @return array
     */
    public function getConfigs()
    {
        return $this->configs;
    }

    /**
     * Get module name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get module title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title ?: ucfirst($this->name);
    }

    /**
     * Get module table item by given $name
     *
     * @param $name
     * @return object Webarq\Info\TableInfo
     */
    public function getTable($name)
    {
        return array_get($this->tables, $name, TableInfo::getInstance(
                $name, $this->name, ModuleConfigReader::get($this->name . '.tables.' . $name, [])));
    }

    /**
     * Get all module tables
     *
     * @return array
     */
    public function getTables()
    {
        return $this->tables;
    }

    /**
     * @param string $default
     * @return string
     */
    public function getIcon($default = '')
    {
        return $this->icon ?: $default;
    }

    /**
     * Determine if a table registered in module
     *
     * @param $tableName
     * @return bool
     */
    public function hasTable($tableName)
    {
        return isset($this->tables[$tableName]);
    }

    /**
     * Get module panel item, by given $key
     *
     * @param mixed $key
     * @param mixed $default
     * @return mixed|object Webarq\Info\PanelInfo
     */
    public function getPanel($key, $default = null)
    {
        return array_get($this->panels, $key, $default);
    }

    /**
     * @return array
     */
    public function getPanels()
    {
        return $this->panels;
    }

    /**
     * @return bool
     */
    public function isIgnored()
    {
        return true === $this->ignored;
    }
}