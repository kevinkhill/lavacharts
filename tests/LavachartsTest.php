<?php namespace Khill\Lavacharts\Tests;

use Khill\Lavacharts\Lavacharts;

class LavachartsTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->lc = new Lavacharts;
    }

    public function testIfInstanceOfJavascriptFactory()
    {
        $this->assertInstanceOf('Khill\Lavacharts\Lavacharts', $this->lc);
        $this->assertInstanceOf('Khill\Lavacharts\Volcano', $this->lc->volcano);
    }

    public function testCreateDataTableViaAlias()
    {
        $this->assertInstanceOf('Khill\Lavacharts\Configs\DataTable', $this->lc->DataTable());
    }

    /**
     * @dataProvider chartTypeProvider
     */
    public function testCreateChartsViaAlias($chartType)
    {
        $this->assertInstanceOf('Khill\Lavacharts\Charts\\'.$chartType, $this->lc->$chartType('testchart'));
    }

    /**
     * @dataProvider configObjectProvider
     */
    public function testCreateConfigObjectsViaAliasNoParams($configType)
    {
        $this->assertInstanceOf('Khill\Lavacharts\Configs\\'.$configType, $this->lc->$configType());
    }

    public function testCreateConfigObjectViaAliasWithParam()
    {
        $params = array(
            'fontSize' => 4,
            'fontColor' => 'green'
        );

        $this->assertInstanceOf('Khill\Lavacharts\Configs\TextStyle', $this->lc->TextStyle($params));
    }

    /**
     * @dataProvider chartTypeProvider
     * @depends testCreateDataTableViaAlias
     */
    public function testRenederChartAliases($chartType)
    {
        $chart = $this->lc->$chartType('test');
        $chart->dataTable($this->lc->DataTable());

        $render = 'render'.$chartType;

        $this->assertTrue(is_string($this->lc->$render('test', 'test-div')));
    }

    /**
     * @depends testCreateDataTableViaAlias
     */
    public function testDirectRenederChart()
    {
        $chart = $this->lc->LineChart('test');
        $chart->dataTable($this->lc->DataTable());

        $this->assertTrue(is_string($this->lc->render('LineChart', 'test', 'test-div')));
    }

    /**
     * @depends testCreateDataTableViaAlias
     */
    public function testDirectRenederChartWithDivNoDimensions()
    {
        $chart = $this->lc->LineChart('test');
        $chart->dataTable($this->lc->DataTable());

        $this->assertTrue(is_string($this->lc->render('LineChart', 'test', 'test-div', true)));
    }

    /**
     * @depends testCreateDataTableViaAlias
     */
    public function testDirectRenederChartWithDivAndDimensions()
    {
        $chart = $this->lc->LineChart('test');
        $chart->dataTable($this->lc->DataTable());

        $dims = array(
            'height' => 200,
            'width' => 200
        );

        $this->assertTrue(is_string($this->lc->render('LineChart', 'test', 'test-div', $dims)));
    }

    /**
     * @depends testCreateDataTableViaAlias
     * @expectedException Khill\Lavacharts\Exceptions\InvalidDivDimensions
     */
    public function testDirectRenederChartWithDivAndBadDimensionKeys()
    {
        $chart = $this->lc->LineChart('test');
        $chart->dataTable($this->lc->DataTable());

        $dims = array(
            'heiXght' => 200,
            'wZidth' => 200
        );

        $this->assertTrue(is_string($this->lc->render('LineChart', 'test', 'test-div', $dims)));
    }

    /**
     * @depends testCreateDataTableViaAlias
     * @expectedException Khill\Lavacharts\Exceptions\InvalidDivDimensions
     */
    public function testDirectRenederChartWithDivAndBadDimensionType()
    {
        $chart = $this->lc->LineChart('test');
        $chart->dataTable($this->lc->DataTable());

        $this->assertTrue(is_string($this->lc->render('LineChart', 'test', 'test-div', 'TacosTacosTacos')));
    }

    /**
     * @depends testCreateDataTableViaAlias
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testDirectRenederChartWithDivAndDimensionsWithBadValues()
    {
        $chart = $this->lc->LineChart('test');
        $chart->dataTable($this->lc->DataTable());

        $dims = array(
            'height' => 4.6,
            'width' => 'hotdogs'
        );

        $this->assertTrue(is_string($this->lc->render('LineChart', 'test', 'test-div', $dims)));
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidLavaObject
     */
    public function testInvalidLavaObject()
    {        $this->lc->PizzaChart();
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidLavaObject
     */
    public function testRenderAliasWithInvalidLavaObject()
    {        $this->lc->renderTacoChart();
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidChartLabel
     */
    public function testCreateChartWithMissingLabel()
    {        $this->lc->LineChart();
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidChartLabel
     */
    public function testCreateChartWithInvalidLabel()
    {        $this->lc->LineChart(5);
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
            //array('HorizontalAxis'),
            array('Gradient'),
            array('Legend'),
            array('MagnifyingGlass'),
            array('TextStyle'),
            array('Tooltip'),
            array('Series'),
            array('SizeAxis'),
            array('Slice'),
            //array('VerticalAxis')
        );
    }

}
