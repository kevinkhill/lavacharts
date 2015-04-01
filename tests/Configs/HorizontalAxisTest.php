<?php namespace Khill\Lavacharts\Tests\Configs;

use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Khill\Lavacharts\Configs\HorizontalAxis;

class HorizontalAxisTest extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->ha = new HorizontalAxis(array());

        $this->mockTextStyle = $this->getMock(
            '\Khill\Lavacharts\Configs\TextStyle',
            array('__construct')
        );
    }

    public function testConstructorValuesAssignment()
    {
        $va = new HorizontalAxis(array(
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
        new HorizontalAxis(array('Jellybeans' => array()));
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testBaselineColorWithBadParams($badParams)
    {
        $this->ha->baselineColor($badParams);
    }

    public function testDirectionWithNegativeOne()
    {
        $this->ha->direction(-1);
        $this->assertEquals(-1, $this->ha->direction);
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testDirectionWithNonAcceptableInt()
    {
        $this->ha->direction(5);
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testDirectionWithBadParams($badParams)
    {
        $this->ha->direction($badParams);
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testFormatWithBadParams($badParams)
    {
        $this->ha->format($badParams);
    }

    public function testGridlinesWithAcceptableKeys()
    {
        $this->ha->gridlines(array(
            'color' => '#123ABC',
            'count' => 7
        ));

        $this->assertEquals('#123ABC', $this->ha->gridlines['color']);
        $this->assertEquals(7, $this->ha->gridlines['count']);
    }

    public function testGridlinesWithAutoCount()
    {
        $this->ha->gridlines(array(
            'color' => '#123ABC',
            'count' => -1
        ));
        $this->assertEquals(-1, $this->ha->gridlines['count']);
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testGridlinesWithBadKeys()
    {
        $this->ha->gridlines(array(
            'frank'     => '#123ABC',
            'and beans' => 7
        ));
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testGridlinesWithBadValueForColor()
    {
        $this->ha->gridlines(array(
            'count' => 5,
            'color' => array()
        ));
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testGridlinesWithBadValueForCount()
    {
        $this->ha->gridlines(array(
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
        $this->ha->gridlines($badParams);
    }

    /**
     * @dataProvider nonBoolProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testLogScaleWithBadParams($badParams)
    {
        $this->ha->logScale($badParams);
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testMaxAlternationWithBadParams($badParams)
    {
        $this->ha->maxAlternation($badParams);
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testMaxTextLinesWithBadParams($badParams)
    {
        $this->ha->maxTextLines($badParams);
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testMaxValueWithBadParams($badParams)
    {
        $this->ha->maxValue($badParams);
    }

    public function testMinorGridlinesWithAutoCount()
    {
        $this->ha->minorGridlines(array(
            'color' => '#123ABC',
            'count' => -1
        ));
        $this->assertEquals(-1, $this->ha->minorGridlines['count']);
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testMinorGridlinesWithBadKeys()
    {
        $this->ha->minorGridlines(array(
            'frank'     => '#123ABC',
            'and beans' => 7
        ));
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testMinorGridlinesWithBadValueForColor()
    {
        $this->ha->minorGridlines(array(
            'count' => 5,
            'color' => array()
        ));
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testMinorGridlinesWithBadValueForCount()
    {
        $this->ha->minorGridlines(array(
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
        $this->ha->minorGridlines($badParams);
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testMinTextSpacingWithBadParams($badParams)
    {
        $this->ha->minTextSpacing($badParams);
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testMinValueWithBadParams($badParams)
    {
        $this->ha->minValue($badParams);
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testShowTextEveryWithBadParams($badParams)
    {
        $this->ha->showTextEvery($badParams);
    }

    public function testTextPositionWithValidValues()
    {
        $this->ha->textPosition('out');
        $this->assertEquals('out', $this->ha->textPosition);

        $this->ha->textPosition('in');
        $this->assertEquals('in', $this->ha->textPosition);

        $this->ha->textPosition('none');
        $this->assertEquals('none', $this->ha->textPosition);
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testTextPositionWithBadValue()
    {
        $this->ha->textPosition('underneath');
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testTextPositionWithBadParams($badParams)
    {
        $this->ha->textPosition($badParams);
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testTextStyleWithBadParams()
    {
        $this->ha->textStyle('not a TextStyle object');
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testTitleWithBadParams($badParams)
    {
        $this->ha->title($badParams);
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testTitleTextStyleWithBadParams()
    {
        $this->ha->titleTextStyle('not a TextStyle object');
    }

    public function testViewWindowModeWithValidValues()
    {
        $this->ha->viewWindowMode('pretty');
        $this->assertEquals('pretty', $this->ha->viewWindowMode);

        $this->ha->viewWindowMode('maximized');
        $this->assertEquals('maximized', $this->ha->viewWindowMode);

        $this->ha->viewWindowMode('explicit');
        $this->assertEquals('explicit', $this->ha->viewWindowMode);
    }

    /**
     * @dataProvider nonArrayProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testViewWindowWithBadParams($badParams)
    {
        $this->ha->viewWindow($badParams);
    }

    /**
     * @depends testConstructorValuesAssignment
     */
    public function testViewWindowModeWithViewWindowSet()
    {
        $this->ha->viewWindow(array(
            'min' => 10,
            'max' => 100
        ));

        $this->assertEquals('explicit', $this->ha->viewWindowMode);
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testViewWindowModeWithBadParams($badParams)
    {
        $this->ha->viewWindowMode($badParams);
    }
}
