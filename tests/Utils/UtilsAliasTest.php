<?php namespace Khill\Lavacharts\Tests\Utilss;

use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Configs as c;

class UtilsAliasTest extends \PHPUnit_Framework_TestCase
{
    public function testAliasedIsDataTable()
    {
        $this->assertTrue(Utils::isDataTable(new c\DataTable));
        $this->assertFalse(Utils::isDataTable(new \stdClass));
    }

    public function testAliasedIsAnnotation()
    {
        $this->assertTrue(Utils::isAnnotation(new c\Annotation));
        $this->assertFalse(Utils::isAnnotation(new \stdClass));
    }

    public function testAliasedIsBackgroundColor()
    {
        $this->assertTrue(Utils::isBackgroundColor(new c\BackgroundColor));
        $this->assertFalse(Utils::isBackgroundColor(new \stdClass));
    }

    public function testAliasedIsBoxStyle()
    {
        $this->assertTrue(Utils::isBoxStyle(new c\BoxStyle));
        $this->assertFalse(Utils::isBoxStyle(new \stdClass));
    }

    public function testAliasedIsChartArea()
    {
        $this->assertTrue(Utils::isChartArea(new c\ChartArea));
        $this->assertFalse(Utils::isChartArea(new \stdClass));
    }

    public function testAliasedIsColorAxis()
    {
        $this->assertTrue(Utils::isColorAxis(new c\ColorAxis));
        $this->assertFalse(Utils::isColorAxis(new \stdClass));
    }

    public function testAliasedIsGradient()
    {
        $this->assertTrue(Utils::isGradient(new c\Gradient));
        $this->assertFalse(Utils::isGradient(new \stdClass));
    }

    public function testAliasedIsHorizontalAxis()
    {
        $this->assertTrue(Utils::isHorizontalAxis(new c\HorizontalAxis));
        $this->assertFalse(Utils::isHorizontalAxis(new \stdClass));
    }

    public function testAliasedIsLegend()
    {
        $this->assertTrue(Utils::isLegend(new c\Legend));
        $this->assertFalse(Utils::isLegend(new \stdClass));
    }

    public function testAliasedIsMagnifyingGlass()
    {
        $this->assertTrue(Utils::isMagnifyingGlass(new c\MagnifyingGlass));
        $this->assertFalse(Utils::isMagnifyingGlass(new \stdClass));
    }

    public function testAliasedIsTextStyle()
    {
        $this->assertTrue(Utils::isTextStyle(new c\TextStyle));
        $this->assertFalse(Utils::isTextStyle(new \stdClass));
    }

    public function testAliasedIsTooltip()
    {
        $this->assertTrue(Utils::isTooltip(new c\Tooltip));
        $this->assertFalse(Utils::isTooltip(new \stdClass));
    }

    public function testAliasedSizeAxis()
    {
        $this->assertTrue(Utils::isSizeAxis(new c\SizeAxis));
        $this->assertFalse(Utils::isSizeAxis(new \stdClass));
    }

    public function testAliasedSeries()
    {
        $this->assertTrue(Utils::isSeries(new c\Series));
        $this->assertFalse(Utils::isSeries(new \stdClass));
    }

    public function testAliasedSlice()
    {
        $this->assertTrue(Utils::isSlice(new c\Slice));
        $this->assertFalse(Utils::isSlice(new \stdClass));
    }

    public function testAliasedVerticalAxis()
    {
        $this->assertTrue(Utils::isVerticalAxis(new c\VerticalAxis));
        $this->assertFalse(Utils::isVerticalAxis(new \stdClass));
    }

    public function testBadAliasCall()
    {
        $this->assertFalse(Utils::canHasTacos(new \stdClass));
    }
}
