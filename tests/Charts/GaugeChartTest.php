<?php namespace Khill\Lavacharts\Tests\Charts;

use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Khill\Lavacharts\Charts\GaugeChart;

class GaugeChartTest extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->g = new GaugeChart('Temps');
    }

    public function testInstanceOfGaugeChartWithType()
    {
        $this->assertInstanceOf('\Khill\Lavacharts\Charts\GaugeChart', $this->g);
    }

    public function testTypeGaugeChart()
    {
        $this->assertEquals('GaugeChart', $this->g->type);
    }

    public function testLabelAssignedViaConstructor()
    {
        $this->assertEquals('Temps', $this->g->label);
    }

    public function testForceIFrame()
    {
        $this->g->forceIFrame(true);

        $this->assertTrue($this->g->getOption('forceIFrame'));
    }

    /**
     * @dataProvider nonBoolProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testForceIFrameWithBadType($badTypes)
    {
        $this->g->forceIFrame($badTypes);
    }

    public function testGreenColor()
    {
        $this->g->greenColor('#FE9BC5');

        $this->assertEquals('#FE9BC5', $this->g->getOption('greenColor'));
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testGreenColorWithBadTypes($badTypes)
    {
        $this->g->greenColor($badTypes);
    }

    public function testGreenFrom()
    {
        $this->g->greenFrom(0);

        $this->assertEquals(0, $this->g->getOption('greenFrom'));
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testGreenFromWithBadTypes($badTypes)
    {
        $this->g->greenFrom($badTypes);
    }

    public function testGreenTo()
    {
        $this->g->greenTo(100);

        $this->assertEquals(100, $this->g->getOption('greenTo'));
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testGreenToWithBadTypes($badTypes)
    {
        $this->g->greenTo($badTypes);
    }

    public function testMajorTicks()
    {
        $this->g->majorTicks(array(
            'Safe',
            'Ok',
            'Danger',
            'Critical'
        ));

        $this->assertEquals(array(
            'Safe',
            'Ok',
            'Danger',
            'Critical'
        ), $this->g->getOption('majorTicks'));
    }

    /**
     * @dataProvider nonArrayProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testMajorTicksWithBadTypes($badTypes)
    {
        $this->g->majorTicks($badTypes);
    }

    public function testMax()
    {
        $this->g->max(100);

        $this->assertEquals(100, $this->g->getOption('max'));
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testMaxWithBadTypes($badTypes)
    {
        $this->g->max($badTypes);
    }

    public function testMin()
    {
        $this->g->min(1);

        $this->assertEquals(1, $this->g->getOption('min'));
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testMinWithBadTypes($badTypes)
    {
        $this->g->min($badTypes);
    }

    public function testMinorTicks()
    {
        $this->g->minorTicks(5);

        $this->assertEquals(5, $this->g->getOption('minorTicks'));
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testMinorTicksWithBadTypes($badTypes)
    {
        $this->g->minorTicks($badTypes);
    }

    public function testRedColor()
    {
        $this->g->redColor('#43F9C1');

        $this->assertEquals('#43F9C1', $this->g->getOption('redColor'));
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testRedColorWithBadTypes($badTypes)
    {
        $this->g->redColor($badTypes);
    }

    public function testRedFrom()
    {
        $this->g->redFrom(0);

        $this->assertEquals(0, $this->g->getOption('redFrom'));
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testRedFromWithBadTypes($badTypes)
    {
        $this->g->redFrom($badTypes);
    }

    public function testRedTo()
    {
        $this->g->redTo(100);

        $this->assertEquals(100, $this->g->getOption('redTo'));
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testRedToWithBadTypes($badTypes)
    {
        $this->g->redTo($badTypes);
    }

    public function testYellowColor()
    {
        $this->g->yellowColor('#00FB3C');

        $this->assertEquals('#00FB3C', $this->g->getOption('yellowColor'));
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testYellowColorWithBadTypes($badTypes)
    {
        $this->g->yellowColor($badTypes);
    }

    public function testYellowFrom()
    {
        $this->g->yellowFrom(0);

        $this->assertEquals(0, $this->g->getOption('yellowFrom'));
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testYellowFromWithBadTypes($badTypes)
    {
        $this->g->yellowFrom($badTypes);
    }

    public function testYellowTo()
    {
        $this->g->yellowTo(100);

        $this->assertEquals(100, $this->g->getOption('yellowTo'));
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testYellowToWithBadTypes($badTypes)
    {
        $this->g->yellowTo($badTypes);
    }
}
