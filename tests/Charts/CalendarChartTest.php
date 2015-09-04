<?php

namespace Khill\Lavacharts\Tests\Charts;

use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Khill\Lavacharts\Charts\CalendarChart;

class CalendarChartTest extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $label = \Mockery::mock('\Khill\Lavacharts\Values\Label', ['MyTestChart'])->makePartial();

        $this->CalendarChart = new CalendarChart($label, $this->partialDataTable);
    }

    public function testInstanceOfLineChartWithType()
    {
        $this->assertInstanceOf('\Khill\Lavacharts\Charts\CalendarChart', $this->CalendarChart);
    }

    public function testTypeLineChart()
    {
        $chart = $this->CalendarChart;

        $this->assertEquals('CalendarChart', $chart::TYPE);
    }

    public function testLabelAssignedViaConstructor()
    {
        $this->assertEquals('MyTestChart', (string) $this->CalendarChart->getLabel());
    }

    public function testCellColor()
    {
        $this->CalendarChart->cellColor([]);

        $this->assertInstanceOf('\Khill\Lavacharts\Configs\Stroke', $this->CalendarChart->cellColor);
    }

    public function testCellSize()
    {
        $this->CalendarChart->cellSize(3);

        $this->assertEquals(3, $this->CalendarChart->cellSize);
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testCellSizeWithBadType($badTypes)
    {
        $this->CalendarChart->cellSize($badTypes);
    }

    public function testDayOfWeekLabel()
    {
        $this->CalendarChart->dayOfWeekLabel([
            'fontSize' => 12
        ]);

        $this->assertInstanceOf('\Khill\Lavacharts\Configs\TextStyle', $this->CalendarChart->dayOfWeekLabel);
        $this->assertEquals(12, $this->CalendarChart->dayOfWeekLabel->fontSize);
    }

    /**
     * @dataProvider nonArrayProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testDayOfWeekLabelWithBadTypes($badVals)
    {
        $this->CalendarChart->dayOfWeekLabel($badVals);
    }

    public function testDayOfWeekRightSpace()
    {
        $this->CalendarChart->dayOfWeekRightSpace(5);

        $this->assertEquals(5, $this->CalendarChart->dayOfWeekRightSpace);
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testDayOfWeekRightSpaceWithBadType($badTypes)
    {
        $this->CalendarChart->dayOfWeekRightSpace($badTypes);
    }

    public function testDaysOfWeek()
    {
        $this->CalendarChart->daysOfWeek('MAWEFWA');

        $this->assertEquals('MAWEFWA', $this->CalendarChart->daysOfWeek);
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testDaysOfWeekWithBadType($badTypes)
    {
        $this->CalendarChart->daysOfWeek($badTypes);
    }

    public function testFocusedCellColor()
    {
        $this->CalendarChart->focusedCellColor([]);

        $this->assertInstanceOf('\Khill\Lavacharts\Configs\Stroke', $this->CalendarChart->focusedCellColor);
    }

    public function testMonthLabel()
    {
        $this->CalendarChart->monthLabel([
            'fontName' => 'Arial'
        ]);

        $this->assertInstanceOf('\Khill\Lavacharts\Configs\TextStyle', $this->CalendarChart->monthLabel);
        $this->assertEquals('Arial', $this->CalendarChart->monthLabel->fontName);
    }

    /**
     * @dataProvider nonArrayProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testMonthLabelWithBadTypes($badVals)
    {
        $this->CalendarChart->monthLabel($badVals);
    }

    public function testMonthOutlineColor()
    {
        $this->CalendarChart->monthOutlineColor([]);

        $this->assertInstanceOf('\Khill\Lavacharts\Configs\Stroke', $this->CalendarChart->monthOutlineColor);
    }

    /**
     * @dataProvider nonArrayProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testMonthOutlineColorWithBadTypes($badVals)
    {
        $this->CalendarChart->monthOutlineColor($badVals);
    }

    public function testUnderMonthSpace()
    {
        $this->CalendarChart->underMonthSpace(5);

        $this->assertEquals(5, $this->CalendarChart->underMonthSpace);
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testUnderMonthSpaceWithBadType($badTypes)
    {
        $this->CalendarChart->underMonthSpace($badTypes);
    }

    public function testUnderYearSpace()
    {
        $this->CalendarChart->underYearSpace(5);

        $this->assertEquals(5, $this->CalendarChart->underYearSpace);
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testUnderYearSpaceWithBadType($badTypes)
    {
        $this->CalendarChart->underYearSpace($badTypes);
    }

    public function testUnusedMonthOutlineColor()
    {
        $this->CalendarChart->unusedMonthOutlineColor([]);

        $this->assertInstanceOf('\Khill\Lavacharts\Configs\Stroke', $this->CalendarChart->unusedMonthOutlineColor);
    }

    /**
     * @dataProvider nonArrayProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testUnusedMonthOutlineColorWithBadTypes($badVals)
    {
        $this->CalendarChart->unusedMonthOutlineColor($badVals);
    }


    public function testForceIFrame()
    {
        $this->CalendarChart->forceIFrame(true);

        $this->assertTrue($this->CalendarChart->forceIFrame);
    }

    /**
     * @dataProvider nonBoolProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testForceIFrameWithBadType($badTypes)
    {
        $this->CalendarChart->forceIFrame($badTypes);
    }

    public function testNoDataPattern()
    {
        $this->CalendarChart->noDataPattern([]);

        $this->assertInstanceOf('\Khill\Lavacharts\Configs\Color', $this->CalendarChart->noDataPattern);
    }

    /**
     * @dataProvider nonArrayProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testNoDataPatternWithBadTypes($badVals)
    {
        $this->CalendarChart->noDataPattern($badVals);
    }
}
