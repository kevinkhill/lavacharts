<?php namespace Lavacharts\Tests\Configs;

use \Lavacharts\Tests\ProvidersTestCase;
use \Lavacharts\Configs\VerticalAxis;

class VerticalAxisTest extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->va = new VerticalAxis(array());

        $this->mockTextStyle = $this->getMock(
            '\Lavacharts\Configs\TextStyle',
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
            'minorGridlines' => array(
                'color' => '#456EFF',
                'count' => 7
            ),
            'minTextSpacing' => 2,
            'maxAlternation' => 2,
            'maxTextLines'   => 3,
            'textPosition'   => 'in',
            'title'          => 'Taco Graph',
            'titleTextStyle' => $this->mockTextStyle,
            'textStyle'      => $this->mockTextStyle,
            'viewWindowMode' => 'explicit'
        ));

        $this->assertEquals('#F4D4E7', $va->baselineColor);
        $this->assertEquals(1, $va->direction);
        $this->assertEquals('999.99', $va->format);
        $this->assertEquals('#123ABC', $va->gridlines['color']);
        $this->assertEquals(4, $va->gridlines['count']);
        $this->assertTrue($va->logScale);
        $this->assertEquals('#456EFF', $va->minorGridlines['color']);
        $this->assertEquals(7, $va->minorGridlines['count']);
        $this->assertEquals(2, $va->minTextSpacing);
        $this->assertEquals(2, $va->maxAlternation);
        $this->assertEquals(3, $va->maxTextLines);
        $this->assertEquals('in', $va->textPosition);
        $this->assertTrue(is_array($va->textStyle));
        $this->assertEquals('Taco Graph', $va->title);
        $this->assertTrue(is_array($va->titleTextStyle));
        $this->assertEquals('explicit', $va->viewWindowMode);
    }

    /**
     * @expectedException Lavacharts\Exceptions\InvalidConfigProperty
     */
    public function testConstructorWithInvalidPropertiesKey()
    {
        new VerticalAxis(array('Jellybeans' => array()));
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testBaselineColorWithBadParams($badVals)
    {
        $this->va->baselineColor($badVals);
    }

    public function testDirectionWithNegativeOne()
    {
        $this->va->direction(-1);
        $this->assertEquals(-1, $this->va->direction);
    }

    /**
     * @expectedException Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testDirectionWithNonAcceptableInt()
    {
        $this->va->direction(5);
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testDirectionWithBadParams($badVals)
    {
        $this->va->direction($badVals);
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testFormatWithBadParams($badVals)
    {
        $this->va->format($badVals);
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
     * @expectedException Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testGridlinesWithBadKeys()
    {
        $this->va->gridlines(array(
            'frank'     => '#123ABC',
            'and beans' => 7
        ));
    }

    /**
     * @expectedException Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testGridlinesWithBadValueForColor()
    {
        $this->va->gridlines(array(
            'count' => 5,
            'color' => array()
        ));
    }

    /**
     * @expectedException Lavacharts\Exceptions\InvalidConfigValue
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
     * @expectedException Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testGridlinesWithBadParams($badVals)
    {
        $this->va->gridlines($badVals);
    }

    /**
     * @dataProvider nonBoolProvider
     * @expectedException Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testLogScaleWithBadParams($badVals)
    {
        $this->va->logScale($badVals);
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testMaxAlternationWithBadParams($badVals)
    {
        $this->va->maxAlternation($badVals);
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testMaxTextLinesWithBadParams($badVals)
    {
        $this->va->maxTextLines($badVals);
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
     * @expectedException Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testMinorGridlinesWithBadKeys()
    {
        $this->va->minorGridlines(array(
            'frank'     => '#123ABC',
            'and beans' => 7
        ));
    }

    /**
     * @expectedException Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testMinorGridlinesWithBadValueForColor()
    {
        $this->va->minorGridlines(array(
            'count' => 5,
            'color' => array()
        ));
    }

    /**
     * @expectedException Lavacharts\Exceptions\InvalidConfigValue
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
     * @expectedException Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testMinorGridlinesWithBadParams($badVals)
    {
        $this->va->minorGridlines($badVals);
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testMinTextSpacingWithBadParams($badVals)
    {
        $this->va->minTextSpacing($badVals);
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
     * @expectedException Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testTextPositionWithBadValue()
    {
        $this->va->textPosition('underneath');
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException Lavacharts\Exceptions\InvalidConfigValue
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
     * @expectedException Lavacharts\Exceptions\InvalidConfigValue
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

    public function testViewWindowModeWithValidValues()
    {
        $this->va->viewWindowMode('pretty');
        $this->assertEquals('pretty', $this->va->viewWindowMode);

        $this->va->viewWindowMode('maximized');
        $this->assertEquals('maximized', $this->va->viewWindowMode);

        $this->va->viewWindowMode('explicit');
        $this->assertEquals('explicit', $this->va->viewWindowMode);
    }

    public function testViewWindowModeWithBadValueAndViewWindowIsNull()
    {
        $this->va->viewWindowMode('bricks');

        $this->assertEquals('pretty', $this->va->viewWindowMode);
    }

    public function testViewWindowModeWithBadValueAndViewWindowIsSet()
    {
        $this->va->viewWindow([
            'min' => 10,
            'max' => 100
        ]);

        $this->va->viewWindowMode('samsung');

        $this->assertEquals('explicit', $this->va->viewWindowMode);
    }

    /**
     * @dataProvider nonStringProvider
     */
    public function testViewWindowModeWithBadParams($badParams)
    {
        $this->va->viewWindowMode($badParams);

        $this->assertEquals('pretty', $this->va->viewWindowMode);
    }

}
