<?php namespace Khill\Lavacharts\Tests\Helpers;

use Khill\Lavacharts\Helpers\Helpers as h;
use Khill\Lavacharts\Configs as c;

class HelperAliasTest extends \PHPUnit_Framework_TestCase
{
    public function testAliasedIsDataTable()
    {
        $this->assertTrue(h::isDataTable(new c\DataTable));
        $this->assertFalse(h::isDataTable(new \stdClass));
    }

    public function testAliasedIsAnnotation()
    {
        $this->assertTrue(h::isAnnotation(new c\Annotation));
        $this->assertFalse(h::isAnnotation(new \stdClass));
    }

    public function testAliasedIsBackgroundColor()
    {
        $this->assertTrue(h::isBackgroundColor(new c\BackgroundColor));
        $this->assertFalse(h::isBackgroundColor(new \stdClass));
    }

    public function testAliasedIsBoxStyle()
    {
        $this->assertTrue(h::isBoxStyle(new c\BoxStyle));
        $this->assertFalse(h::isBoxStyle(new \stdClass));
    }

    public function testAliasedIsChartArea()
    {
        $this->assertTrue(h::isChartArea(new c\ChartArea));
        $this->assertFalse(h::isChartArea(new \stdClass));
    }

    public function testAliasedIsColorAxis()
    {
        $this->assertTrue(h::isColorAxis(new c\ColorAxis));
        $this->assertFalse(h::isColorAxis(new \stdClass));
    }

    public function testAliasedIsGradient()
    {
        $this->assertTrue(h::isGradient(new c\Gradient));
        $this->assertFalse(h::isGradient(new \stdClass));
    }

    public function testAliasedIsHorizontalAxis()
    {
        $this->assertTrue(h::isHorizontalAxis(new c\HorizontalAxis));
        $this->assertFalse(h::isHorizontalAxis(new \stdClass));
    }

    public function testAliasedIsLegend()
    {
        $this->assertTrue(h::isLegend(new c\Legend));
        $this->assertFalse(h::isLegend(new \stdClass));
    }

    public function testAliasedIsMagnifyingGlass()
    {
        $this->assertTrue(h::isMagnifyingGlass(new c\MagnifyingGlass));
        $this->assertFalse(h::isMagnifyingGlass(new \stdClass));
    }

    public function testAliasedIsTextStyle()
    {
        $this->assertTrue(h::isTextStyle(new c\TextStyle));
        $this->assertFalse(h::isTextStyle(new \stdClass));
    }

    public function testAliasedIsTooltip()
    {
        $this->assertTrue(h::isTooltip(new c\Tooltip));
        $this->assertFalse(h::isTooltip(new \stdClass));
    }

    public function testAliasedSizeAxis()
    {
        $this->assertTrue(h::isSizeAxis(new c\SizeAxis));
        $this->assertFalse(h::isSizeAxis(new \stdClass));
    }

    public function testAliasedSeries()
    {
        $this->assertTrue(h::isSeries(new c\Series));
        $this->assertFalse(h::isSeries(new \stdClass));
    }

    public function testAliasedSlice()
    {
        $this->assertTrue(h::isSlice(new c\Slice));
        $this->assertFalse(h::isSlice(new \stdClass));
    }

    public function testAliasedVerticalAxis()
    {
        $this->assertTrue(h::isVerticalAxis(new c\VerticalAxis));
        $this->assertFalse(h::isVerticalAxis(new \stdClass));
    }

    public function testBadAliasWithObject()
    {
        $this->assertFalse(h::isTacos(new \stdClass));
    }

    public function testBadAliasWithNonObject()
    {
        $this->assertFalse(h::isTacos( array() ));
    }

    public function testNonExistentAliasWithNonObject()
    {
        $this->assertFalse(h::isTacos( 'hi' ));
    }
}
