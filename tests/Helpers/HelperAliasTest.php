<?php namespace Khill\Lavacharts\Tests\Helpers;

use Khill\Lavacharts\Helpers\Helpers as H;
use Khill\Lavacharts\Configs as C;

class HelperAliasTest extends \PHPUnit_Framework_TestCase
{
    public function testAliasedIsAnnotation()
    {
        $this->assertTrue(H::isAnnotation(new C\Annotation));
        $this->assertFalse(H::isAnnotation(new \stdClass));
    }

    public function testAliasedIsBackgroundColor()
    {
        $this->assertTrue(H::isBackgroundColor(new C\BackgroundColor));
        $this->assertFalse(H::isBackgroundColor(new \stdClass));
    }

    public function testAliasedIsBoxStyle()
    {
        $this->assertTrue(H::isBoxStyle(new C\BoxStyle));
        $this->assertFalse(H::isBoxStyle(new \stdClass));
    }

    public function testAliasedIsChartArea()
    {
        $this->assertTrue(H::isChartArea(new C\ChartArea));
        $this->assertFalse(H::isChartArea(new \stdClass));
    }

    public function testAliasedIsColorAxis()
    {
        $this->assertTrue(H::isColorAxis(new C\ColorAxis));
        $this->assertFalse(H::isColorAxis(new \stdClass));
    }

    public function testAliasedIsGradient()
    {
        $this->assertTrue(H::isGradient(new C\Gradient));
        $this->assertFalse(H::isGradient(new \stdClass));
    }

    public function testAliasedIsHorizontalAxis()
    {
        $this->assertTrue(H::isHorizontalAxis(new C\HorizontalAxis));
        $this->assertFalse(H::isHorizontalAxis(new \stdClass));
    }

    public function testAliasedIsLegend()
    {
        $this->assertTrue(H::isLegend(new C\Legend));
        $this->assertFalse(H::isLegend(new \stdClass));
    }

    public function testAliasedIsMagnifyingGlass()
    {
        $this->assertTrue(H::isMagnifyingGlass(new C\MagnifyingGlass));
        $this->assertFalse(H::isMagnifyingGlass(new \stdClass));
    }

    public function testAliasedIsTextStyle()
    {
        $this->assertTrue(H::isTextStyle(new C\TextStyle));
        $this->assertFalse(H::isTextStyle(new \stdClass));
    }

    public function testAliasedIsTooltip()
    {
        $this->assertTrue(H::isTooltip(new C\Tooltip));
        $this->assertFalse(H::isTooltip(new \stdClass));
    }

    public function testAliasedSizeAxis()
    {
        $this->assertTrue(H::isSizeAxis(new C\SizeAxis));
        $this->assertFalse(H::isSizeAxis(new \stdClass));
    }

    public function testAliasedSeries()
    {
        $this->assertTrue(H::isSeries(new C\Series));
        $this->assertFalse(H::isSeries(new \stdClass));
    }

    public function testAliasedSlice()
    {
        $this->assertTrue(H::isSlice(new C\Slice));
        $this->assertFalse(H::isSlice(new \stdClass));
    }

    public function testAliasedVerticalAxis()
    {
        $this->assertTrue(H::isVerticalAxis(new C\VerticalAxis));
        $this->assertFalse(H::isVerticalAxis(new \stdClass));
    }

    public function testBadAliasWithObject()
    {
        $this->assertFalse(H::isTacos(new \stdClass));
    }

    public function testBadAliasWithNonObject()
    {
        $this->assertFalse(H::isTacos( array() ));
    }

    public function testNonExistentAliasWithNonObject()
    {
        $this->assertFalse(H::isTacos( array() ));
    }
}
