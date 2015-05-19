<?php

namespace Khill\Lavacharts\Tests\Charts;

use \Khill\Lavacharts\Charts\CalendarChart;

class CalendarChartTest extends ChartTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->CalendarChart = new CalendarChart('MyTestChart', $this->partialDataTable);
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
        $this->assertEquals('MyTestChart', $this->CalendarChart->label);
    }

    public function testCellColor()
    {
        $this->CalendarChart->cellColor($this->getMockStroke('cellColor'));

        $this->assertTrue(is_array($this->CalendarChart->getOption('cellColor')));
    }

    public function testCellSize()
    {
        $this->CalendarChart->cellSize(3);

        $this->assertEquals(3, $this->CalendarChart->getOption('cellSize'));
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
        $this->CalendarChart->dayOfWeekLabel($this->getMockTextStyle('dayOfWeekLabel'));

        $this->assertTrue(is_array($this->CalendarChart->getOption('dayOfWeekLabel')));
    }

    public function testDayOfWeekRightSpace()
    {
        $this->CalendarChart->dayOfWeekRightSpace(5);

        $this->assertEquals(5, $this->CalendarChart->getOption('dayOfWeekRightSpace'));
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

        $this->assertEquals('MAWEFWA', $this->CalendarChart->getOption('daysOfWeek'));
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
        $this->CalendarChart->focusedCellColor($this->getMockStroke('focusedCellColor'));

        $this->assertTrue(is_array($this->CalendarChart->getOption('focusedCellColor')));
    }

    public function testMonthLabel()
    {
        $this->CalendarChart->monthLabel($this->getMockTextStyle('monthLabel'));

        $this->assertTrue(is_array($this->CalendarChart->getOption('monthLabel')));
    }

    public function testMonthOutlineColor()
    {
        $this->CalendarChart->monthOutlineColor($this->getMockStroke('monthOutlineColor'));

        $this->assertTrue(is_array($this->CalendarChart->getOption('monthOutlineColor')));
    }

    public function testUnderMonthSpace()
    {
        $this->CalendarChart->underMonthSpace(5);

        $this->assertEquals(5, $this->CalendarChart->getOption('underMonthSpace'));
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

        $this->assertEquals(5, $this->CalendarChart->getOption('underYearSpace'));
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
        $this->CalendarChart->unusedMonthOutlineColor($this->getMockStroke('unusedMonthOutlineColor'));

        $this->assertTrue(is_array($this->CalendarChart->getOption('unusedMonthOutlineColor')));
    }

    public function testColorAxis()
    {
        $this->CalendarChart->colorAxis($this->getMockColorAxis());

        $this->assertTrue(is_array($this->CalendarChart->getOption('colorAxis')));
    }

    public function testForceIFrame()
    {
        $this->CalendarChart->forceIFrame(true);

        $this->assertTrue($this->CalendarChart->getOption('forceIFrame'));
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
        $this->CalendarChart->noDataPattern($this->getMockColor('noDataPattern'));

        $this->assertTrue(is_array($this->CalendarChart->getOption('noDataPattern')));
    }
}
