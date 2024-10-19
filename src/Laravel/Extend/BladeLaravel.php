<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 12/14/2016
 * Time: 5:14 PM
 */

namespace Webarq\Laravel\Extend;


use Blade;

class BladeLaravel
{
    public function __construct()
    {
        Blade::extend(function ($value) {
            return preg_replace('/\{\?(.+)\?\}/', '<?php ${1} ?>', $value);
        });

        $this->directiveExtend();
    }

    protected function directiveExtend()
    {
        Blade::directive('continue', function () {
            return '<?php continue; ?>';
        });

        Blade::directive('break', function () {
            return '<?php break; ?>';
        });

        Blade::directive('increment', function ($variable) {
            return '<?php $' . $variable . '++; ?>';
        });

        Blade::directive('set', function ($expression) {
// Break the Expression into Pieces
            $params = explode(',', $expression, 2);

// Check if value param is given
            if (!isset($params[1])) {
                $params[1] = '\'\'';
            }
// Trim space from value param
            $params[1] = trim($params[1]);

            return '<?php $' . $params[0] . ' = ' . $params[1] . '; ?>';
        });
    }
}