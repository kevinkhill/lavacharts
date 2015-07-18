<?php

namespace Khill\Lavacharts\Tests\Charts;

use \Carbon\Carbon;
use \Mockery as m;

class ChartTest extends ChartTestCase
{
    public function setUp()
    {
        parent::setUp();

        $label = m::mock('\Khill\Lavacharts\Values\Label', ['TestChart'])->makePartial();

        $this->mockLineChart = m::mock('Khill\Lavacharts\Charts\LineChart', [
            $label, $this->partialDataTable
        ])->makePartial();
    }

    public function testLabelAssignedViaConstructor()
    {
        $this->assertEquals('TestChart', $this->mockLineChart->getLabel());
    }

    public function testDataTable()
    {
        $this->mockLineChart->datatable($this->partialDataTable);

        $this->assertInstanceOf('\Khill\Lavacharts\Configs\DataTable', $this->mockLineChart->getDatatable());
    }

    public function testBackgroundColorWithValidValues()
    {
        $this->mockLineChart->backgroundColor($this->getMockBackgroundColor());
        $this->assertTrue(is_array($this->mockLineChart->getOption('backgroundColor')));
    }

    /**
     * @dataProvider nonConfigObjectProvider
     * @expectedException PHPUnit_Framework_Error
     */
    public function testBackgroundColorWithBadTypes($badTypes)
    {
        $this->mockLineChart->backgroundColor($badTypes);
    }

    public function testChartAreaWithValidValues()
    {
        $this->mockLineChart->chartArea($this->getMockChartArea());
        $this->assertTrue(is_array($this->mockLineChart->getOption('chartArea')));
    }

    /**
     * @dataProvider nonConfigObjectProvider
     * @expectedException PHPUnit_Framework_Error
     */
    public function testChartAreaWithBadTypes($badTypes)
    {
        $this->mockLineChart->chartArea($badTypes);
    }

    public function testColorsWithValidValue()
    {
        $colors = ['green', 'red'];

        $this->mockLineChart->colors($colors);
        $this->assertEquals($colors, $this->mockLineChart->getOption('colors'));
    }

    /**
     * @dataProvider nonArrayProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testColorsWithBadTypes($badTypes)
    {
        $this->mockLineChart->colors($badTypes);
    }

    public function testFontNameWithValidValue()
    {
        $this->mockLineChart->fontName('Tahoma');
        $this->assertEquals('Tahoma', $this->mockLineChart->getOption('fontName'));
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testFontNameWithBadTypes($badTypes)
    {
        $this->mockLineChart->fontName($badTypes);
    }

    public function testFontSizeWithValidValue()
    {
        $this->mockLineChart->fontSize(34);
        $this->assertEquals(34, $this->mockLineChart->getOption('fontSize'));
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testFontSizeWithBadTypes($badTypes)
    {
        $this->mockLineChart->fontSize($badTypes);
    }

    public function testHeightWithValidValue()
    {
        $this->mockLineChart->height(500);
        $this->assertEquals(500, $this->mockLineChart->getOption('height'));
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testHeightWithBadTypes($badTypes)
    {
        $this->mockLineChart->height($badTypes);
    }

    public function testLegendWithValidValues()
    {
        $ml = m::mock('Khill\Lavacharts\Configs\Legend');
        $ml->shouldReceive('toArray')->once()->andReturn([
            'legend' => []
        ]);

        $this->mockLineChart->legend($ml);
        $this->assertTrue(is_array($this->mockLineChart->getOption('legend')));
    }

    /**
     * @dataProvider nonConfigObjectProvider
     * @expectedException PHPUnit_Framework_Error
     */
    public function testLegendWithBadTypes($badTypes)
    {
        $this->mockLineChart->legend($badTypes);
    }

    public function testTitleWithValidValue()
    {
        $this->mockLineChart->title('Fancy Chart');

        $this->assertEquals('Fancy Chart', $this->mockLineChart->getOption('title'));
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testTitleWithBadTypes($badTypes)
    {
        $this->mockLineChart->title($badTypes);
    }

    public function testTitlePositionWithValidValues()
    {
        $this->mockLineChart->titlePosition('in');
        $this->assertEquals('in', $this->mockLineChart->getOption('titlePosition'));

        $this->mockLineChart->titlePosition('out');
        $this->assertEquals('out', $this->mockLineChart->getOption('titlePosition'));

        $this->mockLineChart->titlePosition('none');
        $this->assertEquals('none', $this->mockLineChart->getOption('titlePosition'));
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testTitlePositionWithBadValue()
    {
        $this->mockLineChart->titlePosition('underneath');
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testTitlePositionWithBadTypes($badTypes)
    {
        $this->mockLineChart->titlePosition($badTypes);
    }

    public function testTitleTextStyleWithValidValues()
    {
        $mts = m::mock('Khill\Lavacharts\Configs\TextStyle');
        $mts->shouldReceive('toArray')->once()->andReturn([
            'titleTextStyle' => []
        ]);

        $this->mockLineChart->titleTextStyle($mts);
        $this->assertTrue(is_array($this->mockLineChart->getOption('titleTextStyle')));
    }

    /**
     * @dataProvider nonConfigObjectProvider
     * @expectedException PHPUnit_Framework_Error
     */
    public function testTitleTextStyleWithBadTypes($badTypes)
    {
        $this->mockLineChart->titleTextStyle($badTypes);
    }

    public function testTooltipWithValidValues()
    {
        $mtt = m::mock('Khill\Lavacharts\Configs\Tooltip');
        $mtt->shouldReceive('toArray')->once()->andReturn([
            'tooltip' => []
        ]);

        $this->mockLineChart->tooltip($mtt);
        $this->assertTrue(is_array($this->mockLineChart->getOption('tooltip')));
    }

    /**
     * @dataProvider nonConfigObjectProvider
     * @expectedException PHPUnit_Framework_Error
     */
    public function testTooltipWithBadTypes($badTypes)
    {
        $this->mockLineChart->tooltip($badTypes);
    }

    public function testWidthWithValidValue()
    {
        $this->mockLineChart->width(800);
        $this->assertEquals(800, $this->mockLineChart->getOption('width'));
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testWidthWithBadTypes($badTypes)
    {
        $this->mockLineChart->width($badTypes);
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

        $this->mockLineChart->setOptions($expected);

        $this->assertEquals($expected, $this->mockLineChart->getOptions());
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     */
    public function testSetOptionsWithArrayOfBadOptions()
    {
        $this->mockLineChart->setOptions([
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
        $this->mockLineChart->setOptions($badTypes);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testGetOptionsWithBadValue()
    {
        $this->mockLineChart->getOption('Bananas');
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testGetOptionsWithBadTypes($badTypes)
    {
        $this->mockLineChart->getOption($badTypes);
    }

    /**
     * @depends testTitleWithValidValue
     * @depends testWidthWithValidValue
     * @depends testHeightWithValidValue
     */
    public function testOptionsToJson()
    {
        $this->mockLineChart->title('My Cool Chart');
        $this->mockLineChart->width(1024);
        $this->mockLineChart->height(768);

        $expected = '{"title":"My Cool Chart","width":1024,"height":768}';

        $this->assertEquals($expected, $this->mockLineChart->optionsToJson());
    }
}
