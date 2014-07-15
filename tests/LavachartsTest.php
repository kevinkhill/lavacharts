<?php namespace Khill\Lavacharts\Tests;

use Khill\Lavacharts\Lavacharts;

class LavachartsTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testIfInstanceOfJavascriptFactory()
    {
        $lc = new Lavacharts;

        $this->assertInstanceOf('Khill\Lavacharts\Lavacharts', $lc);
        $this->assertInstanceOf('Khill\Lavacharts\Volcano', $lc->volcano);
    }

    public function testCreateDataTable()
    {
        $lc = new Lavacharts;

        $this->assertInstanceOf('Khill\Lavacharts\Configs\DataTable', $lc->DataTable());
    }

    /**
     * @dataProvider chartTypeProvider
     */
    public function testCreateCharts($chartType)
    {
        $lc = new Lavacharts;

        $this->assertInstanceOf('Khill\Lavacharts\Charts\\'.$chartType, $lc->$chartType('testchart'));
    }

    /**
     * @dataProvider configObjectProvider
     */
    public function testCreateConfigObjects($configType)
    {
        $lc = new Lavacharts;

        $this->assertInstanceOf('Khill\Lavacharts\Configs\\'.$configType, $lc->$configType());
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidLavaObject
     */
    public function testLavachartsInvalidLavaObject()
    {
        $lc = new Lavacharts;

        $lc->LaserChart();
    }

    public function chartTypeProvider()
    {
        return array(
            array('LineChart'),
            array('AreaChart'),
            array('PieChart'),
            array('DonutChart'),
            array('ColumnChart'),
            array('GeoChart'),
            array('ComboChart')
        );
    }

    public function configObjectProvider()
    {
        return array(
            array('Annotation'),
            array('Axis'),
            array('BoxStyle'),
            array('BackgroundColor'),
            array('ChartArea'),
            array('ColorAxis'),
            array('HorizontalAxis'),
            array('JsDate'),
            array('Gradient'),
            array('Legend'),
            array('MagnifyingGlass'),
            array('TextStyle'),
            array('Tooltip'),
            array('Series'),
            array('SizeAxis'),
            array('Slice'),
            array('VerticalAxis')
        );
    }

}
