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
     * @dataProvider chartTypeProvider
     * @depends testCreateDataTable
     */
    public function testRenederChartAliases($chartType)
    {
        $lc = new Lavacharts;

        $chart = $lc->$chartType('test');
        $chart->dataTable($lc->DataTable());

        $render = 'render'.$chartType;

        $this->assertTrue(is_string($lc->$render('test', 'test-div')));
    }

    /**
     * @depends testCreateDataTable
     */
    public function testDirectRenederChart()
    {
        $lc = new Lavacharts;

        $chart = $lc->LineChart('test');
        $chart->dataTable($lc->DataTable());

        $this->assertTrue(is_string($lc->render('LineChart', 'test', 'test-div')));
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidLavaObject
     */
    public function testInvalidLavaObject()
    {
        $lc = new Lavacharts;
        $lc->PizzaChart();
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
