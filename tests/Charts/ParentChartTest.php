<?php

namespace Khill\Lavacharts\Tests\Charts;

use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Mockery as m;

class ChartAndTraitsTest extends ProvidersTestCase
{
    public $mockChart;

    public function setUp()
    {
        parent::setUp();

        $label = m::mock('\Khill\Lavacharts\Values\Label', ['TestChart'])->makePartial();

        $this->mockChart = new MockChart($label, $this->partialDataTable);
    }

    public function testLabelAssignedViaConstructor()
    {
        $this->assertEquals('TestChart', (string) $this->mockChart->getLabel());
    }

    public function testDataTable()
    {
        $this->mockChart->datatable($this->partialDataTable);

        $this->assertInstanceOf('\Khill\Lavacharts\DataTables\DataTable', $this->mockChart->getDataTable());
    }

    public function testBackgroundColorWithValidValues()
    {
        $this->mockChart->backgroundColor([
            'fill' => '#CCC'
        ]);

        $this->assertInstanceOf('\Khill\Lavacharts\Configs\BackgroundColor', $this->mockChart->backgroundColor);
        $this->assertEquals('#CCC', $this->mockChart->backgroundColor->fill);
    }

    /**
     * @dataProvider nonArrayProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testBackgroundColorWithBadTypes($badTypes)
    {
        $this->mockChart->backgroundColor($badTypes);
    }

    public function testChartAreaWithValidValues()
    {
        $this->mockChart->chartArea([]);

        $this->assertInstanceOf('\Khill\Lavacharts\Configs\ChartArea', $this->mockChart->chartArea);
    }

    /**
     * @dataProvider nonArrayProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testChartAreaWithBadTypes($badTypes)
    {
        $this->mockChart->chartArea($badTypes);
    }

    public function testColorsWithValidValue()
    {
        $colors = ['green', 'red'];

        $this->mockChart->colors($colors);

        $this->assertEquals($colors, $this->mockChart->colors);
    }

    /**
     * @dataProvider nonArrayProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testColorsWithBadTypes($badTypes)
    {
        $this->mockChart->colors($badTypes);
    }

    public function testFontNameWithValidValue()
    {
        $this->mockChart->fontName('Tahoma');

        $this->assertEquals('Tahoma', $this->mockChart->fontName);
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testFontNameWithBadTypes($badTypes)
    {
        $this->mockChart->fontName($badTypes);
    }

    public function testFontSizeWithValidValue()
    {
        $this->mockChart->fontSize(34);
        $this->assertEquals(34, $this->mockChart->fontSize);
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testFontSizeWithBadTypes($badTypes)
    {
        $this->mockChart->fontSize($badTypes);
    }

    public function testHeightWithValidValue()
    {
        $this->mockChart->height(500);
        $this->assertEquals(500, $this->mockChart->height);
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testHeightWithBadTypes($badTypes)
    {
        $this->mockChart->height($badTypes);
    }

    public function testLegendWithValidValues()
    {
        $this->mockChart->legend([]);

        $this->assertInstanceOf('\Khill\Lavacharts\Configs\Legend', $this->mockChart->legend);
    }

    /**
     * @dataProvider nonArrayProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testLegendWithBadTypes($badTypes)
    {
        $this->mockChart->legend($badTypes);
    }

    public function testTitleWithValidValue()
    {
        $this->mockChart->title('Fancy Chart');

        $this->assertEquals('Fancy Chart', $this->mockChart->title);
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testTitleWithBadTypes($badTypes)
    {
        $this->mockChart->title($badTypes);
    }

    public function testTitlePositionWithValidValues()
    {
        $this->mockChart->titlePosition('in');
        $this->assertEquals('in', $this->mockChart->titlePosition);

        $this->mockChart->titlePosition('out');
        $this->assertEquals('out', $this->mockChart->titlePosition);

        $this->mockChart->titlePosition('none');
        $this->assertEquals('none', $this->mockChart->titlePosition);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testTitlePositionWithBadValue()
    {
        $this->mockChart->titlePosition('underneath');
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testTitlePositionWithBadTypes($badTypes)
    {
        $this->mockChart->titlePosition($badTypes);
    }

    public function testTitleTextStyleWithValidValues()
    {
        $this->mockChart->titleTextStyle([]);

        $this->assertInstanceOf('\Khill\Lavacharts\Configs\TextStyle', $this->mockChart->titleTextStyle);
    }

    /**
     * @dataProvider nonArrayProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testTitleTextStyleWithBadTypes($badTypes)
    {
        $this->mockChart->titleTextStyle($badTypes);
    }

    public function testTooltipWithValidValues()
    {
        $this->mockChart->tooltip([]);

        $this->assertInstanceOf('\Khill\Lavacharts\Configs\Tooltip', $this->mockChart->tooltip);
    }

    /**
     * @dataProvider nonArrayProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testTooltipWithBadTypes($badTypes)
    {
        $this->mockChart->tooltip($badTypes);
    }

    public function testWidthWithValidValue()
    {
        $this->mockChart->width(800);
        $this->assertEquals(800, $this->mockChart->width);
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testWidthWithBadTypes($badTypes)
    {
        $this->mockChart->width($badTypes);
    }

    /**
     * @depends testTitleWithValidValue
     * @depends testWidthWithValidValue
     * @depends testHeightWithValidValue
     */
    public function testSetOptionsWithArrayOfValidOptions()
    {
        $expected = [
            'title' => 'My Cool Chart',
            'width' => 1024,
            'height' => 768
        ];

        $this->mockChart->setOptions($expected);

        $this->assertEquals($expected, $this->mockChart->getOptions()->getValues());
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     */
    public function testSetOptionsWithArrayOfBadOptions()
    {
        $this->mockChart->setOptions([
            'tibtle' => 'My Cool Chart',
            'widmth' => 1024,
            'heaight' => 768
        ]);
    }

    /**
     * @dataProvider nonArrayProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testSetOptionsWithBadTypes($badTypes)
    {
        $this->mockChart->setOptions($badTypes);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidOption
     */
    public function testGettingNonExistentOptionValue()
    {
        $this->mockChart->bananas;
    }

    /**
     * @depends testTitleWithValidValue
     * @depends testWidthWithValidValue
     * @depends testHeightWithValidValue
     */
    public function testOptionsToJson()
    {
        $this->mockChart->title('My Cool Chart');
        $this->mockChart->width(1024);
        $this->mockChart->height(768);

        $expected = '{"title":"My Cool Chart","width":1024,"height":768}';

        $this->assertEquals($expected, json_encode($this->mockChart));
    }
}
