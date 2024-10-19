<?php
/**
 * Created by PhpStorm
 * Date: 30/12/2016
 * Time: 21:58
 * Author: Daniel Simangunsong
 *
 * Calm seas, never make skill full sailors
 */

namespace Webarq\Laravel\Extend;


use DB;
use Validator;

class ValidatorLaravel
{
    public function __construct()
    {
        Validator::extend('numericArray', function ($attribute, array $values, $parameters, $validator) {
            foreach ($values as $value) {
                if (!is_numeric($value)) {
                    return false;
                }
            }
            return true;
        });

        Validator::extend('integerArray', function ($attribute, array $values, $parameters, $validator) {
            foreach ($values as $value) {
                if (!is_int($value)) {
                    return false;
                }
            }
            return true;
        });


        Validator::extend('maxArray', function ($attribute, array $values, $parameters, $validator) {
            foreach ($values as $value) {
                if ($value > $parameters[0]) {
                    return false;
                }
            }
            return true;
        });


        Validator::extend('minArray', function ($attribute, array $values, $parameters, $validator) {
            foreach ($values as $value) {
                if ($value < $parameters[0]) {
                    return false;
                }
            }
            return true;
        });

        Validator::extend('existent', function ($column, $value, $parameters = [], $validator) {
            $id = (int)$parameters[2];

            $validator->addReplacer('existent', function ($message, $attribute, $rule, $parameters)
            use ($validator) {
                return str_replace(':count', $parameters[1], $message);
            });

            $row = DB::table($parameters[0])
                    ->select($column)
                    ->where($column, $value);

            if (0 !== $id) {
                $row->where(array_get($parameters, 4, 'id'), '!=', $id);
            }

            return $row->get()->count() < (int)$parameters[1];


            return true;
        }, 'You can only have :count :attribute item on the current list');

        Validator::extend('captcha', function ($attribute, $secret, $parameters = []) {
            $response = json_decode(file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='
                    . array_get($parameters, 0) . '&response=' . $secret
                    . '&remoteip=' . \Request::server('REMOTE_ADDR')), true);

            return array_get((array)$response, 'success', false);
        }, 'Please check your :attribute secret');

        Validator::extend('complexity', function ($attribute, $value, $parameters = []) {
            $c = preg_match('@[A-Z]@', $value)
                    + preg_match('@[a-z]@', $value)
                    + preg_match('@[\d]@', $value)
                    + preg_match('@[\W]@', $value);

            return $c === 4;
        }, 'Please check your :attribute complexity. Your :attribute should contain alpha numeric, at least one'
        . ' non alpha numeric, one lower case, and one uppercase');

        Validator::extend('matched_hash', function($field, $value, $parameters)
        {
            return \Hash::check($value, $parameters[0]);
        }, 'Your :attribute did not match');
    }
}