<?php

namespace Khill\Lavacharts\Tests\Configs;

use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Khill\Lavacharts\Configs\VerticalAxis;

class VerticalAxisTest extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->va = new VerticalAxis;

        $this->textStyleOptions = [
            'color'    => 'red',
            'fontName' => 'Arial',
            'fontSize' => 12,
            'italic'   => true
        ];
    }

    public function testConstructorValuesAssignment()
    {
        $va = new VerticalAxis([
            'baselineColor'  => '#F4D4E7',
            'direction'      => 1,
            'format'         => '999.99',
            'gridlines'      => [
                'color' => '#123ABC',
                'count' => 4
            ],
            'logScale'       => true,
            'maxAlternation' => 2,
            'maxTextLines'   => 3,
            'maxValue'       => 5000,
            'minorGridlines' => [
                'color' => '#456EFF',
                'count' => 7
            ],
            'minTextSpacing' => 2,
            'minValue'       => 50,
            'showTextEvery'  => 3,
            'textPosition'   => 'in',
            'title'          => 'Taco Graph',
            'titleTextStyle' => $this->textStyleOptions,
            'textStyle'      => $this->textStyleOptions,
            'viewWindow'     => [
                'min' => 100,
                'max' => 400
            ],
            'viewWindowMode' => 'explicit'
        ]);

        $this->assertEquals('#F4D4E7', $va->baselineColor);
        $this->assertEquals(1, $va->direction);
        $this->assertEquals('999.99', $va->format);
        $this->assertInstanceOf('\Khill\Lavacharts\Configs\Gridlines', $va->gridlines);
        $this->assertTrue($va->logScale);
        $this->assertEquals(2, $va->maxAlternation);
        $this->assertEquals(3, $va->maxTextLines);
        $this->assertEquals(5000, $va->maxValue);
        $this->assertInstanceOf('\Khill\Lavacharts\Configs\Gridlines', $va->minorGridlines);
        $this->assertEquals(2, $va->minTextSpacing);
        $this->assertEquals(50, $va->minValue);
        $this->assertEquals(3, $va->showTextEvery);
        $this->assertEquals('in', $va->textPosition);
        $this->assertInstanceOf('\Khill\Lavacharts\Configs\TextStyle', $va->textStyle);
        $this->assertEquals('Taco Graph', $va->title);
<<<<<<< HEAD
        $this->assertTrue(is_array($va->titleTextStyle));
        $this->assertEquals(100, $va->viewWindow['min']);
        $this->assertEquals(400, $va->viewWindow['max']);
||||||| merged common ancestors
        $this->assertTrue(is_array($va->titleTextStyle));
        $this->assertEquals(100, $va->viewWindow['viewWindowMin']);
        $this->assertEquals(400, $va->viewWindow['viewWindowMax']);
=======
        $this->assertInstanceOf('\Khill\Lavacharts\Configs\TextStyle', $va->titleTextStyle);
        $this->assertEquals(100, $va->viewWindow['viewWindowMin']);
        $this->assertEquals(400, $va->viewWindow['viewWindowMax']);
>>>>>>> 3.0
        $this->assertEquals('explicit', $va->viewWindowMode);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     */
    public function testConstructorWithInvalidPropertiesKey()
    {
        new VerticalAxis(['Jellybeans' => []]);
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
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
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testDirectionWithNonAcceptableInt()
    {
        $this->va->direction(5);
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testDirectionWithBadParams($badParams)
    {
        $this->va->direction($badParams);
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testFormatWithBadParams($badParams)
    {
        $this->va->format($badParams);
    }

    public function testGridlinesWithAcceptableKeys()
    {
        $this->va->gridlines([
            'color' => '#123ABC',
            'count' => 7
        ]);

        $this->assertEquals('#123ABC', $this->va->gridlines->color);
        $this->assertEquals(7, $this->va->gridlines->count);
    }

    public function testGridlinesWithAutoCount()
    {
        $this->va->gridlines([
            'color' => '#123ABC',
            'count' => -1
        ]);
        $this->assertEquals(-1, $this->va->gridlines->count);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     */
    public function testGridlinesWithBadKeys()
    {
        $this->va->gridlines([
            'frank'     => '#123ABC',
            'and beans' => 7
        ]);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testGridlinesWithBadValueForColor()
    {
        $this->va->gridlines([
            'count' => 5,
            'color' => []
        ]);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testGridlinesWithBadValueForCount()
    {
        $this->va->gridlines([
            'count' => 9.8,
            'color' => '#123ABC'
        ]);
    }

    /**
     * @dataProvider nonArrayProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testGridlinesWithBadParams($badParams)
    {
        $this->va->gridlines($badParams);
    }

    /**
     * @dataProvider nonBoolProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testLogScaleWithBadParams($badParams)
    {
        $this->va->logScale($badParams);
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testMaxAlternationWithBadParams($badParams)
    {
        $this->va->maxAlternation($badParams);
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testMaxTextLinesWithBadParams($badParams)
    {
        $this->va->maxTextLines($badParams);
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testMaxValueWithBadParams($badParams)
    {
        $this->va->maxValue($badParams);
    }

    public function testMinorGridlinesWithAutoCount()
    {
        $this->va->minorGridlines([
            'color' => '#123ABC',
            'count' => -1
        ]);
        $this->assertEquals(-1, $this->va->minorGridlines->count);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     */
    public function testMinorGridlinesWithBadKeys()
    {
        $this->va->minorGridlines([
            'frank'     => '#123ABC',
            'and beans' => 7
        ]);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testMinorGridlinesWithBadValueForColor()
    {
        $this->va->minorGridlines([
            'count' => 5,
            'color' => []
        ]);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testMinorGridlinesWithBadValueForCount()
    {
        $this->va->minorGridlines([
            'count' => 9.8,
            'color' => '#123ABC'
        ]);
    }

    /**
     * @dataProvider nonArrayProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testMinorGridlinesWithBadParams($badParams)
    {
        $this->va->minorGridlines($badParams);
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testMinTextSpacingWithBadParams($badParams)
    {
        $this->va->minTextSpacing($badParams);
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testMinValueWithBadParams($badParams)
    {
        $this->va->minValue($badParams);
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
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
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testTextPositionWithBadValue()
    {
        $this->va->textPosition('underneath');
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testTextPositionWithBadParams($badParams)
    {
        $this->va->textPosition($badParams);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testTextStyleWithBadParams()
    {
        $this->va->textStyle('not a TextStyle object');
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testTitleWithBadParams($badParams)
    {
        $this->va->title($badParams);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testTitleTextStyleWithBadParams()
    {
        $this->va->titleTextStyle('not a TextStyle object');
    }

    public function testViewWindowWithValidValues()
    {
        $this->va->viewWindow([
            'min' => 10,
            'max' => 100
        ]);

        $this->assertEquals(10, $this->va->viewWindow['min']);
        $this->assertEquals(100, $this->va->viewWindow['max']);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testViewWindowWithInvalidArrayKeys()
    {
        $this->va->viewWindow([
            'gunderfluffen' => 10
        ]);
    }

    /**
     * @dataProvider nonArrayProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
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
        $this->va->viewWindow([
            'min' => 10,
            'max' => 100
        ]);

        $this->assertEquals('explicit', $this->va->viewWindowMode);
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testViewWindowModeWithBadParams($badParams)
    {
        $this->va->viewWindowMode($badParams);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testViewWindowModeWithNonAcceptableParam()
    {
        $this->va->viewWindowMode('eggs');
    }
}
