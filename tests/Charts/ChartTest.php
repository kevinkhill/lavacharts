<?php namespace Khill\Lavacharts\Tests\Charts;

use Khill\Lavacharts\Tests\DataProviders;
use Mockery as m;

class ChartTest extends DataProviders
{
    public function setUp()
    {
        parent::setUp();

        $this->mlc = m::mock(new \Khill\Lavacharts\Charts\LineChart('TestChart'));
        $this->mlc->label = 'TestChart';

        foreach (array(
            'dataTable',
            'backgroundColor',
            'chartArea',
            'colors',
            'events',
            'fontSize',
            'fontName',
            'height',
            'legend',
            'title',
            'titlePosition',
            'titleTextStyle',
            'tooltip',
            'width'
        ) as $prop) {
            $this->mlc->{$prop} = null;
        }

    }

    public function testLabelAssignedViaConstructor()
    {
    	$this->assertEquals('TestChart', $this->mlc->label);
    }

    public function testBackgroundColorWithValidValues()
    {
        $mbc = m::mock('Khill\Lavacharts\Configs\BackgroundColor');
        $mbc->shouldReceive('toArray')->once()->andReturn(array(
            'BackgroundColor' => array()
        ));

        $this->mlc->backgroundColor($mbc);
        $this->assertTrue(is_array($this->mlc->backgroundColor));
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException PHPUnit_Framework_Error
     */
    public function testBackgroundColorWithBadTypes($badTypes)
    {
        $this->mlc->backgroundColor($badTypes);
    }

}
