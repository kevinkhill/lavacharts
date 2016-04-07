<?php

namespace Khill\Lavacharts\Tests\Charts;

use Khill\Lavacharts\Tests\ProvidersTestCase;

class ChartTraitsTest extends ProvidersTestCase
{
    public $mockChart;

    public function setUp()
    {
        parent::setUp();

        $label = \Mockery::mock('\Khill\Lavacharts\Values\Label', ['TestChart'])->makePartial();

        $this->mockChart = new MockChart($label, $this->partialDataTable);
    }

    public function testAnnotations()
    {
        $this->mockChart->annotations([
            'alwaysOutside' => true
        ]);

        $this->assertInstanceOf('\Khill\Lavacharts\Configs\Annotation', $this->mockChart->annotations);
        $this->assertTrue($this->mockChart->annotations->alwaysOutside);
    }

    public function testAreaOpacity()
    {
        $this->mockChart->areaOpacity(.5);

        $this->assertEquals(.5, $this->mockChart->areaOpacity);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testAreaOpacityWithOutOfRangeValues()
    {
        $this->mockChart->areaOpacity(-0.2);
        $this->mockChart->areaOpacity(1.2);
    }

    /**
     * @dataProvider nonFloatProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testAreaOpacityWithBadParams($badVals)
    {
        $this->mockChart->areaOpacity($badVals);
    }

    public function testAxisTitlesPositionValidValues()
    {
        $this->mockChart->axisTitlesPosition('in');
        $this->assertEquals('in', $this->mockChart->axisTitlesPosition);

        $this->mockChart->axisTitlesPosition('out');
        $this->assertEquals('out', $this->mockChart->axisTitlesPosition);

        $this->mockChart->axisTitlesPosition('none');
        $this->assertEquals('none', $this->mockChart->axisTitlesPosition);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testAxisTitlesPositionWithBadValue()
    {
        $this->mockChart->axisTitlesPosition('happymeal');
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testAxisTitlesPositionWithBadType($badTypes)
    {
        $this->mockChart->axisTitlesPosition($badTypes);
    }

    public function testBarGroupWidthWithInt()
    {
        $this->mockChart->barGroupWidth(200);

        $this->assertEquals(200, $this->mockChart->barGroupWidth['groupWidth']);
    }

    public function testBarGroupWidthWithPercent()
    {
        $this->mockChart->barGroupWidth('33%');

        $this->assertEquals('33%', $this->mockChart->barGroupWidth['groupWidth']);
    }

    /**
     * @dataProvider nonIntOrPercentProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testBarGroupWidthWithBadTypes($badTypes)
    {
        $this->mockChart->barGroupWidth($badTypes);
    }

    public function testColorAxis()
    {
        $this->mockChart->colorAxis([]);

        $this->assertInstanceOf('\Khill\Lavacharts\Configs\ColorAxis', $this->mockChart->colorAxis);
    }

    /**
     * @dataProvider nonArrayProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testColorAxisWithBadTypes($badVals)
    {
        $this->mockChart->colorAxis($badVals);
    }

    public function testCrosshair()
    {
        $this->mockChart->crosshair([
            'color' => 'red'
        ]);

        $this->assertInstanceOf('\Khill\Lavacharts\Configs\Crosshair', $this->mockChart->crosshair);
        $this->assertEquals('red', $this->mockChart->crosshair->color);
    }

    public function testCurveTypeWithValidValues()
    {
        $this->mockChart->curveType('none');
        $this->assertEquals('none', $this->mockChart->curveType);

        $this->mockChart->curveType('function');
        $this->assertEquals('function', $this->mockChart->curveType);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testCurveTypeWithBadValue()
    {
        $this->mockChart->curveType('rocks');
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testCurveTypeWithBadType($badTypes)
    {
        $this->mockChart->curveType($badTypes);
    }

    public function testDataOpacity()
    {
        $this->mockChart->dataOpacity(0.75);

        $this->assertEquals(0.75, $this->mockChart->dataOpacity);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testDataOpacityWithOutOfLimitsValues()
    {
        $this->mockChart->dataOpacity(-0.1);
        $this->mockChart->dataOpacity(1.1);
    }

    /**
     * @dataProvider nonFloatProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testDataOpacityWithBadValues($badVals)
    {
        $this->mockChart->dataOpacity($badVals);
    }

    public function testEnableInteractivity()
    {
        $this->mockChart->enableInteractivity(true);

        $this->assertTrue($this->mockChart->enableInteractivity);
    }

    /**
     * @dataProvider nonBoolProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testEnableInteractivityWithBadTypes($badTypes)
    {
        $this->mockChart->enableInteractivity($badTypes);
    }

    public function testFocusTarget()
    {
        $this->mockChart->focusTarget('datum');
        $this->assertEquals('datum', $this->mockChart->focusTarget);

        $this->mockChart->focusTarget('category');
        $this->assertEquals('category', $this->mockChart->focusTarget);
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testFocusTargetWithBadType($badTypes)
    {
        $this->mockChart->focusTarget($badTypes);
    }

    public function testForceIFrame()
    {
        $this->mockChart->forceIFrame(true);

        $this->assertTrue($this->mockChart->forceIFrame);
    }

    /**
     * @dataProvider nonBoolProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testForceIFrameWithBadType($badTypes)
    {
        $this->mockChart->forceIFrame($badTypes);
    }

    public function testHorizontalAxes()
    {
        $this->mockChart->hAxes([[], []]);

        $this->assertTrue(is_array($this->mockChart->hAxes));
        $this->assertInstanceOf('\Khill\Lavacharts\Configs\HorizontalAxis', $this->mockChart->hAxes[0]);
        $this->assertInstanceOf('\Khill\Lavacharts\Configs\HorizontalAxis', $this->mockChart->hAxes[1]);
    }

    /**
     * @dataProvider nonArrayProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testHorizontalAxesWithBadTypes($badTypes)
    {
        $this->mockChart->hAxes($badTypes);
    }

    public function testHorizontalAxis()
    {
        $this->mockChart->hAxis([
            'allowContainerBoundaryTextCutoff' => true
        ]);

        $this->assertInstanceOf('\Khill\Lavacharts\Configs\HorizontalAxis', $this->mockChart->hAxis);
    }

    public function testInterpolateNulls()
    {
        $this->mockChart->interpolateNulls(true);

        $this->assertTrue($this->mockChart->interpolateNulls);
    }

    /**
     * @dataProvider nonBoolProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testInterpolateNullsWithBadType($badTypes)
    {
        $this->mockChart->interpolateNulls($badTypes);
    }

    public function testIsStacked()
    {
        $this->mockChart->isStacked(true);

        $this->assertTrue($this->mockChart->isStacked);
    }

    /**
     * @dataProvider nonBoolProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testIsStackedWithBadTypes($badTypes)
    {
        $this->mockChart->isStacked($badTypes);
    }

    public function testLineWidth()
    {
        $this->mockChart->lineWidth(22);

        $this->assertEquals(22, $this->mockChart->lineWidth);
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testLineWidthWithBadType($badTypes)
    {
        $this->mockChart->lineWidth($badTypes);
    }

    public function testOrientationWithValidInput()
    {
        $this->mockChart->orientation('horizontal');
        $this->assertEquals('horizontal', $this->mockChart->orientation);

        $this->mockChart->orientation('vertical');
        $this->assertEquals('vertical', $this->mockChart->orientation);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testOrientationWithBadValue()
    {
        $this->mockChart->orientation('circles');
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testOrientationWithBadTypes($badTypes)
    {
        $this->mockChart->orientation($badTypes);
    }

    public function testPointShape()
    {
        foreach (['circle', 'triangle', 'square', 'diamond', 'star', 'polygon'] as $shape) {
            $this->mockChart->pointShape($shape);

            $this->assertEquals($shape, $this->mockChart->pointShape);
        }
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testPointShapeWithInvalidShape()
    {
        $this->mockChart->pointShape('banana');
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testPointShapeWithBadType($badTypes)
    {
        $this->mockChart->pointShape($badTypes);
    }

    public function testPointSize()
    {
        $this->mockChart->pointSize(3);

        $this->assertEquals(3, $this->mockChart->pointSize);
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testPointSizeWithBadType($badTypes)
    {
        $this->mockChart->pointSize($badTypes);
    }

    public function testReverseCategories()
    {
        $this->mockChart->reverseCategories(true);

        $this->assertTrue($this->mockChart->reverseCategories);
    }

    /**
     * @dataProvider nonBoolProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testReverseCategoriesWithBadTypes($badTypes)
    {
        $this->mockChart->reverseCategories($badTypes);
    }

    public function testSelectionModeWithValidInput()
    {
        $this->mockChart->selectionMode('multiple');
        $this->assertEquals('multiple', $this->mockChart->selectionMode);

        $this->mockChart->selectionMode('single');
        $this->assertEquals('single', $this->mockChart->selectionMode);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testSelectionModeWithBadValue()
    {
        $this->mockChart->selectionMode('all');
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testSelectionModeWithBadTypes($badTypes)
    {
        $this->mockChart->selectionMode($badTypes);
    }

    public function testSeries()
    {
        $this->mockChart->series([[], []]);

        $this->assertTrue(is_array($this->mockChart->series));
        $this->assertInstanceOf('\Khill\Lavacharts\Configs\Series', $this->mockChart->series[0]);
        $this->assertInstanceOf('\Khill\Lavacharts\Configs\Series', $this->mockChart->series[1]);
    }

    /**
     * @dataProvider nonArrayProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testSeriesWithBadTypes($badTypes)
    {
        $this->mockChart->series($badTypes);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testSeriesWithArrayOfBadTypes()
    {
        $this->mockChart->series([4, [], 8.7]);
    }

    public function testTheme()
    {
        $this->mockChart->theme('maximized');

        $this->assertEquals('maximized', $this->mockChart->theme);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testThemeWithBadValue()
    {
        $this->mockChart->theme('spaceTheme');
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testThemeWithBadTypes($badTypes)
    {
        $this->mockChart->theme($badTypes);
    }

    public function testVerticalAxes()
    {
        $this->mockChart->vAxes([[], []]);

        $this->assertTrue(is_array($this->mockChart->vAxes));
        $this->assertInstanceOf('\Khill\Lavacharts\Configs\VerticalAxis', $this->mockChart->vAxes[0]);
        $this->assertInstanceOf('\Khill\Lavacharts\Configs\VerticalAxis', $this->mockChart->vAxes[1]);
    }

    /**
     * @dataProvider nonArrayProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testVerticalAxesWithBadTypes($badTypes)
    {
        $this->mockChart->vAxes($badTypes);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testVerticalAxesWithArrayOfBadTypes()
    {
        $this->mockChart->vAxes([false, 'truth']);
    }

    public function testVerticalAxis()
    {
        $this->mockChart->vAxis([
            'direction' => 1
        ]);

        $this->assertInstanceOf('\Khill\Lavacharts\Configs\VerticalAxis', $this->mockChart->vAxis);
    }
}
