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

    public function testCreateDataTableViaAlias()
    {
        $lc = new Lavacharts;

        $this->assertInstanceOf('Khill\Lavacharts\Configs\DataTable', $lc->DataTable());
    }

    /**
     * @dataProvider chartTypeProvider
     */
    public function testCreateChartsViaAlias($chartType)
    {
        $lc = new Lavacharts;

        $this->assertInstanceOf('Khill\Lavacharts\Charts\\'.$chartType, $lc->$chartType('testchart'));
    }

    /**
     * @dataProvider configObjectProvider
     */
    public function testCreateConfigObjectsViaAliasNoParams($configType)
    {
        $lc = new Lavacharts;

        $this->assertInstanceOf('Khill\Lavacharts\Configs\\'.$configType, $lc->$configType());
    }

    public function testCreateConfigObjectViaAliasWithParam()
    {
        $lc = new Lavacharts;

        $params = array(
            'fontSize' => 4,
            'fontColor' => 'green'
        );

        $this->assertInstanceOf('Khill\Lavacharts\Configs\TextStyle', $lc->TextStyle($params));
    }

    /**
     * @dataProvider chartTypeProvider
     * @depends testCreateDataTableViaAlias
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
     * @depends testCreateDataTableViaAlias
     */
    public function testDirectRenederChart()
    {
        $lc = new Lavacharts;

        $chart = $lc->LineChart('test');
        $chart->dataTable($lc->DataTable());

        $this->assertTrue(is_string($lc->render('LineChart', 'test', 'test-div')));
    }

    /**
     * @depends testCreateDataTableViaAlias
     */
    public function testDirectRenederChartWithDivNoDimensions()
    {
        $lc = new Lavacharts;

        $chart = $lc->LineChart('test');
        $chart->dataTable($lc->DataTable());

        $this->assertTrue(is_string($lc->render('LineChart', 'test', 'test-div', true)));
    }

    /**
     * @depends testCreateDataTableViaAlias
     */
    public function testDirectRenederChartWithDivAndDimensions()
    {
        $lc = new Lavacharts;

        $chart = $lc->LineChart('test');
        $chart->dataTable($lc->DataTable());

        $dims = array(
            'height' => 200,
            'width' => 200
        );

        $this->assertTrue(is_string($lc->render('LineChart', 'test', 'test-div', $dims)));
    }

    /**
     * @depends testCreateDataTableViaAlias
     * @expectedException Khill\Lavacharts\Exceptions\InvalidDivDimensions
     */
    public function testDirectRenederChartWithDivAndBadDimensionKeys()
    {
        $lc = new Lavacharts;

        $chart = $lc->LineChart('test');
        $chart->dataTable($lc->DataTable());

        $dims = array(
            'heiXght' => 200,
            'wZidth' => 200
        );

        $this->assertTrue(is_string($lc->render('LineChart', 'test', 'test-div', $dims)));
    }

    /**
     * @depends testCreateDataTableViaAlias
     * @expectedException Khill\Lavacharts\Exceptions\InvalidDivDimensions
     */
    public function testDirectRenederChartWithDivAndBadDimensionType()
    {
        $lc = new Lavacharts;

        $chart = $lc->LineChart('test');
        $chart->dataTable($lc->DataTable());

        $this->assertTrue(is_string($lc->render('LineChart', 'test', 'test-div', 'TacosTacosTacos')));
    }

    /**
     * @depends testCreateDataTableViaAlias
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testDirectRenederChartWithDivAndDimensionsWithBadValues()
    {
        $lc = new Lavacharts;

        $chart = $lc->LineChart('test');
        $chart->dataTable($lc->DataTable());

        $dims = array(
            'height' => 4.6,
            'width' => 'hotdogs'
        );

        $this->assertTrue(is_string($lc->render('LineChart', 'test', 'test-div', $dims)));
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidLavaObject
     */
    public function testInvalidLavaObject()
    {
        $lc = new Lavacharts;
        $lc->PizzaChart();
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidLavaObject
     */
    public function testRenderAliasWithInvalidLavaObject()
    {
        $lc = new Lavacharts;
        $lc->renderPizzaChart();
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidChartLabel
     */
    public function testCreateChartWithMissingLabel()
    {
        $lc = new Lavacharts;
        $lc->LineChart();
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidChartLabel
     */
    public function testCreateChartWithInvalidLabel()
    {
        $lc = new Lavacharts;
        $lc->LineChart(5);
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
