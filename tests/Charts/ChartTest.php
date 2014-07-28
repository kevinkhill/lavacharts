<?php namespace Khill\Lavacharts\Tests\Charts;

use Khill\Lavacharts\Tests\DataProviders;
use Mockery as m;

class ChartTest extends DataProviders
{
    public function setUp()
    {
        parent::setUp();

        $this->mlc = m::mock('Khill\Lavacharts\Charts\LineChart', array('TestChart'))->makePartial();
    }

    public function testLabelAssignedViaConstructor()
    {
    	$this->assertEquals('TestChart', $this->mlc->label);
    }

    public function testBackgroundColorWithValidValues()
    {
        $mbc = m::mock('Khill\Lavacharts\Configs\BackgroundColor');
        $mbc->shouldReceive('toArray')->once()->andReturn(array(
            'backgroundColor' => array()
        ));

        $this->mlc->backgroundColor($mbc);
        $this->assertTrue(is_array($this->mlc->options['backgroundColor']));
    }

    /**
     * @dataProvider nonConfigObjectProvider
     * @expectedException PHPUnit_Framework_Error
     */
    public function testBackgroundColorWithBadTypes($badTypes)
    {
        $this->mlc->backgroundColor($badTypes);
    }

    public function testChartAreaWithValidValues()
    {
        $mca = m::mock('Khill\Lavacharts\Configs\ChartArea');
        $mca->shouldReceive('toArray')->once()->andReturn(array(
            'chartArea' => array()
        ));

        $this->mlc->chartArea($mca);
        $this->assertTrue(is_array($this->mlc->options['chartArea']));
    }

    /**
     * @dataProvider nonConfigObjectProvider
     * @expectedException PHPUnit_Framework_Error
     */
    public function testChartAreaWithBadTypes($badTypes)
    {
        $this->mlc->chartArea($badTypes);
    }

    public function testColorsWithValidValue()
    {
        $colors = array('green', 'red');

        $this->mlc->colors($colors);
        $this->assertEquals($colors, $this->mlc->options['colors']);
    }

    /**
     * @dataProvider nonArrayProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testColorsWithBadTypes($badTypes)
    {
        $this->mlc->colors($badTypes);
    }

    public function testFontNameWithValidValue()
    {
        $this->mlc->fontName('Tahoma');
        $this->assertEquals('Tahoma', $this->mlc->options['fontName']);
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testFontNameWithBadTypes($badTypes)
    {
        $this->mlc->fontName($badTypes);
    }

    public function testFontSizeWithValidValue()
    {
        $this->mlc->fontSize(34);
        $this->assertEquals(34, $this->mlc->options['fontSize']);
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testFontSizeWithBadTypes($badTypes)
    {
        $this->mlc->fontSize($badTypes);
    }

    public function testHeightWithValidValue()
    {
        $this->mlc->height(500);
        $this->assertEquals(500, $this->mlc->options['height']);
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testHeightWithBadTypes($badTypes)
    {
        $this->mlc->height($badTypes);
    }

    public function testLegendWithValidValues()
    {
        $ml = m::mock('Khill\Lavacharts\Configs\Legend');
        $ml->shouldReceive('toArray')->once()->andReturn(array(
            'legend' => array()
        ));

        $this->mlc->legend($ml);
        $this->assertTrue(is_array($this->mlc->options['legend']));
    }

    /**
     * @dataProvider nonConfigObjectProvider
     * @expectedException PHPUnit_Framework_Error
     */
    public function testLegendWithBadTypes($badTypes)
    {
        $this->mlc->legend($badTypes);
    }

    public function testTitleWithValidValue()
    {
        $this->mlc->title('Fancy Chart');
        $this->assertEquals('Fancy Chart', $this->mlc->options['title']);
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testTitleWithBadTypes($badTypes)
    {
        $this->mlc->title($badTypes);
    }

    public function testTitlePositionWithValidValues()
    {
        $this->mlc->titlePosition('in');
        $this->assertEquals('in', $this->mlc->options['titlePosition']);

        $this->mlc->titlePosition('out');
        $this->assertEquals('out', $this->mlc->options['titlePosition']);

        $this->mlc->titlePosition('none');
        $this->assertEquals('none', $this->mlc->options['titlePosition']);
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testTitlePositionWithBadValue()
    {
        $this->mlc->titlePosition('underneath');
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testTitlePositionWithBadTypes($badTypes)
    {
        $this->mlc->titlePosition($badTypes);
    }

    public function testTitleTextStyleWithValidValues()
    {
        $mts = m::mock('Khill\Lavacharts\Configs\TextStyle');
        $mts->shouldReceive('toArray')->once()->andReturn(array(
            'titleTextStyle' => array()
        ));

        $this->mlc->titleTextStyle($mts);
        $this->assertTrue(is_array($this->mlc->options['titleTextStyle']));
    }

    /**
     * @dataProvider nonConfigObjectProvider
     * @expectedException PHPUnit_Framework_Error
     */
    public function testTitleTextStyleWithBadTypes($badTypes)
    {
        $this->mlc->titleTextStyle($badTypes);
    }

    public function testTooltipWithValidValues()
    {
        $mtt = m::mock('Khill\Lavacharts\Configs\Tooltip');
        $mtt->shouldReceive('toArray')->once()->andReturn(array(
            'tooltip' => array()
        ));

        $this->mlc->tooltip($mtt);
        $this->assertTrue(is_array($this->mlc->options['tooltip']));
    }

    /**
     * @dataProvider nonConfigObjectProvider
     * @expectedException PHPUnit_Framework_Error
     */
    public function testTooltipWithBadTypes($badTypes)
    {
        $this->mlc->tooltip($badTypes);
    }

    public function testWidthWithValidValue()
    {
        $this->mlc->width(800);
        $this->assertEquals(800, $this->mlc->options['width']);
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testWidthWithBadTypes($badTypes)
    {
        $this->mlc->width($badTypes);
    }

}
