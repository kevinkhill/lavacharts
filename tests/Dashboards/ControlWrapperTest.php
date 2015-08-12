<?php

namespace Khill\Lavacharts\Tests\Dashboards;

use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Khill\Lavacharts\Dashboards\ControlWrapper;
use \Mockery as m;

class ControlWrapperTest extends ProvidersTestCase
{
    public $ControlWrapper;

    public function setUp()
    {
        parent::setUp();

        $mockElementId    = m::mock('\Khill\Lavacharts\Values\ElementId', ['TestId'])->makePartial();
        $mockNumberFilter = m::mock('\Khill\Lavacharts\Dashboards\Filters\NumberRange')
            ->shouldReceive('getType')
            ->once()
            ->andReturn('NumberRangeFilter')
            ->shouldReceive('getOptions')
            ->once()
            ->andReturn([
                'Option1' => 5,
                'Option2' => true
            ])
            ->getMock();

        $this->ControlWrapper = new ControlWrapper($mockNumberFilter, $mockElementId);
    }

    public function testJsonSerialization()
    {
        $json = '{"controlType":"NumberRangeFilter","containerId":"TestId","options":{"Option1":5,"Option2":true}}';

        $this->assertEquals($json, json_encode($this->ControlWrapper));
    }

    public function testToJavascript()
    {
        $json = '{"controlType":"NumberRangeFilter","containerId":"TestId","options":{"Option1":5,"Option2":true}}';
        $javascript = "new google.visualization.ControlWrapper($json)";

        $this->assertEquals($javascript, $this->ControlWrapper->toJavascript());
    }
}
