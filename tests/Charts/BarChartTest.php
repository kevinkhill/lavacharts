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

        $this->assertTrue($this->cc->getOption('forceIFrame'));
    }

    /**
     * @dataProvider nonBoolProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testForceIFrameWithBadType($badTypes)
    {
        $this->cc->forceIFrame($badTypes);
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


    public function testVerticalAxis()
    {
        $mockVerticalAxis = m::mock('Khill\Lavacharts\Configs\VerticalAxis');
        $mockVerticalAxis->shouldReceive('toArray')->once()->andReturn(array(
            'vAxis' => array()
        ));

        $this->bc->vAxis($mockVerticalAxis);

        $this->assertTrue(is_array($this->bc->getOption('vAxis')));
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
