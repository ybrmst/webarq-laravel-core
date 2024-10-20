<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 11/24/2016
 * Time: 5:35 PM
 */

namespace Webarq\Laravel\Extend;


use Illuminate\Support\Str;

class StrLaravel
{
    public function __construct()
    {
        /**
         * Remove vowels from given string
         *
         * @param  $string
         * @return string
         */
        Str::macro('stripVowels', function ($string) {
            return str_replace(['a', 'i', 'u', 'e', 'o', 'A', 'I', 'U', 'E', 'O'], '', $string);
        });

        /**
         * Change wildcard with proper value in a string
         *
         * @param string|array $pair formatted string|[formatted string,sequence params]
         * @param array $values [value to matching against params (if any)]
         * @param string $wildcard
         * @example Str::prepareStatement('some/?/?',['value1','value2']) will return 'some/value1/value2'
         * @example Str::prepareStatement(['some/?/?',['id','label']],['label'=>'abc','id'=>2]) will return 'some/2/abc'
         * @return mixed
         */
        Str::macro('prepareStatement', function ($pair = array(), array $values, $wildcard = '?') {
            if (is_array($pair)) {
                list($str, $params) = $pair;
// Bundling params with match values
                foreach ($params as &$param) $param = $values[$param];
            } else {
                $str = $pair;
                $params = $values;
            }
            if (false !== ($post = strpos($str, $wildcard))) {
                $pattern = '/[' . $wildcard . ']{' . strlen($wildcard) . '}/';
                $str = preg_replace_callback($pattern, function () use (&$params) {
                    return array_shift($params); // wrap in quotes and sanitize
                }, $str);
            }
            return $str;
        });

        /**
         * Encode a serialization of an array
         *
         * @param array $items
         */
        Str::macro('encodeSerialize', function (array $items) {
            return base64_encode(serialize($items));
        });

        /**
         * Decode an encoded serialization of an array
         *
         * @param string $string
         */
        Str::macro('decodeSerialize', function ($string) {
// Safety decoder
            try {
                return unserialize(base64_decode($string));
            } catch(\Exception $e) {
                return $string;
            }
        });

        /**
         *  Remove duplicate word
         */
        Str::macro('filter', function ($string, $separator = ' ') {
            return implode($separator, array_flip(array_flip(explode($separator, $string))));
        });

        /**
         * Human readable file size
         * @param mixed $bytes
         * @param false|number $precision
         */
        Str::macro('size', function ($bytes, $precision = 2) {
            $units = ['B', 'KB', 'MB', 'GB', 'TB'];
            $index = count($units) - 1;

            while($index >= 1) {
// ** is a new style for pow
                if ($bytes >= $bytes ** $index) {
                    break;
                }
                $index--;
            }

            return number_format($bytes/1024, $precision) . ' ' . $units[$index];
        });


        /**
         * Check if primary string starting with seconder (singular) string
         * @param string $primary
         * @param string $seconder
         * @param string $separator
         */
        Str::macro('isStartsWith', function($primary, $seconder, $separator = '_'){
            return Str::startsWith($primary, $seconder . $separator)
                    || Str::startsWith($primary, str_singular($seconder) . $separator);
        });

        /**
         * @param string $serial
         * @return mixed Origin format before serialized or false when error
         */
        Str::macro('saveUnSerialize', function ($serial){
           try {
               return unserialize($serial);
           } catch (\Exception $e) {
               return false;
           }
        });
    }
}