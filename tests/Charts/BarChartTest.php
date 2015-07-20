<?php

namespace Khill\Lavacharts\Tests\Charts;

use \Khill\Lavacharts\Charts\BarChart;
use \Mockery as m;

class BarChartTest extends ChartTestCase
{
    public function setUp()
    {
        parent::setUp();

        $label = m::mock('\Khill\Lavacharts\Values\Label', ['MyTestChart'])->makePartial();

        $this->BarChart = new BarChart($label, $this->partialDataTable);
    }

    public function testInstanceOfBarChartWithType()
    {
        $this->assertInstanceOf('\Khill\Lavacharts\Charts\BarChart', $this->BarChart);
    }

    public function testTypeBarChart()
    {
        $chart = $this->BarChart;

        $this->assertEquals('BarChart', $chart::TYPE);
    }

    public function testLabelAssignedViaConstructor()
    {
        $this->assertEquals('MyTestChart', (string) $this->BarChart->getLabel());
    }

    public function testAnnotations()
    {
        $this->BarChart->annotations($this->getMockAnnotation());

        $this->assertTrue(is_array($this->BarChart->getOption('annotations')));
    }

    public function testAxisTitlesPositionWithValidValues()
    {
        $this->BarChart->axisTitlesPosition('in');
        $this->assertEquals('in', $this->BarChart->getOption('axisTitlesPosition'));

        $this->BarChart->axisTitlesPosition('out');
        $this->assertEquals('out', $this->BarChart->getOption('axisTitlesPosition'));

        $this->BarChart->axisTitlesPosition('none');
        $this->assertEquals('none', $this->BarChart->getOption('axisTitlesPosition'));
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testAxisTitlesPositionWithBadValue()
    {
        $this->BarChart->axisTitlesPosition('stapler');
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testAxisTitlesPositionWithBadType($badTypes)
    {
        $this->BarChart->axisTitlesPosition($badTypes);
    }

    public function testBarGroupWidthWithInt()
    {
        $this->BarChart->barGroupWidth(200);

        $this->assertEquals(200, $this->BarChart->getOption('barGroupWidth')['groupWidth']);
    }

    public function testBarGroupWidthWithPercent()
    {
        $this->BarChart->barGroupWidth('33%');

        $this->assertEquals('33%', $this->BarChart->getOption('barGroupWidth')['groupWidth']);
    }

    /**
     * @dataProvider nonIntOrPercentProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testBarGroupWidthWithBadTypes($badTypes)
    {
        $this->BarChart->barGroupWidth($badTypes);
    }

    public function testDataOpacity()
    {
        $this->BarChart->dataOpacity(0.75);

        $this->assertEquals(0.75, $this->BarChart->getOption('dataOpacity'));
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testDataOpacityWithOverLimit()
    {
        $this->BarChart->dataOpacity(1.1);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testDataOpacityWithNegative()
    {
        $this->BarChart->dataOpacity(-0.1);
    }

    /**
     * @dataProvider nonFloatProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testDataOpacityWithBadValues($badVals)
    {
        $this->BarChart->dataOpacity($badVals);
    }

    public function testEnableInteractivity()
    {
        $this->BarChart->enableInteractivity(true);

        $this->assertTrue($this->BarChart->getOption('enableInteractivity'));
    }

    /**
     * @dataProvider nonBoolProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testEnableInteractivityWithBadTypes($badTypes)
    {
        $this->BarChart->enableInteractivity($badTypes);
    }

    public function testFocusTarget()
    {
        $this->BarChart->focusTarget('datum');
        $this->assertEquals('datum', $this->BarChart->getOption('focusTarget'));

        $this->BarChart->focusTarget('category');
        $this->assertEquals('category', $this->BarChart->getOption('focusTarget'));
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testFocusTargetWithBadType($badTypes)
    {
        $this->BarChart->focusTarget($badTypes);
    }

    public function testForceIFrame()
    {
        $this->BarChart->forceIFrame(true);

        $this->assertTrue($this->BarChart->getOption('forceIFrame'));
    }

    /**
     * @dataProvider nonBoolProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testForceIFrameWithBadType($badTypes)
    {
        $this->BarChart->forceIFrame($badTypes);
    }

    public function testHorizontalAxes()
    {
        $mockHorizontalAxis = m::mock('Khill\Lavacharts\Configs\HorizontalAxis');

        $this->BarChart->hAxes([$mockHorizontalAxis, $mockHorizontalAxis]);

        $this->assertTrue(is_array($this->BarChart->getOption('hAxes')));
    }

    /**
     * @dataProvider nonArrayProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testHorizontalAxesWithBadTypes($badTypes)
    {
        $this->BarChart->hAxes($badTypes);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testHorizontalAxesWithArrayOfBadTypes()
    {
        $this->BarChart->hAxes([1, 4.5, 'salmon']);
    }

    public function testHorizontalAxis()
    {
        $this->BarChart->hAxis($this->getMockHorizontalAxis());

        $this->assertTrue(is_array($this->BarChart->getOption('hAxis')));
    }

    public function testOrientationWithValidInput()
    {
        $this->BarChart->orientation('horizontal');
        $this->assertEquals('horizontal', $this->BarChart->getOption('orientation'));

        $this->BarChart->orientation('vertical');
        $this->assertEquals('vertical', $this->BarChart->getOption('orientation'));
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testOrientationWithBadValue()
    {
        $this->BarChart->orientation('circles');
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testOrientationWithBadTypes($badTypes)
    {
        $this->BarChart->orientation($badTypes);
    }

    public function testIsStacked()
    {
        $this->BarChart->isStacked(true);

        $this->assertTrue($this->BarChart->getOption('isStacked'));
    }

    /**
     * @dataProvider nonBoolProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testIsStackedWithBadTypes($badTypes)
    {
        $this->BarChart->isStacked($badTypes);
    }

    public function testReverseCategories()
    {
        $this->BarChart->reverseCategories(true);

        $this->assertTrue($this->BarChart->getOption('reverseCategories'));
    }

    /**
     * @dataProvider nonBoolProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testReverseCategoriesWithBadTypes($badTypes)
    {
        $this->BarChart->reverseCategories($badTypes);
    }

    public function testSeries()
    {
        $mockSeries = m::mock('Khill\Lavacharts\Configs\Series');

        $this->BarChart->series([$mockSeries, $mockSeries]);

        $this->assertTrue(is_array($this->BarChart->getOption('series')));
    }

    /**
     * @dataProvider nonArrayProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testSeriesWithBadTypes($badTypes)
    {
        $this->BarChart->series($badTypes);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testSeriesWithArrayOfBadTypes()
    {
        $this->BarChart->series([4, [], 8.7]);
    }

    public function testTheme()
    {
        $this->BarChart->theme('maximized');

        $this->assertEquals('maximized', $this->BarChart->getOption('theme'));
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testThemeWithBadValue()
    {
        $this->BarChart->theme('spaceTheme');
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testThemeWithBadTypes($badTypes)
    {
        $this->BarChart->theme($badTypes);
    }

    public function testVerticalAxes()
    {
        $mockVerticalAxis = m::mock('Khill\Lavacharts\Configs\VerticalAxis');

        $this->BarChart->vAxes([$mockVerticalAxis, $mockVerticalAxis]);

        $this->assertTrue(is_array($this->BarChart->getOption('vAxes')));
    }

    /**
     * @dataProvider nonArrayProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testVerticalAxesWithBadTypes($badTypes)
    {
        $this->BarChart->vAxes($badTypes);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testVerticalAxesWithArrayOfBadTypes()
    {
        $this->BarChart->vAxes([false, 'truth']);
    }

    public function testVerticalAxis()
    {
        $this->BarChart->vAxis($this->getMockVerticalAxis());

        $this->assertTrue(is_array($this->BarChart->getOption('vAxis')));
    }
}
