<?php namespace Khill\Lavacharts\Tests;

use Khill\Lavacharts\Lavacharts;
use Mockery as m;

class LavachartsTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->lc = new Lavacharts;

        $this->mdt = m::mock('Khill\Lavacharts\Charts\DataTable')
                      ->shouldReceive('toJson')
                      ->atMost(1)
                      ->getMock();

    }

    public function testIfInstanceOfVolcano()
    {
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
    public function testRenderChartAliases($chartType)
    {
        $chart = $this->lc->$chartType('test');
        $chart->datatable($this->mdt);

        $render = 'render'.$chartType;

        $this->assertTrue(is_string($this->lc->$render('test', 'test-div')));
    }

    /**
     * @depends testCreateDataTableViaAlias
     */
    public function testDirectRenderChart()
    {
        $chart = $this->lc->LineChart('test');
        $chart->datatable($this->mdt);

        $this->assertTrue(is_string($this->lc->render('LineChart', 'test', 'test-div')));
    }

    /**
     * @depends testCreateDataTableViaAlias
     */
    public function testDirectRenderChartWithDivNoDimensions()
    {
        $chart = $this->lc->LineChart('test');
        $chart->datatable($this->mdt);

        $this->assertTrue(is_string($this->lc->render('LineChart', 'test', 'test-div', true)));
    }

    /**
     * @depends testCreateDataTableViaAlias
     */
    public function testDirectRenderChartWithDivAndDimensions()
    {
        $chart = $this->lc->LineChart('test');
        $chart->datatable($this->mdt);

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
    public function testDirectRenderChartWithDivAndBadDimensionKeys()
    {
        $chart = $this->lc->LineChart('test');
        $chart->datatable($this->mdt);

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
    public function testDirectRenderChartWithDivAndBadDimensionType()
    {
        $chart = $this->lc->LineChart('test');
        $chart->datatable($this->mdt);

        $this->assertTrue(is_string($this->lc->render('LineChart', 'test', 'test-div', 'TacosTacosTacos')));
    }

    /**
     * @depends testCreateDataTableViaAlias
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testDirectRenderChartWithDivAndDimensionsWithBadValues()
    {
        $chart = $this->lc->LineChart('test');
        $chart->datatable($this->mdt);

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
            array('AreaChart'),
            array('ColumnChart'),
            array('ComboChart'),
            array('DonutChart'),
            array('GeoChart'),
            array('LineChart'),
            array('PieChart')
        );
    }

    public function configObjectProvider()
    {
        return array(
            array('Annotation'),
            //array('Axis'),
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
