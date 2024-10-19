<?php
/**
 * Created by PhpStorm
 * Date: 25/10/2016
 * Time: 11:46
 * Author: Daniel Simangunsong
 *
 * Calm seas, never make skill full sailors
 */

namespace Webarq\Commands\Installer;


use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Wa;
use Webarq\Info\ModuleInfo;
use Webarq\Info\TableInfo;
use Webarq\Reader\ModuleConfigReader;

abstract class AbstractInstaller
{
    /**
     * @var string
     */
    protected $response = '';

    /**
     * @var array of object Webarq\Info\TableInfo
     */
    protected $tables = [];

    /**
     * Table payload
     *
     * @var array
     */
    protected $payload = [];

    public function __construct($modules = null)
    {
        $this->payload = ModuleConfigReader::get('payload', []);

        if (null !== $modules) {
            $modules = explode(str_contains($modules, ']') ? ']' : ',', $modules);
        } else {
            $modules = Wa::modules();
        }

        $this->makeCollection($modules);
    }

    /**
     * @param array $modules
     */
    private function makeCollection(array $modules)
    {
        if ([] !== $modules) {
            foreach ($modules as $item) {
                if ('' === $item) continue;

                $item = trim($item, ',');
// Get intended table only
                if (str_contains($item, '[')) {
                    list($module, $table) = explode('[', $item, 2);
                    if (null !== ($module = Wa::module($module))) {
                        $this->collectTable($module, explode(',', $table));
                    }
                } else {
                    if (null !== ($module = Wa::module($item))) {
                        $this->collectTable($module);
                    }
                }
            }
        }
    }

    /**
     * @param ModuleInfo $module
     * @param array $tables
     */
    private function collectTable(ModuleInfo $module, array $tables = [])
    {
        if ([] !== $tables) {
            foreach ($tables as $table) {
                if ($module->hasTable($table)) {
                    $this->tables[$table] = $module->getTable($table);
                }
            }
        } else {
            $this->tables = Arr::merge($this->tables, $module->getTables());
        }
    }

    /**
     *
     */
    public function install()
    {
        if ([] !== $this->tables) {
            foreach ($this->tables as $table) {
// Install table (and related object)
                $this->installation($table);
            }

            $this->setPayload();
        }

        return 'done';
    }

    abstract protected function installation(TableInfo $table);

    private function setPayload()
    {
// Update payload data
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
                        . '..' . DIRECTORY_SEPARATOR
                        . 'modules';
            }
        }

        $f = fopen($root . DIRECTORY_SEPARATOR . 'payload.php', 'w+');

        fwrite($f, '<?php return '
                . PHP_EOL
                . '       '
                . $this->var_export_array($this->payload, '       ') . ';');
        fclose($f);
    }

    protected function var_export_array($var, $indent = "")
    {
        switch (gettype($var)) {
            case "array":
                $indexed = array_keys($var) === range(0, count($var) - 1);
                $r = [];
                foreach ($var as $key => $value) {
                    $r[] = "$indent    "
                            . ($indexed ? "" : $this->var_export_array($key) . " => ")
                            . $this->var_export_array($value, "$indent    ");
                }
                return "[\n" . implode(",\n", $r) . "\n" . $indent . "]";
            default:
                return var_export($var, TRUE);
        }
    }

    public function getResponse()
    {
        return $this->response;
    }

    protected function setMigrationFile($strClass, $lineCode)
    {
        $f = fopen(app_path() . '/../database/migrations/' . date('Y_m_d_His') . '_' . $strClass . '.php', 'w+');
        fwrite($f, $lineCode);
        fclose($f);
    }

    /**
     * @param string $class
     * @return string
     */
    protected function openClass($class)
    {
        $str = '<?php ' . PHP_EOL . PHP_EOL;
        $str .= 'use Illuminate\Database\Schema\Blueprint;' . PHP_EOL;
        $str .= 'use Illuminate\Database\Migrations\Migration;' . PHP_EOL . PHP_EOL . PHP_EOL;
        return $str . 'class ' . Str::studly($class) . ' extends Migration {' . PHP_EOL;
    }

    /**
     * @return string
     */
    protected function closeClass()
    {
        return '}';
    }
}