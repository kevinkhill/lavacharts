<?php namespace Khill\Lavacharts\Tests\Charts;

use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Khill\Lavacharts\Charts\BarChart;
use \Mockery as m;

class BarChartTest extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->bc = new BarChart('MyTestChart');
    }

    public function testInstanceOfBarChartWithType()
    {
        $this->assertInstanceOf('\Khill\Lavacharts\Charts\BarChart', $this->bc);
    }

    public function testTypeBarChart()
    {
        $this->assertEquals('BarChart', $this->bc->type);
    }

    public function testLabelAssignedViaConstructor()
    {
        $this->assertEquals('MyTestChart', $this->bc->label);
    }

    public function testAnnotations()
    {
        $mockAnnotation = m::mock('Khill\Lavacharts\Configs\Annotation');
        $mockAnnotation->shouldReceive('toArray')->once()->andReturn(array(
            'annotations' => array()
        ));

        $this->bc->annotations($mockAnnotation);

        $this->assertTrue(is_array($this->bc->getOption('annotations')));
    }

    public function testAxisTitlesPositionWithValidValues()
    {
        $this->bc->axisTitlesPosition('in');
        $this->assertEquals('in', $this->bc->getOption('axisTitlesPosition'));

        $this->bc->axisTitlesPosition('out');
        $this->assertEquals('out', $this->bc->getOption('axisTitlesPosition'));

        $this->bc->axisTitlesPosition('none');
        $this->assertEquals('none', $this->bc->getOption('axisTitlesPosition'));
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testAxisTitlesPositionWithBadValue()
    {
        $this->bc->axisTitlesPosition('stapler');
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testAxisTitlesPositionWithBadType($badTypes)
    {
        $this->bc->axisTitlesPosition($badTypes);
    }

    public function testBarGroupWidthWithInt()
    {
        $this->bc->barGroupWidth(200);

        $bar = $this->bc->getOption('bar');

        $this->assertEquals(200, $bar['groupWidth']);
    }

    public function testBarGroupWidthWithPercent()
    {
        $this->bc->barGroupWidth('33%');

        $bar = $this->bc->getOption('bar');

        $this->assertEquals('33%', $bar['groupWidth']);
    }

    /**
     * @dataProvider nonIntOrPercentProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testBarGroupWidthWithBadTypes($badTypes)
    {
        $this->bc->barGroupWidth($badTypes);
    }

    public function testDataOpacity()
    {
        $this->bc->dataOpacity(0.75);

        $this->assertEquals(0.75, $this->bc->getOption('dataOpacity'));
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testDataOpacityWithOverLimit()
    {
        $this->bc->dataOpacity(1.1);
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testDataOpacityWithNegative()
    {
        $this->bc->dataOpacity(-0.1);
    }

    /**
     * @dataProvider nonFloatProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testDataOpacityWithBadValues($badVals)
    {
        $this->bc->dataOpacity($badVals);
    }

    public function testEnableInteractivity()
    {
        $this->bc->enableInteractivity(true);

        $this->assertTrue($this->bc->getOption('enableInteractivity'));
    }

    /**
     * @dataProvider nonBoolProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testEnableInteractivityWithBadTypes($badTypes)
    {
        $this->bc->enableInteractivity($badTypes);
    }

    public function testFocusTarget()
    {
        $this->bc->focusTarget('datum');
        $this->assertEquals('datum', $this->bc->getOption('focusTarget'));

        $this->bc->focusTarget('category');
        $this->assertEquals('category', $this->bc->getOption('focusTarget'));
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testFocusTargetWithBadType($badTypes)
    {
        $this->bc->focusTarget($badTypes);
    }

    public function testForceIFrame()
    {
        $this->bc->forceIFrame(true);

        $this->assertTrue($this->bc->getOption('forceIFrame'));
    }

    /**
     * @dataProvider nonBoolProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testForceIFrameWithBadType($badTypes)
    {
        $this->bc->forceIFrame($badTypes);
    }

    public function testHorizontalAxes()
    {
        $mockHorizontalAxis = m::mock('Khill\Lavacharts\Configs\HorizontalAxis');

        $this->bc->hAxes(array($mockHorizontalAxis, $mockHorizontalAxis));

        $this->assertTrue(is_array($this->bc->getOption('hAxes')));
    }

    /**
     * @dataProvider nonArrayProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testHorizontalAxesWithBadTypes($badTypes)
    {
        $this->bc->hAxes($badTypes);
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testHorizontalAxesWithArrayOfBadTypes()
    {
        $this->bc->hAxes(array(1, 4.5, 'salmon'));
    }

    public function testHorizontalAxis()
    {
        $mockHorizontalAxis = m::mock('Khill\Lavacharts\Configs\HorizontalAxis');
        $mockHorizontalAxis->shouldReceive('toArray')->once()->andReturn(array(
            'hAxis' => array()
        ));

        $this->bc->hAxis($mockHorizontalAxis);

        $this->assertTrue(is_array($this->bc->getOption('hAxis')));
    }

    public function testOrientationWithValidInput()
    {
        $this->bc->orientation('horizontal');
        $this->assertEquals('horizontal', $this->bc->getOption('orientation'));

        $this->bc->orientation('vertical');
        $this->assertEquals('vertical', $this->bc->getOption('orientation'));
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testOrientationWithBadValue()
    {
        $this->bc->orientation('circles');
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testOrientationWithBadTypes($badTypes)
    {
        $this->bc->orientation($badTypes);
    }

    public function testIsStacked()
    {
        $this->bc->isStacked(true);

        $this->assertTrue($this->bc->getOption('isStacked'));
    }

    /**
     * @dataProvider nonBoolProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testIsStackedWithBadTypes($badTypes)
    {
        $this->bc->isStacked($badTypes);
    }

    public function testReverseCategories()
    {
        $this->bc->reverseCategories(true);

        $this->assertTrue($this->bc->getOption('reverseCategories'));
    }

    /**
     * @dataProvider nonBoolProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testReverseCategoriesWithBadTypes($badTypes)
    {
        $this->bc->reverseCategories($badTypes);
    }

    public function testSeries()
    {
        $mockSeries = m::mock('Khill\Lavacharts\Configs\Series');

        $this->bc->series(array($mockSeries, $mockSeries));

        $this->assertTrue(is_array($this->bc->getOption('series')));
    }

    /**
     * @dataProvider nonArrayProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testSeriesWithBadTypes($badTypes)
    {
        $this->bc->series($badTypes);
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testSeriesWithArrayOfBadTypes()
    {
        $this->bc->series(array(4, array(), 8.7));
    }

    public function testTheme()
    {
        $this->bc->theme('maximized');

        $this->assertEquals('maximized', $this->bc->getOption('theme'));
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testThemeWithBadValue()
    {
        $this->bc->theme('spaceTheme');
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testThemeWithBadTypes($badTypes)
    {
        $this->bc->theme($badTypes);
    }

    public function testVerticalAxes()
    {
        $mockVerticalAxis = m::mock('Khill\Lavacharts\Configs\VerticalAxis');

        $this->bc->vAxes(array($mockVerticalAxis, $mockVerticalAxis));

        $this->assertTrue(is_array($this->bc->getOption('vAxes')));
    }

    /**
     * @dataProvider nonArrayProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testVerticalAxesWithBadTypes($badTypes)
    {
        $this->bc->vAxes($badTypes);
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testVerticalAxesWithArrayOfBadTypes()
    {
        $this->bc->vAxes(array(false, 'truth'));
    }

    public function testVerticalAxis()
    {
        $mockVerticalAxis = m::mock('Khill\Lavacharts\Configs\VerticalAxis');
        $mockVerticalAxis->shouldReceive('toArray')->once()->andReturn(array(
            'vAxis' => array()
        ));

        $this->bc->vAxis($mockVerticalAxis);

        $this->assertTrue(is_array($this->bc->getOption('vAxis')));
    }
}
