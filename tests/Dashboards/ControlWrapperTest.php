<?php

namespace Khill\Lavacharts\Tests\Dashboards;

use Khill\Lavacharts\Dashboards\Filter;
use Khill\Lavacharts\Dashboards\Wrappers\ControlWrapper;
use Khill\Lavacharts\Tests\ProvidersTestCase;
use Mockery;

/**
 * @property string jsonOutput
 * @property Filter mockNumberRangeFilter
 */
class ControlWrapperTest extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->jsonOutput = '{"containerId":"control-div","options":[],"controlType":"NumberRangeFilter"}';

        $this->mockNumberRangeFilter = Mockery::mock(Filter::class)
            ->shouldReceive('getOptions')
            ->andReturn([])
            ->shouldReceive('getType')
            ->andReturn('NumberRangeFilter')
            ->shouldReceive('getWrapType')
            ->andReturn('controlType')
            ->getMock();
    }

    /**
     * @covers \Khill\Lavacharts\Dashboards\Wrappers\Wrapper::getJsClass
     */
    public function testGetJsClass()
    {
        $controlWrapper = new ControlWrapper($this->mockNumberRangeFilter, 'control-div');

        $javascript = 'google.visualization.ControlWrapper';

        $this->assertEquals($javascript, $controlWrapper->getJsClass());
    }

    public function testJsonSerialize()
    {
        $controlWrapper = new ControlWrapper($this->mockNumberRangeFilter, 'control-div');

        $this->assertJsonStringEqualsJsonString(
            $this->jsonOutput,
            json_encode($controlWrapper)
        );
    }

    public function testToJson()
    {
        $controlWrapper = new ControlWrapper($this->mockNumberRangeFilter, 'control-div');

        $this->assertJsonStringEqualsJsonString(
            $this->jsonOutput,
            $controlWrapper->toJson()
        );
    }
}
