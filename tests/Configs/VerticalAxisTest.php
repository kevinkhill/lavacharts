<?php namespace Khill\Lavacharts\Tests\Configs;

use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Khill\Lavacharts\Configs\VerticalAxis;

class VerticalAxisTest extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->va = new VerticalAxis(array());

        $this->mockTextStyle = $this->getMock(
            '\Khill\Lavacharts\Configs\TextStyle',
            array('__construct')
        );
    }

    public function testConstructorValuesAssignment()
    {
        $va = new VerticalAxis(array(
            'baselineColor'  => '#F4D4E7',
            'direction'      => 1,
            'format'         => '999.99',
            'gridlines'      => array(
                'color' => '#123ABC',
                'count' => 4
            ),
            'logScale'       => true,
            'maxAlternation' => 2,
            'maxTextLines'   => 3,
            'maxValue'       => 5000,
            'minorGridlines' => array(
                'color' => '#456EFF',
                'count' => 7
            ),
            'minTextSpacing' => 2,
            'minValue'       => 50,
            'showTextEvery'  => 3,
            'textPosition'   => 'in',
            'title'          => 'Taco Graph',
            'titleTextStyle' => $this->mockTextStyle,
            'textStyle'      => $this->mockTextStyle,
            'viewWindow'     => array(
                'min' => 100,
                'max' => 400
            ),
            'viewWindowMode' => 'explicit'
        ));

        $this->assertEquals('#F4D4E7', $va->baselineColor);
        $this->assertEquals(1, $va->direction);
        $this->assertEquals('999.99', $va->format);
        $this->assertEquals('#123ABC', $va->gridlines['color']);
        $this->assertEquals(4, $va->gridlines['count']);
        $this->assertTrue($va->logScale);
        $this->assertEquals(2, $va->maxAlternation);
        $this->assertEquals(3, $va->maxTextLines);
        $this->assertEquals(5000, $va->maxValue);
        $this->assertEquals('#456EFF', $va->minorGridlines['color']);
        $this->assertEquals(7, $va->minorGridlines['count']);
        $this->assertEquals(2, $va->minTextSpacing);
        $this->assertEquals(50, $va->minValue);
        $this->assertEquals(3, $va->showTextEvery);
        $this->assertEquals('in', $va->textPosition);
        $this->assertTrue(is_array($va->textStyle));
        $this->assertEquals('Taco Graph', $va->title);
        $this->assertTrue(is_array($va->titleTextStyle));
        $this->assertEquals(100, $va->viewWindow['viewWindowMin']);
        $this->assertEquals(400, $va->viewWindow['viewWindowMax']);
        $this->assertEquals('explicit', $va->viewWindowMode);
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigProperty
     */
    public function testConstructorWithInvalidPropertiesKey()
    {
        new VerticalAxis(array('Jellybeans' => array()));
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testBaselineColorWithBadParams($badParams)
    {
        $this->va->baselineColor($badParams);
    }

    public function testDirectionWithNegativeOne()
    {
        $this->va->direction(-1);
        $this->assertEquals(-1, $this->va->direction);
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testDirectionWithNonAcceptableInt()
    {
        $this->va->direction(5);
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testDirectionWithBadParams($badParams)
    {
        $this->va->direction($badParams);
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testFormatWithBadParams($badParams)
    {
        $this->va->format($badParams);
    }

    public function testGridlinesWithAcceptableKeys()
    {
        $this->va->gridlines(array(
            'color' => '#123ABC',
            'count' => 7
        ));

        $this->assertEquals('#123ABC', $this->va->gridlines['color']);
        $this->assertEquals(7, $this->va->gridlines['count']);
    }

    public function testGridlinesWithAutoCount()
    {
        $this->va->gridlines(array(
            'color' => '#123ABC',
            'count' => -1
        ));
        $this->assertEquals(-1, $this->va->gridlines['count']);
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testGridlinesWithBadKeys()
    {
        $this->va->gridlines(array(
            'frank'     => '#123ABC',
            'and beans' => 7
        ));
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testGridlinesWithBadValueForColor()
    {
        $this->va->gridlines(array(
            'count' => 5,
            'color' => array()
        ));
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testGridlinesWithBadValueForCount()
    {
        $this->va->gridlines(array(
            'count' => 9.8,
            'color' => '#123ABC'
        ));
    }

    /**
     * @dataProvider nonArrayProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testGridlinesWithBadParams($badParams)
    {
        $this->va->gridlines($badParams);
    }

    /**
     * @dataProvider nonBoolProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testLogScaleWithBadParams($badParams)
    {
        $this->va->logScale($badParams);
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testMaxAlternationWithBadParams($badParams)
    {
        $this->va->maxAlternation($badParams);
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testMaxTextLinesWithBadParams($badParams)
    {
        $this->va->maxTextLines($badParams);
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testMaxValueWithBadParams($badParams)
    {
        $this->va->maxValue($badParams);
    }

    public function testMinorGridlinesWithAutoCount()
    {
        $this->va->minorGridlines(array(
            'color' => '#123ABC',
            'count' => -1
        ));
        $this->assertEquals(-1, $this->va->minorGridlines['count']);
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testMinorGridlinesWithBadKeys()
    {
        $this->va->minorGridlines(array(
            'frank'     => '#123ABC',
            'and beans' => 7
        ));
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testMinorGridlinesWithBadValueForColor()
    {
        $this->va->minorGridlines(array(
            'count' => 5,
            'color' => array()
        ));
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testMinorGridlinesWithBadValueForCount()
    {
        $this->va->minorGridlines(array(
            'count' => 9.8,
            'color' => '#123ABC'
        ));
    }

    /**
     * @dataProvider nonArrayProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testMinorGridlinesWithBadParams($badParams)
    {
        $this->va->minorGridlines($badParams);
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testMinTextSpacingWithBadParams($badParams)
    {
        $this->va->minTextSpacing($badParams);
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testMinValueWithBadParams($badParams)
    {
        $this->va->minValue($badParams);
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testShowTextEveryWithBadParams($badParams)
    {
        $this->va->showTextEvery($badParams);
    }

    public function testTextPositionWithValidValues()
    {
        $this->va->textPosition('out');
        $this->assertEquals('out', $this->va->textPosition);

        $this->va->textPosition('in');
        $this->assertEquals('in', $this->va->textPosition);

        $this->va->textPosition('none');
        $this->assertEquals('none', $this->va->textPosition);
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testTextPositionWithBadValue()
    {
        $this->va->textPosition('underneath');
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testTextPositionWithBadParams($badParams)
    {
        $this->va->textPosition($badParams);
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testTextStyleWithBadParams()
    {
        $this->va->textStyle('not a TextStyle object');
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testTitleWithBadParams($badParams)
    {
        $this->va->title($badParams);
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testTitleTextStyleWithBadParams()
    {
        $this->va->titleTextStyle('not a TextStyle object');
    }

    public function testViewWindowWithValidValues()
    {
        $this->va->viewWindow(array(
            'min' => 10,
            'max' => 100
        ));

        $this->assertEquals(10, $this->va->viewWindow['viewWindowMin']);
        $this->assertEquals(100, $this->va->viewWindow['viewWindowMax']);
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testViewWindowWithInvalidArrayKeys()
    {
        $this->va->viewWindow(array(
            'gunderfluffen' => 10
        ));
    }

    /**
     * @dataProvider nonArrayProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testViewWindowWithBadParams($badParams)
    {
        $this->va->viewWindow($badParams);
    }

    public function testViewWindowModeWithValidValues()
    {
        $this->va->viewWindowMode('pretty');
        $this->assertEquals('pretty', $this->va->viewWindowMode);

        $this->va->viewWindowMode('maximized');
        $this->assertEquals('maximized', $this->va->viewWindowMode);

        $this->va->viewWindowMode('explicit');
        $this->assertEquals('explicit', $this->va->viewWindowMode);
    }

    /**
     * @depends testConstructorValuesAssignment
     */
    public function testViewWindowModeWithViewWindowSet()
    {
        $this->va->viewWindow(array(
            'min' => 10,
            'max' => 100
        ));

        $this->assertEquals('explicit', $this->va->viewWindowMode);
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testViewWindowModeWithBadParams($badParams)
    {
        $this->va->viewWindowMode($badParams);
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testViewWindowModeWithNonAcceptableParam()
    {
        $this->va->viewWindowMode('eggs');
    }
}
