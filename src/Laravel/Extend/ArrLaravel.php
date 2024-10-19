<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 11/24/2016
 * Time: 4:08 PM
 */

namespace Webarq\Laravel\Extend;


use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class ArrLaravel
{
    public function __construct()
    {
        /**
         * Get array member with numeric keys only
         *
         * @param  mixed $var
         * @return bool
         */
        Arr::macro('getNumericKeyItems', function (array $array) {
            $tmp = [];

            foreach ($array as $x => $y) {
                if (is_int($x)) {
                    $tmp[$x] = $y;
                }
            }

            return $tmp;
        });

        /**
         * Determine associative array
         *
         * @param  mixed $var
         * @return bool
         */
        Arr::macro('isAssocAbs', function (array $array) {
            $keys = array_keys($array);

            return array_keys($keys) !== $keys && [] === Arr::getNumericKeyItems($array);
        });


        /**
         * Transform multidimensional array into linear array by concatenate the keys
         *
         * @param  array $array
         * @param  string $separator
         * @param  string $prefix
         * @return array
         */
        Arr::macro('concatenation', function (array $array, $separator = '.', $prefix = '') {
            $tmp = [];
            foreach ($array as $i => $v) {
                $key = trim($prefix . $separator . $i, '.');
                if (is_array($v)) {
                    $tmp = $tmp + Arr::concatenation($v, $separator, $key);
                } else {
                    $tmp[$key] = $v;
                }
            }
            return $tmp;
        });

        /**
         * Merge to or more array
         *
         * Option :
         * normal|true  : Replace existing key
         * join|null    : Will join existing key
         * ignore|false : Will ignore existing key
         *
         * High note.
         * Numeric keys will be always appended
         *
         * @param  array $old
         * @param  array $new
         * @param  string $option | Array n ( ... )
         * @return array
         */
        Arr::macro('merge', function (array $old, array $new, $option = 'normal') {
            $args = func_get_args();
            $count = count($args);
            if ($count > 2) {
                $option = $args[$count - 1];
                if (is_array($option)) {
                    $option = 'normal';
                } else {
                    $count--;
                }

                $results = $args[0];
                for ($i = 1; $i < $count; $i++) {
                    $results = Arr::combine($results, $args[$i], $option);
                }
                return $results;
            } else {
                return Arr::combine($old, $new, $option);
            }
        });

        /**
         * Combine two array
         *
         * @param  array $old
         * @param  array $new
         * @param  string $option
         * @return array
         * @todo Enhanced the merge logic
         */
        Arr::macro('combine', function (array $old, array $new, $option = 'normal') {
            if (true === $option) {
                $option = 'normal';
            } elseif (false === $option) {
                $option = 'ignore';
            } elseif (is_null($option)) {
                $option = 'join';
            }

            if ([] === $old) {
                return $new;
            } elseif ([] === $new) {
                return $old;
            }

            foreach ($new as $key => $value) {
                if (!isset($old[$key]) || gettype($old[$key]) !== gettype($value)) {
                    $old[$key] = $value;
                    continue;
                }
                if (is_numeric($key)) {
                    $old[] = $value;
                    continue;
                }
                if (is_array($value)) {
                    $old[$key] = Arr::combine((array)$old[$key], $value, $option);

                    if (!Arr::isAssocAbs($old[$key])) {
                        $old[$key] = array_flip(array_flip($old[$key]));
                    }
                } else {
                    if ('normal' === $option) {
                        $old[$key] = $value;
                    } elseif ('join' === $option) {
                        $old[$key] .= ' ' . $value;
                    }
                }
            }

            return $old;
        });

// Filtering array by value
        Arr::macro('filter', function (array $arr, array $allowed = []) {
            if ([] === $allowed) {
                return array_filter($arr);
            }

            return array_filter($arr, function ($value) use ($allowed) {
                $status = false;
                foreach ($allowed as $value1) {
// Casting numeric type as int
                    if (is_numeric($value1)) {
                        $value1 = (int)$value1;
                    }
// Casting numeric type as int
                    if (is_numeric($value)) {
                        $value = (int)$value;
                    } elseif (!is_array($value)) {
                        $value = trim((string)$value);
                    }

                    if ($value === $value1
                            || (!is_null($value)
                                    && false !== $value
                                    && 0 !== $value
                                    && [] !== $value
                                    && '' !== $value)
                    ) {
                        $status = true;
                        break;
                    }
                }
                return $status;
            });
        });

        Arr::macro('inArray', function (array $arr, $key) {
            foreach ($arr as $i => $value) {
                if ($value === $key) {
                    return true;
                }
            }
            return false || isset($arr[$key]);
        });

// Filter associative member from an array
        Arr::macro('unsetAssocKey', function (array $array) {
            if ([] !== $array) {
                foreach ($array as $key => $value) {
                    if (!is_numeric($key)) {
                        unset($array[$key]);
                    }
                }
            }
            return $array;
        });

        Arr::macro('serialize', function (array $array) {
            return base64_encode(serialize($array));
        });

        Arr::macro('deserialize', function ($str) {
            return unserialize(base64_decode($str));
        });

// Get the first key of an array
        Arr::macro('firstKey', function($arr = []) {
            if (is_array($arr) || $arr instanceof Collection) {
                foreach ($arr as $key => $value) return $key;
            }
        });

        Arr::macro('changeCase', function($arr, $case = CASE_UPPER ){
            foreach ($arr as $key => $value) {
                $arr[$key] = CASE_UPPER === $case ? strtoupper($value) : strtolower($value);
            }

            return $arr;
        });
    }
}