<?php namespace Khill\Lavacharts\Tests\Helpers;

use Khill\Lavacharts\Helpers\Helpers as H;

class HelperAliasTest extends HelperTestCase
{

    public function testAliasedIsBackgroundColor()
    {
        $this->assertTrue( H::is_backgroundColor( $this->backgroundColor ) );
        $this->assertFalse( H::is_backgroundColor( $this->obj ) );
    }

    public function testAliasedIsChartArea()
    {
        $this->assertTrue( H::is_chartArea( $this->chartArea ) );
        $this->assertFalse( H::is_chartArea( $this->obj ) );
    }

    public function testAliasedIsColorAxis()
    {
        $this->assertTrue( H::is_colorAxis( $this->colorAxis ) );
        $this->assertFalse( H::is_colorAxis( $this->obj ) );
    }

    public function testAliasedIsHAxis()
    {
        $this->assertTrue( H::is_hAxis( $this->hAxis ) );
        $this->assertFalse( H::is_hAxis( $this->obj ) );
    }

    public function testAliasedIsJsDate()
    {
        $this->assertTrue( H::is_jsDate( $this->jsDate ) );
        $this->assertFalse( H::is_jsDate( $this->obj ) );
    }

    public function testAliasedIsLegend()
    {
        $this->assertTrue( H::is_legend( $this->legend ) );
        $this->assertFalse( H::is_legend( $this->obj ) );
    }

    public function testAliasedIsMagnifyingGlass()
    {
        $this->assertTrue( H::is_magnifyingGlass( $this->magnifyingGlass ) );
        $this->assertFalse( H::is_magnifyingGlass( $this->obj ) );
    }

    public function testAliasedIsTextStyle()
    {
        $this->assertTrue( H::is_textStyle( $this->textStyle ));
        $this->assertFalse( H::is_textStyle( $this->obj ));
    }

    public function testAliasedIsTooltip()
    {
        $this->assertTrue( H::is_tooltip( $this->tooltip ));
        $this->assertFalse( H::is_tooltip( $this->obj ));
    }

    public function testAliasedSizeAxis()
    {
        $this->assertTrue( H::is_sizeAxis( $this->sizeAxis ) );
        $this->assertFalse( H::is_sizeAxis( $this->obj ) );
    }

    public function testAliasedSlice()
    {
        $this->assertTrue( H::is_slice( $this->slice ) );
        $this->assertFalse( H::is_slice( $this->obj ) );
    }

    public function testAliasedVAxis()
    {
        $this->assertTrue( H::is_vAxis( $this->vAxis ) );
        $this->assertFalse( H::is_vAxis( $this->obj ) );
    }

    public function testBadAliasWithObject()
    {
        $this->assertFalse( H::istacos( $this->obj ) );
    }

    public function testBadAliasWithNonObject()
    {
        $this->assertFalse( H::istacos( array() ) );
    }

    public function testNonExistentAliasWithNonObject()
    {
        $this->assertFalse( H::is_tacos( array() ) );
    }

}
