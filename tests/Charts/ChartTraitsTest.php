<?php

namespace Khill\Lavacharts\Tests\Charts;

use \Mockery as m;
use \Khill\Lavacharts\Charts\AreaChart;
use \Khill\Lavacharts\Traits\AnnotationsTrait;

class ChartTraitsTest extends ChartTestCase
{
    public $mockLabel;

    public function setUp()
    {
        parent::setUp();

        $this->mockLabel = m::mock('\Khill\Lavacharts\Values\Label', ['MyTestChart'])->makePartial();
    }

    public function getMockChart($chartType)
    {
        return m::mock('\\Khill\\Lavacharts\\Charts\\'.$chartType.'Chart', [$this->mockLabel, $this->partialDataTable]);
    }

    public function testAnnotations()
    {
        $barChart = $this->getMockChart('Bar');

        $mock = $this->getMockForTrait('\Khill\Lavacharts\Traits\AnnotationsTrait');

        $mock->expects($this->any())
            ->method('abstractMethod')
            ->will($this->returnValue(TRUE));

        $barChart->annotations([
            'alwaysOutside' => true
        ]);

        $this->assertInstanceOf('\Khill\Lavacharts\Configs\Annotation', $barChart->annotations);
    }

    public function testAxisTitlesPositionWithValidValues()
    {
        $barChart = $this->getMockChart('Bar');

        $barChart->axisTitlesPosition('in');
        $this->assertEquals('in', $barChart->axisTitlesPosition);

        $barChart->axisTitlesPosition('out');
        $this->assertEquals('out', $barChart->axisTitlesPosition);

        $barChart->axisTitlesPosition('none');
        $this->assertEquals('none', $barChart->axisTitlesPosition);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testAxisTitlesPositionWithBadValue()
    {
        $barChart = $this->getMockChart('Bar');

        $barChart->axisTitlesPosition('stapler');
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testAxisTitlesPositionWithBadType($badTypes)
    {
        $barChart = $this->getMockChart('Bar');

        $barChart->axisTitlesPosition($badTypes);
    }

    public function testBarGroupWidthWithInt()
    {
        $barChart = $this->getMockChart('Bar');

        $barChart->barGroupWidth(200);

        $this->assertEquals(200, $barChart->barGroupWidth['groupWidth']);
    }

    public function testBarGroupWidthWithPercent()
    {
        $barChart = $this->getMockChart('Bar');

        $barChart->barGroupWidth('33%');

        $this->assertEquals('33%', $barChart->barGroupWidth['groupWidth']);
    }

    /**
     * @dataProvider nonIntOrPercentProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testBarGroupWidthWithBadTypes($badTypes)
    {
        $barChart = $this->getMockChart('Bar');

        $barChart->barGroupWidth($badTypes);
    }
}
