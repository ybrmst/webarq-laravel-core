<?php
/**
 * Created by PhpStorm
 * Date: 24/10/2016
 * Time: 13:42
 * Author: Daniel Simangunsong
 *
 * Calm seas, never make skill full sailors
 */

namespace Webarq\Laravel;


use Illuminate\Support\Facades\Facade;

class WaFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'wa';
    }

}