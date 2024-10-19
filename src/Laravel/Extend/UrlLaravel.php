<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 12/15/2016
 * Time: 2:25 PM
 */

namespace Webarq\Laravel\Extend;


use App;
use HTML;
use URL;
use Wa;

class UrlLaravel
{
    public function __construct()
    {
        /**
         * Generate an absolute URL to given path, and prefixing it by  webarq config panel url prefix
         *
         * @param mixed $str
         */
        URL::macro('panel', function ($str, $attr = [], $secure = null) {
            if ($this->isValidUrl($str)) {
                return $str;
            }

            return $this->to(config('webarq.system.panel-url-prefix') . '/' . $str,
                    $attr, $secure ?: config('webarq.system.secure-url'));
        });

        /**
         * Generate an absolute URL to given path
         *
         * @param mixed $str
         */
        URL::macro('site', function ($str = null, $attr = [], $secure = null) {
            if ($this->isValidUrl($str)) {
                return $str;
            }

            return $this->to($str, $attr, $secure ?: config('webarq.system.secure-url'));
        });

        URL::macro('detect', function ($url, $module, $panel, $item) {
            if (true === $url) {
                $url = trim($module . '/' . $panel . '/' . $item, '/');
            } elseif (is_null($url)) {
                $url = 'helper/' . trim($item, '/') . '/' . $module . '/' . $panel;
            }

            return str_replace('//', '/', $url);
        });

        URL::macro('frontAsset', function ($str = '') {
            return $this->asset('vendor/webarq/front-end/' . $str);
        });

        URL::macro('trans', function ($str, $attr = [], $secure = null, $lang = null) {
            if (class_exists('Wl')) {
                if (null === $lang) {
                    $lang = App::getLocale();
                }

                if (0 === Wa::config('system.url-lang-section', config('webarq.system.url-lang-section'))
                        || strtolower($lang) !== \Wl::getSystem()
                ) {
                    $str = trim($lang . '/' . $str, '/');
                }
            }

            return $this->site($str, $attr, $secure);
        });
    }
}