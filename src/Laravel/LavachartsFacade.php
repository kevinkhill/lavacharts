<?php

namespace Khill\Lavacharts\Laravel;

use \Illuminate\Support\Facades\Facade;

class LavachartsFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'lavacharts';
    }
}
