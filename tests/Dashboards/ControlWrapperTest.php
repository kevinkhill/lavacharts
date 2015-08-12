<?php

namespace Khill\Lavacharts\Tests\Dashboards;

use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Khill\Lavacharts\Dashboards\ControlWrapper;
use \Mockery as m;

class ControlWrapperTest extends ProvidersTestCase
{
    public function testJsonSerialization()
    {
        $mockElementId    = m::mock('\Khill\Lavacharts\Values\ElementId', ['TestId'])->makePartial();
        $mockNumberFilter = m::mock('\Khill\Lavacharts\Dashboards\Filters\NumberRange')
            ->shouldReceive('getType')
            ->once()
            ->andReturn('LineChart')
            ->shouldReceive('getOptions')
            ->once()
            ->andReturn([
                'Option1' => 5,
                'Option2' => true
            ])
            ->getMock();

        $chartWrapper = new ControlWrapper($mockNumberFilter, $mockElementId);

        $this->assertEquals('{"controlType":"LineChart","containerId":"TestId","options":{"Option1":5,"Option2":true}}', json_encode($chartWrapper));
    }
}
