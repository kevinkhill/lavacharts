<?php

namespace Khill\Lavacharts\Tests\Charts;

use \Khill\Lavacharts\Charts\BarChart;
use \Mockery as m;

class BarChartTest extends ChartTestCase
{
    public $BarChart;

    public function setUp()
    {
        parent::setUp();

        $label = m::mock('\Khill\Lavacharts\Values\Label', ['MyTestChart'])->makePartial();

        $this->BarChart = new BarChart($label, $this->partialDataTable);
    }

    public function testTypeBarChart()
    {
        $this->assertEquals('BarChart', BarChart::TYPE);
        $this->assertEquals('BarChart', $this->BarChart->getType());
    }

    public function testLabelAssignedViaConstructor()
    {
        $this->assertEquals('MyTestChart', (string) $this->BarChart->getLabel());
    }

    public function testAnnotations()
    {
        $this->BarChart->annotations([
            'alwaysOutside' => true
        ]);

        $this->assertInstanceOf('\Khill\Lavacharts\Configs\Annotation', $this->BarChart->annotations);
    }

    public function testAxisTitlesPositionWithValidValues()
    {
        $this->BarChart->axisTitlesPosition('in');
        $this->assertEquals('in', $this->BarChart->axisTitlesPosition);

        $this->BarChart->axisTitlesPosition('out');
        $this->assertEquals('out', $this->BarChart->axisTitlesPosition);

        $this->BarChart->axisTitlesPosition('none');
        $this->assertEquals('none', $this->BarChart->axisTitlesPosition);
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

        $this->assertEquals(200, $this->BarChart->barGroupWidth['groupWidth']);
    }

    public function testBarGroupWidthWithPercent()
    {
        $this->BarChart->barGroupWidth('33%');

        $this->assertEquals('33%', $this->BarChart->barGroupWidth['groupWidth']);
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

        $this->assertEquals(0.75, $this->BarChart->dataOpacity);
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

        $this->assertTrue($this->BarChart->enableInteractivity);
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
        $this->assertEquals('datum', $this->BarChart->focusTarget);

        $this->BarChart->focusTarget('category');
        $this->assertEquals('category', $this->BarChart->focusTarget);
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

        $this->assertTrue($this->BarChart->forceIFrame);
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
        $this->BarChart->hAxes([[], []]);

        $this->assertTrue(is_array($this->BarChart->hAxes));
        $this->assertInstanceOf('\Khill\Lavacharts\Configs\HorizontalAxis', $this->BarChart->hAxes[0]);
        $this->assertInstanceOf('\Khill\Lavacharts\Configs\HorizontalAxis', $this->BarChart->hAxes[1]);
    }

    /**
     * @dataProvider nonArrayProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testHorizontalAxesWithBadTypes($badTypes)
    {
        $this->BarChart->hAxes($badTypes);
    }

    public function testHorizontalAxis()
    {
        $this->BarChart->hAxis([
            'allowContainerBoundaryTextCutoff' => true
        ]);

        $this->assertInstanceOf('\Khill\Lavacharts\Configs\HorizontalAxis', $this->BarChart->hAxis);
    }

    public function testOrientationWithValidInput()
    {
        $this->BarChart->orientation('horizontal');
        $this->assertEquals('horizontal', $this->BarChart->orientation);

        $this->BarChart->orientation('vertical');
        $this->assertEquals('vertical', $this->BarChart->orientation);
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

        $this->assertTrue($this->BarChart->isStacked);
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

        $this->assertTrue($this->BarChart->reverseCategories);
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
        $this->BarChart->series([[], []]);

        $this->assertTrue(is_array($this->BarChart->series));
        $this->assertInstanceOf('\Khill\Lavacharts\Configs\Series', $this->BarChart->series[0]);
        $this->assertInstanceOf('\Khill\Lavacharts\Configs\Series', $this->BarChart->series[1]);
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

        $this->assertEquals('maximized', $this->BarChart->theme);
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
        $this->BarChart->vAxes([[], []]);

        $this->assertTrue(is_array($this->BarChart->vAxes));
        $this->assertInstanceOf('\Khill\Lavacharts\Configs\VerticalAxis', $this->BarChart->vAxes[0]);
        $this->assertInstanceOf('\Khill\Lavacharts\Configs\VerticalAxis', $this->BarChart->vAxes[1]);
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
        $this->BarChart->vAxis([
            'direction' => 1
        ]);

        $this->assertInstanceOf('\Khill\Lavacharts\Configs\VerticalAxis', $this->BarChart->vAxis);
    }
}

