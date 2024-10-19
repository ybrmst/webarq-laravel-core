<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/5/2017
 * Time: 1:18 PM
 */

namespace Webarq\Manager;


use Html;
use Illuminate\Support\Traits\Macroable;
use URL;

class ValueModifierManager
{
    use Macroable;

    public function __construct()
    {
// Hashing string
        static::macro('password', function ($string) {
            return \Hash::make($string);
        });

// Format given datetime
        static::macro('wa-datetime', function ($date = null, $format = 'Y-m-d H:i:s') {
            return date($format, strtotime($date) ?: time());
        });

// Format given date
        static::macro('wa-date', function ($date = null, $format = 'Y-m-d') {
            return date($format, strtotime($date) ?: time());
        });

// Format given time
        static::macro('wa-time', function ($date = null, $format = 'H:i:s') {
            return date($format, strtotime($date) ?: time());
        });

// Print an image with smaller size (without resizing)
        static::macro('thumb', function ($path, $width = 30, $height = 0) {
            $attr = [];

            if (0 !== (int)$width) {
                $attr['style'] = 'width: ' . $width . (!ends_with($width, '%') ? 'px;' : ';');
            }

            if (0 !== (int)$height) {
                $attr['style'] = 'height: ' . $height . (!ends_with($height, '%') ? 'px;' : ';');
            }

            return is_file($path)
                    ? '<img src="' . URL::asset($path) . '"' . Html::attributes($attr) . '/>'
                    : '';
        });

        static::macro('str-slug', function ($str) {
            return URL::isValidUrl($str) ? $str : str_slug($str);
        });

        static::macro('getFileExt', function ($value, $input) {
            return app('request')->file($input) ? app('request')->file($input)->extension() : $value;
        });

        static::macro('getFileSize', function ($value, $input) {
            return app('request')->file($input) ? app('request')->file($input)->getSize() : $value;
        });

        static::macro('getFileName', function ($value, $input) {
            return app('request')->file($input) ? app('request')->file($input)->getClientOriginalName() : $value;
        });

        static::macro('toFile', function ($file) {
            return '<a href="' . asset($file) . '" target="_blank">' . array_last(explode('/', $file)) . '</a>';
        });

        static::macro('stringToBlade', function ($string, array $args = array()) {
            try {
                $string = \Blade::compileString($string);

                ob_start() and extract($args, EXTR_SKIP);

                // We'll include the view contents for parsing within a catcher
                // so we can avoid any WSOD "White screen of death" errors. If an exception occurs we
                // will throw it out to the exception handler.
                try {
                    eval('?>' . $string);
                }

                    // If we caught an exception, we'll silently flush the output
                    // buffer so that no partially rendered views get thrown out
                    // to the client and confuse the user with junk.
                catch (\Exception $e) {
                    ob_get_clean();
                    throw $e;
                }

                return ob_get_clean();
            } catch (\Exception $e) {
                return $string;
            }
        });

        static::macro('legacy_html_entity_decode', function ($str, $quotes = ENT_QUOTES, $charset = 'UTF-8') {
            return preg_replace_callback('/&#(\d+);/', function ($m) use ($quotes, $charset) {
                if (0x80 <= $m[1] && $m[1] <= 0x9F) {
                    return iconv('cp1252', $charset, html_entity_decode($m[0], $quotes, 'cp1252'));
                }
                return html_entity_decode($m[0], $quotes, $charset);
            }, $str);
        });
    }
}