<?php

namespace Khill\Lavacharts\Tests\Configs;

use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Khill\Lavacharts\Configs\HorizontalAxis;

class HorizontalAxisTest extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->ha = new HorizontalAxis;

        $this->textStyleOptions = [
            'color'    => 'red',
            'fontName' => 'Arial',
            'fontSize' => 12,
            'italic'   => true
        ];
    }

    public function testConstructorValuesAssignment()
    {
        $ha = new HorizontalAxis([
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

        $this->assertEquals('#F4D4E7', $ha->baselineColor);
        $this->assertEquals(1, $ha->direction);
        $this->assertEquals('999.99', $ha->format);
        $this->assertInstanceOf('\Khill\Lavacharts\Configs\Gridline', $ha->gridlines);
        $this->assertTrue($ha->logScale);
        $this->assertEquals(2, $ha->maxAlternation);
        $this->assertEquals(3, $ha->maxTextLines);
        $this->assertEquals(5000, $ha->maxValue);
        $this->assertInstanceOf('\Khill\Lavacharts\Configs\Gridline', $ha->minorGridlines);
        $this->assertEquals(2, $ha->minTextSpacing);
        $this->assertEquals(50, $ha->minValue);
        $this->assertEquals(3, $ha->showTextEvery);
        $this->assertEquals('in', $ha->textPosition);
        $this->assertInstanceOf('\Khill\Lavacharts\Configs\TextStyle', $ha->textStyle);
        $this->assertEquals('Taco Graph', $ha->title);
        $this->assertInstanceOf('\Khill\Lavacharts\Configs\TextStyle', $ha->titleTextStyle);
        $this->assertEquals(100, $ha->viewWindow['viewWindowMin']);
        $this->assertEquals(400, $ha->viewWindow['viewWindowMax']);
        $this->assertEquals('explicit', $ha->viewWindowMode);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     */
    public function testConstructorWithInvalidPropertiesKey()
    {
        new HorizontalAxis(['Jellybeans' => []]);
    }

    public function testAllowContainerBoundaryTextCutoff()
    {
        $this->ha->allowContainerBoundaryTextCutoff(true);
        $this->assertTrue($this->ha->allowContainerBoundaryTextCutoff);
    }

    /**
     * @dataProvider nonBoolProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testAllowContainerBoundaryTextCutoffWithBadParams($badParams)
    {
        $this->ha->allowContainerBoundaryTextCutoff($badParams);
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
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
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testDirectionWithNonAcceptableInt()
    {
        $this->ha->direction(5);
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testDirectionWithBadParams($badParams)
    {
        $this->ha->direction($badParams);
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testFormatWithBadParams($badParams)
    {
        $this->ha->format($badParams);
    }

    public function testGridlinesWithAcceptableKeys()
    {
        $this->ha->gridlines([
            'color' => '#123ABC',
            'count' => 7
        ]);

        $this->assertEquals('#123ABC', $this->ha->gridlines->color);
        $this->assertEquals(7, $this->ha->gridlines->count);
    }

    public function testGridlinesWithAutoCount()
    {
        $this->ha->gridlines([
            'color' => '#123ABC',
            'count' => -1
        ]);
        $this->assertEquals(-1, $this->ha->gridlines['count']);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testGridlinesWithBadKeys()
    {
        $this->ha->gridlines([
            'frank'     => '#123ABC',
            'and beans' => 7
        ]);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testGridlinesWithBadValueForColor()
    {
        $this->ha->gridlines([
            'count' => 5,
            'color' => []
        ]);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testGridlinesWithBadValueForCount()
    {
        $this->ha->gridlines([
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
        $this->ha->gridlines($badParams);
    }

    /**
     * @dataProvider nonBoolProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testLogScaleWithBadParams($badParams)
    {
        $this->ha->logScale($badParams);
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testMaxAlternationWithBadParams($badParams)
    {
        $this->ha->maxAlternation($badParams);
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testMaxTextLinesWithBadParams($badParams)
    {
        $this->ha->maxTextLines($badParams);
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testMaxValueWithBadParams($badParams)
    {
        $this->ha->maxValue($badParams);
    }

    public function testMinorGridlinesWithAutoCount()
    {
        $this->ha->minorGridlines([
            'color' => '#123ABC',
            'count' => -1
        ]);
        $this->assertEquals(-1, $this->ha->minorGridlines['count']);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testMinorGridlinesWithBadKeys()
    {
        $this->ha->minorGridlines([
            'frank'     => '#123ABC',
            'and beans' => 7
        ]);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testMinorGridlinesWithBadValueForColor()
    {
        $this->ha->minorGridlines([
            'count' => 5,
            'color' => []
        ]);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testMinorGridlinesWithBadValueForCount()
    {
        $this->ha->minorGridlines([
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
        $this->ha->minorGridlines($badParams);
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testMinTextSpacingWithBadParams($badParams)
    {
        $this->ha->minTextSpacing($badParams);
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testMinValueWithBadParams($badParams)
    {
        $this->ha->minValue($badParams);
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
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
     * @depends testTextPositionWithValidValues
     */
    public function testSlantedText()
    {
        $this->ha->textPosition('out');

        $this->ha->slantedText(true);
        $this->assertTrue($this->ha->slantedText);
    }

    /**
     * @depends testTextPositionWithValidValues
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testSlantedTextWithTextPositionNotOut()
    {
        $this->ha->textPosition('in');

        $this->ha->slantedText(true);
        $this->assertTrue($this->ha->slantedText);
    }

    /**
     * @depends testTextPositionWithValidValues
     * @dataProvider nonBoolProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testSlantedTextWithBadParams($badParams)
    {
        $this->ha->textPosition('out');

        $this->ha->slantedText($badParams);
        $this->assertTrue($this->ha->slantedText);
    }

    public function testSlantedTextAngle()
    {
        $this->ha->slantedTextAngle(30);
        $this->assertEquals(30, $this->ha->slantedTextAngle);
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testSlantedTextAngleWithBadParams($badParams)
    {
        $this->ha->slantedTextAngle($badParams);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testSlantedTextAngleOutOfLowerLimit()
    {
        $this->ha->slantedTextAngle(0);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testSlantedTextAngleOutOfUpperLimit()
    {
        $this->ha->slantedTextAngle(95);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testTextPositionWithBadValue()
    {
        $this->ha->textPosition('underneath');
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testTextPositionWithBadParams($badParams)
    {
        $this->ha->textPosition($badParams);
    }

    /**
     * @dataProvider nonArrayProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testTextStyleWithBadParams($badParams)
    {
        $this->ha->textStyle($badParams);
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testTitleWithBadParams($badParams)
    {
        $this->ha->title($badParams);
    }

    /**
     * @dataProvider nonArrayProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testTitleTextStyleWithBadParams($badParams)
    {
        $this->ha->titleTextStyle($badParams);
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
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
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
        $this->ha->viewWindow([
            'min' => 10,
            'max' => 100
        ]);

        $this->assertEquals('explicit', $this->ha->viewWindowMode);
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testViewWindowModeWithBadParams($badParams)
    {
        $this->ha->viewWindowMode($badParams);
    }
}
