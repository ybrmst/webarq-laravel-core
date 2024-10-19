<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 12/6/2016
 * Time: 6:32 PM
 */

namespace Webarq\Commands\Installer;


use Wa;
use Webarq\Info\TableInfo;

class CreateTable extends AbstractInstaller
{
    public function __construct($modules = null)
    {
        parent::__construct($modules);

        /*
         * Ah .. finally, here you are. Your first time i guess.
         * Okay then, before do the installation let the system clear some files for us to ensure everything will run
         * smoothly.
         */
        if ([] === $this->payload) {
            array_map('unlink', glob(app_path() . '/../database/migrations/*'));
            copy(__DIR__ . '../../../../supports/auth.php', config_path('auth.php'));
            copy(__DIR__ . '../../../../supports/elfinder.php', config_path('elfinder.php'));
            copy(__DIR__ . '../../../../supports/.gitignore', app_path() . '/../.gitignore');

            $env = fopen(app_path() . '/../.env', 'a') or die('Unable to open the environment file!');
            fwrite($env, <<<EOD


CK_EDITOR_SOURCE=local
CK_EDITOR_TIMESTAMP=946684800
EOD
            );
            fclose($env);
            exec('php artisan key:generate');
        }
    }

    protected function installation(TableInfo $table)
    {
        if (null === array_get($this->payload, 'installed.' . $table->getName() . '.create')
                && [] !== $table->getColumns()
        ) {
            $code = $this->openClass($strClass = 'create_' . $table->getName() . '_class');
            $code .= $this->migrationUp($table) . PHP_EOL;
            $code .= $this->migrationDown($table);
            $code .= $this->closeClass();
// Copy migration file
            $this->setMigrationFile($strClass, $code);
// Create model or not
            $this->createModelOrNot($table);
// Payload set
            array_set($this->payload, 'installed.' . $table->getName() . '.create', $table->getSerialize());

            if ($table->isMultiLingual()) {
                $name = \Wl::translateTableName($table->getName());
                array_set($this->payload, 'installed.' . $name . '.create', serialize([$name]));
            }

        } elseif ($table->isMultiLingual()) {
// If webarq lang vendor, installed in the middle of development, then
// need to check if respected translation table has been created or not
            $name = \Wl::translateTableName($table->getName());
            if (null === array_get($this->payload, 'installed.' . $name . '.create')) {
                $code = $this->openClass($strClass = 'create_' . $name . '_class');
                $code .= $this->migrationUp($table, true) . PHP_EOL;
                $code .= $this->migrationDown($table, true);
                $code .= $this->closeClass();

                $this->setMigrationFile($strClass, $code);
// Payload set
                array_set($this->payload, 'installed.' . $name . '.create', serialize([$name]));
            }
        }
    }

    /**
     * Migration up method
     *
     * @param TableInfo $table
     * @param bool|false $lateTranslation
     * @return string
     */
    private function migrationUp(TableInfo $table, $lateTranslation = false)
    {
        //DDL script
        $str = '    /**' . PHP_EOL;
        $str .= '     * Run the migrations.' . PHP_EOL;
        $str .= '     *' . PHP_EOL;
        $str .= '     * @return void' . PHP_EOL;
        $str .= '     */' . PHP_EOL;
        $str .= '    public function up()' . PHP_EOL;
        $str .= '    {';

        if (false === $lateTranslation) {
            $str .= PHP_EOL;
            $str .= '        Schema::create(\'' . $table->getName() . '\', function(Blueprint $table)' . PHP_EOL;
            $str .= '        {' . PHP_EOL;

            $uniqueItems = $uniquesItems = [];

            foreach ($table->getColumns() as $column) {
                $str .= (new DefinitionManager($column))->getDefinition();
                if (true === $column->isUnique()) {
                    $uniqueItems[] = $column->getName();
                }
                if (true === $column->isUniques()) {
                    $uniquesItems[] = $column->getName();
                }
            }
            $mgr = new UniqueDefinitionManager();
            $str .= $mgr->getDefinitionUnique($uniqueItems);
            $str .= $mgr->getDefinitionUniques($uniquesItems);
            $str .= '        });' . PHP_EOL;
        }

// Create translation table
        $str .= $this->translationTable($table);
        $str .= '    }' . PHP_EOL;

        return $str;
    }

    private function translationTable(TableInfo $table)
    {
        if ($table->isMultiLingual()) {
            $str = PHP_EOL;
            $str .= '        Schema::create(\'' . \Wl::translateTableName($table->getName()) . '\', function(Blueprint $table)' . PHP_EOL;
            $str .= '        {' . PHP_EOL;
// Column ID
            $str .= (new DefinitionManager(Wa::load('info.column', ['master' => 'bigId'])))
                    ->getDefinition();
// Column language code
            $str .= (new DefinitionManager(Wa::load('info.column', [
                    'name' => 'lang_code',
                    'type' => 'char',
                    'length' => 2,
                    'notnull' => true
            ])))->getDefinition();

            foreach ($table->getColumns() as $column) {
                if ($column->isPrimary()) {
                    $attrColumn = [
                            'name' => $table->getReferenceKeyName(),
                            'type' => $column->getType(),
                            'unsigned' => $column->getExtra('unsigned'),
                            'notnull' => true];
                    $str .= (new DefinitionManager(Wa::load('info.column', $attrColumn)))
                            ->getDefinition();
                } elseif ($column->getExtra('multilingual')) {
                    $column = clone $column;

                    $str .= (new DefinitionManager($column))->getDefinition();
                }
            }

            $str .= (new DefinitionManager(Wa::load('info.column', ['master' => 'createOn'])))
                    ->getDefinition();

            return $str . '        });' . PHP_EOL;
        }
    }

    /**
     * Migration  down method
     *
     * @param TableInfo $table
     * @param bool|false $lateTranslation
     * @return string
     */
    private function migrationDown(TableInfo $table, $lateTranslation = false)
    {
        $str = '    /**' . PHP_EOL;
        $str .= '     * Reverse the migrations.' . PHP_EOL;
        $str .= '     *' . PHP_EOL;
        $str .= '     * @return void' . PHP_EOL;
        $str .= '     */' . PHP_EOL;
        $str .= '    public function down()' . PHP_EOL;
        $str .= '    {';

        if (false === $lateTranslation) {
            $str .= PHP_EOL;
            $str .= '        Schema::drop(\'' . $table->getName() . '\');' . PHP_EOL;
        }


        if ($table->isMultiLingual()) {
            $str .= PHP_EOL . '        Schema::drop(\'' . $table->getName() . '_i18n\');' . PHP_EOL;
        }

        $str .= '    }' . PHP_EOL;

        return $str;
    }

    /**
     * All newly generated model files will be placed under app/Webarq/Model directory
     *
     * @param TableInfo $table
     */
    private function createModelOrNot(TableInfo $table)
    {
        $class = $table->getModel();

        if (null !== $class) {

            $path = 'Model';

            if (null !== ($x = $table->getModelDir())) {
                $path = '/' . $x;
            }

            $namespace = $this->getModelNameSpace('Webarq/' . $path);

            $class = studly_case($class) . 'Model';

            if (!file_exists(__DIR__ . '/../../' . $path . '/' . $class . '.php')
                    && !file_exists(app_path() . '/Webarq/' . $path . '/' . $class . '.php')
            ) {
                $str = '<?php' . PHP_EOL . PHP_EOL;
                $str .= 'namespace ' . $namespace . ';' . PHP_EOL . PHP_EOL . PHP_EOL;
// All model should be extends AbstractListingModel
                $str .= 'use Webarq\Model\AbstractListingModel;' . PHP_EOL . PHP_EOL;
                $str .= 'class ' . $class . ' extends AbstractListingModel' . PHP_EOL;
                $str .= '{' . PHP_EOL;
                $str .= '    protected $table = \'' . $table->getName() . '\';' . PHP_EOL;
                $str .= '}';

                $f = fopen(app_path() . '/Webarq/' . $path . '/' . $class . '.php', 'w+');
                fwrite($f, $str);
                fclose($f);
            }
        }
    }

    protected function getModelNameSpace($dir)
    {
        $path = app_path();

        $namespace = 'App';

        foreach (explode('/', $dir) as $dir) {
            $path .= '/' . $dir;
            if (!is_dir($path)) {
                mkdir($path, 0755);
            }
            $namespace .= '\\' . ucfirst(strtolower($dir));
        }

        return $namespace;
    }
}