<?php

namespace Khill\Lavacharts\Tests\Charts;

use \Khill\Lavacharts\Charts\Chart;
use Khill\Lavacharts\Support\Contracts\JsClass;
use Khill\Lavacharts\Support\Traits\MaterialRenderableTrait;
use Khill\Lavacharts\Support\Traits\PngRenderableTrait;
use const Khill\Lavacharts\Support\GOOGLE_VISUALIZATION;

/**
 * MockChart Class
 *
 * This is used for testing traits, as well as testing the parent methods for the charts.
 */
class MockChart extends Chart implements JsClass
{
    use PngRenderableTrait, MaterialRenderableTrait;

    public function getJsPackage()
    {
        return 'mockchart';
    }

    public function getVersion()
    {
        return '3.2';
    }

    public function getJsClass()
    {
        return static::GOOGLE_VISUALIZATION . 'MockChart';
    }
}
