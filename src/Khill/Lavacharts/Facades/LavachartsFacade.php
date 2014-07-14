<?php namespace Khill\Lavacharts\Facades;

use Illuminate\Support\Facades\Facade;

class Lavacharts extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'lavacharts'; }

}