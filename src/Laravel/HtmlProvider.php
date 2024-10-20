<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 2/16/2017
 * Time: 7:26 PM
 */

namespace Webarq\Laravel;

use Illuminate\Support\Str;
use Collective\Html\HtmlServiceProvider;
use Html;
use URL;

class HtmlProvider extends HtmlServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Html::macro('anchor', function ($str, $label, array $options = []) {
            $secure = array_pull($options, 'secure', false);

            if (!URL::isValidUrl($str)) {
                $str = URL::trans($str, [], $secure);
            } elseif (!Str::startsWith($str, url(''))) {
                $options += ['target' => '_blank', 'rel' => 'nofollow'];
            }

            return '<a href="' . $this->url->to($str, [], $secure) . '"' . $this->attributes($options) . '>'
            . $label . '</a>';
        });
    }

    protected function registerFormBuilder()
    {
        $this->app->singleton('form', function ($app) {
            $form = new FormBuilder($app['html'], $app['url'], $app['view'], $app['session.store']->getToken());

            return $form->setSessionStore($app['session.store']);
        });
    }
}