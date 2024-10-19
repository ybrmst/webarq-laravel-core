<?php

namespace Webarq\Commands;


use Illuminate\Console\Command;
use Wa;
use Webarq\Commands\Installer\AlterTable;
use Webarq\Commands\Installer\CreateTable;
use Webarq\Reader\ModuleConfigReader;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Argument module value should be follow below format:
     * {module name[table name, another table name],another module name}
     * eg.
     * system[users, roles],static[client,contact]
     *
     * @var string
     */
    protected $signature = 'wa:install {migrate=true} {option?} {--alter} {--delete}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migration based on configuration model';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $stage = getenv('APP_ENV');
        if ('local' !== $stage) {
            if (!$this->confirm('You are in ' . $stage . ' mode. Are you wish to continue?', true)) {
                $this->comment('Good bye ;D');
                return;
            }
        }

        if ($this->option('alter')) {
//            (new AlterTable())->install();
            $response = 'Not supported yet. Please do it by your self';
        } elseif ($this->option('delete')) {
            $response = 'Not supported yet. Please do it by your self';
        } else {
            $response = (new CreateTable())->install();
        }
// Exec laravel artisan
        if ('true' === $this->argument('migrate')) {
            exec('php artisan migrate');
        }
// Check for seeds
        if ([] !== ($arr = ModuleConfigReader::get('seeds', []))) {
            foreach ($arr as $table => $rows) {
                foreach ($rows as $row) {
                    if (null !== ($id = array_get($row, 'id'))) {
                        $find = \DB::table($table)->find($id);
                    } else {
                        $find = \DB::table($table);
                        foreach ($row as $column => $value) {
                            $find->where($column, $value);
                        }
                        $find = $find->select($column)->get()->first();
                    }

                    if (is_null($find)) {
                        \DB::table($table)->insert($row);
                    }
                }
            }
        }


        $this->comment($response);
    }
}
