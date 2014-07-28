<?php namespace Khill\Lavacharts\Tests\Charts;

use Khill\Lavacharts\Tests\DataProviders;
use Mockery as m;

class ChartTest extends DataProviders
{
    public function setUp()
    {
        parent::setUp();

        $this->mlc = m::mock('Khill\Lavacharts\Charts\LineChart', ['TestChart'])->makePartial();
    }

    public function testLabelAssignedViaConstructor()
    {
    	$this->assertEquals('TestChart', $this->mlc->label);
    }

    public function testBackgroundColorWithValidValues()
    {
        $mbc = m::mock('Khill\Lavacharts\Configs\BackgroundColor');
        $mbc->shouldReceive('toArray')->once()->andReturn(array(
            'backgroundColor' => array()
        ));

        $this->mlc->backgroundColor($mbc);
        $this->assertTrue(is_array($this->mlc->options['backgroundColor']));
    }

    /**
     * @dataProvider nonConfigObjectProvider
     * @expectedException PHPUnit_Framework_Error
     */
    public function testBackgroundColorWithBadTypes($badTypes)
    {
        $this->mlc->backgroundColor($badTypes);
    }

    public function testChartAreaWithValidValues()
    {
        $mbc = m::mock('Khill\Lavacharts\Configs\ChartArea');
        $mbc->shouldReceive('toArray')->once()->andReturn(array(
            'chartArea' => array()
        ));

        $this->mlc->chartArea($mbc);
        $this->assertTrue(is_array($this->mlc->options['chartArea']));
    }

    /**
     * @dataProvider nonConfigObjectProvider
     * @expectedException PHPUnit_Framework_Error
     */
    public function testChartAreaWithBadTypes($badTypes)
    {
        $this->mlc->chartArea($badTypes);
    }

}
