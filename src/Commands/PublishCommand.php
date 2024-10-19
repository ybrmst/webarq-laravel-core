<?php
/**
 * Created by PhpStorm
 * Date: 11/02/2017
 * Time: 21:55
 * Author: Daniel Simangunsong
 *
 * Calm seas, never make skill full sailors
 */

namespace Webarq\Commands;


use File;
use Illuminate\Console\Command;
use Wa;

class PublishCommand extends Command
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
    protected $signature = 'wa:publish {source?} {destination?} {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish webarq core files';

    /**
     * @var
     */
    protected $dir;

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();

        $this->dir = realpath(__DIR__ . '/../..') . '/';
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $sources = $this->argument('source') ? explode(',', $this->argument('source')) : ['modules', 'public'];
// Copy directories
        $copying = [];

        foreach ($sources as $name) {
            $src = $this->dir . $name;
            if (null === ($des = $this->argument('destination'))) {
                $des = app_path() . '/../' . $name;
                if ('public' === $name) {
                    $des .= '/vendor';
                }
            }

            if ((is_file($src) || is_dir($src)) && Wa::copyDirectory($src, $des, $this->option('force'))) {
                $copying[] = $name;
            }
        }

// Need to move site directory from vendor
        $path = public_path('vendor' . DIRECTORY_SEPARATOR . 'webarq' . DIRECTORY_SEPARATOR . 'site');
        if (File::isDirectory(($path))) {
            Wa::copyDirectory($path, public_path('site'), $this->option('force') || !is_dir(public_path('site')));

            File::deleteDirectory($path);
        }
// And favicon.ico file as well
        $path = public_path('vendor' . DIRECTORY_SEPARATOR . 'webarq' . DIRECTORY_SEPARATOR . 'favicon.ico');
        if (!File::isFile(public_path('favicon.ico'))) {
            File::move($path, public_path('favicon.ico'));
        }

        $this->comment($copying
                ? 'Done. ' . implode($copying, ',') . ' directories has been copied'
                : 'Seems the directory/file you are trying to copy already exist. Please give option "--force" to force the system copying your file'
        );
    }
}
