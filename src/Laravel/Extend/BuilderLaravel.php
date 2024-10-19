<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 2/27/2017
 * Time: 9:53 AM
 */

namespace Webarq\Laravel\Extend;


use App;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Query\Builder;
use Wa;

class BuilderLaravel
{
    public function __construct()
    {
        Builder::macro('selectTranslate', function (... $columns) {
            $lang = strtolower(App::getLocale());

            if ([] !== $columns) {
                if (is_array($columns[0])) {
                    $columns = $columns[0];
                }

                if (class_exists('Wl') && $lang !== \Wl::getSystem()) {
                    $this->makeSelectTranslate($lang, ... $columns);
                } else {
                    foreach ($columns as $i => &$column) {
                        if (is_bool($column)) {
                            unset($columns[$i]);
                        } else {
                            $columns[$i] = str_replace('!', '', $column);
                        }
                    }

                    $this->addSelect($columns);
                }
            }

            return $this;
        });

        Builder::macro('whereTranslate', function ($column, $operator = '=', $value = null, $boolean = 'and',
                                                   $check = false) {
            $lang = strtolower(App::getLocale());
            $operator = strtolower(trim($operator));

            if (is_bool($boolean)) {
                $check = $boolean;
                $boolean = 'and';
            } elseif (is_bool($value)) {
                $check = $value;
                $value = $operator;
                $operator = '=';
            }

            if (class_exists('wl') && $lang !== \Wl::getSystem()) {
                $this->makeWhereTranslate($column, $operator, $value, $boolean, $check);
            } else {
                switch ($operator) {
                    case 'between':
                        $this->whereBetween($column, $value, $boolean);
                        break;
                    case 'not between':
                        $this->whereBetween($column, $value, $boolean, true);
                        break;
                    case 'in':
                        $this->whereIn($column, $value, $boolean);
                        break;
                    case 'not in':
                        $this->whereIn($column, $value, $boolean, true);
                        break;
                    default:
                        $this->where($column, $operator, $value, $boolean);
                        break;
                }
            }

            return $this;
        });

        Builder::macro('orWhereTranslate', function ($column, $operator = '=', $value = false, $check = false) {
            return $this->whereTranslate($column, $operator, $value, $check, 'or');
        });

        Builder::macro('monthly', function ($date = null, $column = 'create_on') {

            if (null === $date) {
                $carbon = Carbon::now();
            } else {
                if (false === ($time = strtotime($date))) {
                    $time = time();
                    $column = $date;
                }

                $carbon = Carbon::createFromTimestamp($time);
            }
            return $this
                    ->whereRaw('DATE(' . $column . ') >= ?', [$carbon->startOfMonth()->format('Y-m-d')])
                    ->whereRaw('DATE(' . $column . ') <= ?', [$carbon->endOfMonth()->format('Y-m-d')]);
        });

        Builder::macro('weekly', function ($date = null, $column = 'create_on') {

            if (null === $date) {
                $carbon = Carbon::now();
            } else {
                if (false === ($time = strtotime($date))) {
                    $time = time();
                    $column = $date;
                }

                $carbon = Carbon::createFromTimestamp($time);
            }

            return $this
                    ->whereRaw('DATE(' . $column . ') >= ?', [$carbon->startOfWeek()->format('Y-m-d')])
                    ->whereRaw('DATE(' . $column . ') <= ?', [$carbon->endOfWeek()->format('Y-m-d')]);
        });

        Builder::macro('daily', function ($date = null, $column = 'create_on') {

            if (null === $date) {
                $date = date('Y-m-d');
            } elseif (false === ($time = strtotime($date))) {
                $column = $date;
                $date = date('Y-m-d');
            }

            return $this->whereRaw('DATE(' . $column . ') = ?', [$date]);
        });

        Builder::macro('makeWhereFromOptions', function (array $options = []) {
            if ([] !== $options) {
                foreach ($options as $column => $value) {
                    if (is_array($value)) {
                        $this->whereIn($column, $value);
                    } else {
                        $this->where($column, $value);
                    }
                }
            }

            return $this;
        });

        Builder::macro('makeSequenceFromOptions', function ($sequence) {

            if (!is_string($sequence) && is_callable($sequence)) {
                $sequence($this);
            } elseif (!is_null($sequence)) {
                if (!is_array($sequence)) {
                    $sequence = explode(',', $sequence);
                }

                foreach ($sequence as $column => $dir) {
                    if (is_numeric($column)) {
                        if (false !== strpos($dir, ':')) {
                            list($column, $dir) = explode(':', $dir);
                        } else {
                            $column = $dir;
                            $dir = 'asc';
                        }
                    }

                    $this->orderBy($column, $dir);
                }
            }

            return $this;
        });
    }
}