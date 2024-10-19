<?php
/**
 * Created by PhpStorm
 * Date: 05/03/2017
 * Time: 13:45
 * Author: Daniel Simangunsong
 *
 * Calm seas, never make skill full sailors
 */

namespace Webarq\Laravel;


use Illuminate\Support\Facades\Facade;

class SectionFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'section';
    }
}