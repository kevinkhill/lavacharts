<?php namespace Khill\Lavacharts\Tests\Helpers;

use Khill\Lavacharts\Helpers\Helpers;
use Khill\Lavacharts\Configs as C;

class HelperAliasTest extends \PHPUnit_Framework_TestCase
{
    public function testAliasedIsAnnotation()
    {
        $this->assertTrue( Helpers::isAnnotation(new C\Annotation));
        $this->assertFalse( Helpers::isAnnotation(new \stdClass));
    }

    public function testAliasedIsBackgroundColor()
    {
        $this->assertTrue( Helpers::isBackgroundColor(new C\BackgroundColor));
        $this->assertFalse( Helpers::isBackgroundColor(new \stdClass));
    }

    public function testAliasedIsBoxStyle()
    {
        $this->assertTrue( Helpers::isBoxStyle(new C\BoxStyle));
        $this->assertFalse( Helpers::isBoxStyle(new \stdClass));
    }

    public function testAliasedIsChartArea()
    {
        $this->assertTrue( Helpers::isChartArea(new C\ChartArea));
        $this->assertFalse( Helpers::isChartArea(new \stdClass));
    }

    public function testAliasedIsColorAxis()
    {
        $this->assertTrue( Helpers::isColorAxis(new C\ColorAxis));
        $this->assertFalse( Helpers::isColorAxis(new \stdClass));
    }

    public function testAliasedIsGradient()
    {
        $this->assertTrue( Helpers::isGradient(new C\Gradient));
        $this->assertFalse( Helpers::isGradient(new \stdClass));
    }

    public function testAliasedIsHorizontalAxis()
    {
        $this->assertTrue( Helpers::isHorizontalAxis(new C\HorizontalAxis));
        $this->assertFalse( Helpers::isHorizontalAxis(new \stdClass));
    }

    public function testAliasedIsJsDate()
    {
        $this->assertTrue( Helpers::isJsDate(new C\JsDate));
        $this->assertFalse( Helpers::isJsDate(new \stdClass));
    }

    public function testAliasedIsLegend()
    {
        $this->assertTrue( Helpers::isLegend(new C\Legend));
        $this->assertFalse( Helpers::isLegend(new \stdClass));
    }

    public function testAliasedIsMagnifyingGlass()
    {
        $this->assertTrue( Helpers::isMagnifyingGlass(new C\MagnifyingGlass));
        $this->assertFalse( Helpers::isMagnifyingGlass(new \stdClass));
    }

    public function testAliasedIsTextStyle()
    {
        $this->assertTrue( Helpers::isTextStyle(new C\TextStyle));
        $this->assertFalse( Helpers::isTextStyle(new \stdClass));
    }

    public function testAliasedIsTooltip()
    {
        $this->assertTrue( Helpers::isTooltip(new C\Tooltip));
        $this->assertFalse( Helpers::isTooltip(new \stdClass));
    }

    public function testAliasedSizeAxis()
    {
        $this->assertTrue( Helpers::isSizeAxis(new C\SizeAxis));
        $this->assertFalse( Helpers::isSizeAxis(new \stdClass));
    }

    public function testAliasedSeries()
    {
        $this->assertTrue( Helpers::isSeries(new C\Series));
        $this->assertFalse( Helpers::isSeries(new \stdClass));
    }

    public function testAliasedSlice()
    {
        $this->assertTrue( Helpers::isSlice(new C\Slice));
        $this->assertFalse( Helpers::isSlice(new \stdClass));
    }

    public function testAliasedVerticalAxis()
    {
        $this->assertTrue( Helpers::isVerticalAxis(new C\VerticalAxis));
        $this->assertFalse( Helpers::isVerticalAxis(new \stdClass));
    }

    public function testBadAliasWithObject()
    {
        $this->assertFalse( Helpers::isTacos(new \stdClass));
    }

    public function testBadAliasWithNonObject()
    {
        $this->assertFalse( Helpers::isTacos( array() ));
    }

    public function testNonExistentAliasWithNonObject()
    {
        $this->assertFalse( Helpers::isTacos( array() ));
    }
}
