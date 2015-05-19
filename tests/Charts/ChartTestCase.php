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

    protected function getMockAnnotation()
    {
        return m::mock('Khill\Lavacharts\Configs\Annotation', function($mock) {
            $mock->shouldReceive('toArray')->once()->andReturn([
                'annotations' => []
            ]);
        });
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

    protected function getMockColor($returnKey)
    {
        return m::mock('Khill\Lavacharts\Configs\Color', function($mock) use ($returnKey) {
            $mock->shouldReceive('toArray')->once()->andReturn([
                $returnKey => []
            ]);
        });
    }

    protected function getMockColorAxis()
    {
        return m::mock('Khill\Lavacharts\Configs\ColorAxis', function($mock) {
            $mock->shouldReceive('toArray')->once()->andReturn([
                'colorAxis' => []
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

    protected function getMockStroke($returnKey)
    {
        return m::mock('Khill\Lavacharts\Configs\Stroke', function($mock) use ($returnKey) {
            $mock->shouldReceive('toArray')->once()->andReturn([
                $returnKey => []
            ]);
        });
    }

    protected function getMockSizeAxis()
    {
        return m::mock('Khill\Lavacharts\Configs\SizeAxis', function($mock) {
            $mock->shouldReceive('toArray')->once()->andReturn([
                'sizeAxis' => []
            ]);
        });
    }

    protected function getMockTextStyle($returnKey)
    {
        return m::mock('Khill\Lavacharts\Configs\TextStyle', function($mock) use ($returnKey) {
            $mock->shouldReceive('toArray')->once()->andReturn([
                $returnKey => []
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
