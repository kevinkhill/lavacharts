<?php

namespace Khill\Lavacharts\Tests\Charts;

use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Mockery as m;

class ChartTestCase extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    protected function getMockBackgroundColor()
    {
        return m::mock('Khill\Lavacharts\Configs\BackgroundColor', function($mock) {
            $mock->shouldReceive('toArray')->once()->andReturn([
                'backgroundColor' => []
            ]);
        });
    }

    protected function getMockChartArea()
    {
        return m::mock('Khill\Lavacharts\Configs\ChartArea', function($mock) {
            $mock->shouldReceive('toArray')->once()->andReturn([
                'chartArea' => []
            ]);
        });
    }

    protected function getMockHorizontalAxis()
    {
        return m::mock('Khill\Lavacharts\Configs\HorizontalAxis', function($mock) {
            $mock->shouldReceive('toArray')->once()->andReturn([
                'hAxis' => []
            ]);
        });
    }

    protected function getMockVerticalAxis()
    {
        return m::mock('Khill\Lavacharts\Configs\VerticalAxis', function($mock) {
            $mock->shouldReceive('toArray')->once()->andReturn([
                'vAxis' => []
            ]);
        });
    }
}
