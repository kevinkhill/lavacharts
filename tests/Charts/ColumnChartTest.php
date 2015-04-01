<?php namespace Khill\Lavacharts\Tests\Charts;

use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Khill\Lavacharts\Charts\ColumnChart;
use \Mockery as m;

class ColumnChartTest extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->cc = new ColumnChart('MyTestChart');
    }

    public function testInstanceOfColumnChartWithType()
    {
        $this->assertInstanceOf('\Khill\Lavacharts\Charts\ColumnChart', $this->cc);
    }

    public function testTypeColumnChart()
    {
        $this->assertEquals('ColumnChart', $this->cc->type);
    }

    public function testLabelAssignedViaConstructor()
    {
        $this->assertEquals('MyTestChart', $this->cc->label);
    }

    public function testAxisTitlesPositionValidValues()
    {
        $this->cc->axisTitlesPosition('in');
        $this->assertEquals('in', $this->cc->getOption('axisTitlesPosition'));

        $this->cc->axisTitlesPosition('out');
        $this->assertEquals('out', $this->cc->getOption('axisTitlesPosition'));

        $this->cc->axisTitlesPosition('none');
        $this->assertEquals('none', $this->cc->getOption('axisTitlesPosition'));
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testAxisTitlesPositionWithBadValue()
    {
        $this->cc->axisTitlesPosition('happymeal');
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testAxisTitlesPositionWithBadType($badTypes)
    {
        $this->cc->axisTitlesPosition($badTypes);
    }

    public function testBarGroupWidthWithInt()
    {
        $this->cc->barGroupWidth(200);

        $bar = $this->cc->getOption('bar');

        $this->assertEquals(200, $bar['groupWidth']);
    }

    public function testBarGroupWidthWithPercent()
    {
        $this->cc->barGroupWidth('33%');

        $bar = $this->cc->getOption('bar');

        $this->assertEquals('33%', $bar['groupWidth']);
    }

    /**
     * @dataProvider nonIntOrPercentProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testBarGroupWidthWithBadTypes($badTypes)
    {
        $this->cc->barGroupWidth($badTypes);
    }

    public function testHorizontalAxis()
    {
        $mockHorizontalAxis = m::mock('Khill\Lavacharts\Configs\HorizontalAxis');
        $mockHorizontalAxis->shouldReceive('toArray')->once()->andReturn(array(
            'hAxis' => array()
        ));

        $this->cc->hAxis($mockHorizontalAxis);

        $this->assertTrue(is_array($this->cc->getOption('hAxis')));
    }

    public function testIsStacked()
    {
        $this->cc->isStacked(true);

        $this->assertTrue($this->cc->getOption('isStacked'));
    }

    /**
     * @dataProvider nonBoolProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testIsStackedWithBadType($badTypes)
    {
        $this->cc->isStacked($badTypes);
    }

    public function testVerticalAxis()
    {
        $mockVerticalAxis = m::mock('Khill\Lavacharts\Configs\VerticalAxis');
        $mockVerticalAxis->shouldReceive('toArray')->once()->andReturn(array(
            'vAxis' => array()
        ));

        $this->cc->vAxis($mockVerticalAxis);

        $this->assertTrue(is_array($this->cc->getOption('vAxis')));
    }

    public function nonIntOrPercentProvider()
    {
        return array(
            array(3.2),
            array(true),
            array(false),
            array(array()),
            array(new \stdClass)
        );
    }
}
