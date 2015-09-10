<?php

namespace Khill\Lavacharts\Tests\Dashboards;

use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Khill\Lavacharts\Dashboards\ControlWrapper;

class ControlWrapperTest extends ProvidersTestCase
{
    public $ControlWrapper;
    public $mockElementId;
    public $jsonOutput;

    public function setUp()
    {
        parent::setUp();

        $this->mockElementId = \Mockery::mock('\Khill\Lavacharts\Values\ElementId', ['TestId'])->makePartial();
        $this->jsonOutput = '{"controlType":"NumberRangeFilter","containerId":"TestId","options":{"Option1":5,"Option2":true}}';

        $mockNumberFilter = \Mockery::mock('\Khill\Lavacharts\Dashboards\Filters\NumberRangeFilter')
            ->shouldReceive('getType')
            ->once()
            ->andReturn('NumberRangeFilter')
            ->shouldReceive('jsonSerialize')
            ->once()
            ->andReturn([
                'Option1' => 5,
                'Option2' => true
            ])
            ->getMock();

        $this->ControlWrapper = new ControlWrapper($mockNumberFilter, $this->mockElementId);
    }

    public function testJsonSerializationOutput()
    {
        $this->assertEquals($this->jsonOutput, json_encode($this->ControlWrapper));
    }

    public function testToJavascriptOutput()
    {
        $javascript = 'new google.visualization.ControlWrapper('.$this->jsonOutput.')';

        $this->assertEquals($javascript, $this->ControlWrapper->toJavascript());
    }
}
