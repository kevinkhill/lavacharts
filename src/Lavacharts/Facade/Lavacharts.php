<?php namespace Lavacharts\Facade;

use \Illuminate\Support\Facades\Facade;

class Lavacharts extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'lavacharts';
    }
}
