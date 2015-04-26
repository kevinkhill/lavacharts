<?php namespace Khill\Lavacharts\Tests;

use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Khill\Lavacharts\Lavacharts;
use \Mockery as m;

class LavachartsTest extends ProvidersTestCase
{
    public function setUp()
    {
        $this->lava = new Lavacharts;

        $this->mdt = m::mock('\Khill\Lavacharts\Configs\DataTable')
                      ->shouldReceive('toJson')
                      ->atMost(1)
                      ->shouldReceive('hasFormats')
                      ->atLeast(1)
                      ->getMock();
    }

    public function testIfInstanceOfVolcano()
    {
        $this->assertInstanceOf('\Khill\Lavacharts\Volcano', $this->lava->volcano);
    }

    public function testIfInstanceOfJavascriptFactory()
    {
        $this->assertInstanceOf('\Khill\Lavacharts\JavascriptFactory', $this->lava->jsFactory);
    }

    public function testCreateDataTableViaAlias()
    {
        $this->assertInstanceOf('\Khill\Lavacharts\Configs\DataTable', $this->lava->DataTable());
    }

    public function testCreateDataTableViaAliasWithTimezone()
    {
        $this->assertInstanceOf('\Khill\Lavacharts\Configs\DataTable', $this->lava->DataTable('America/Los_Angeles'));
    }

    public function testExistsWithExistingChartInVolcano()
    {
        $this->lava->LineChart('TestChart');

        $this->assertTrue($this->lava->exists('LineChart', 'TestChart'));
    }

    public function testExistsWithNonExistantChartTypeInVolcano()
    {
        $this->lava->LineChart('TestChart');

        $this->assertFalse($this->lava->exists('SheepChart', 'TestChart'));
    }

    public function testExistsWithNonExistantChartLabelInVolcano()
    {
        $this->lava->LineChart('WhaaaaatChart?');

        $this->assertFalse($this->lava->exists('LineChart', 'TestChart'));
    }

    /**
     * @dataProvider nonStringProvider
     */
    public function testExistsWithNonStringInputForType($badTypes)
    {
        $this->lava->LineChart('TestChart');

        $this->assertFalse($this->lava->exists($badTypes, 'TestChart'));
    }

    /**
     * @dataProvider nonStringProvider
     */
    public function testExistsWithNonStringInputForLabel($badTypes)
    {
        $this->lava->LineChart('TestChart');

        $this->assertFalse($this->lava->exists('LineChart', $badTypes));
    }

    /**
     * @dataProvider chartTypesProvider
     */
    public function testCreateChartsViaAlias($chartType)
    {
        $this->assertInstanceOf('\Khill\Lavacharts\Charts\\'.$chartType, $this->lava->$chartType('testchart'));
    }

    /**
     * @dataProvider configObjectProvider
     */
    public function testCreateConfigObjectsViaAlias($configType)
    {
        $this->assertInstanceOf('\Khill\Lavacharts\Configs\\'.$configType, $this->lava->$configType());
    }

    /**
     * @dataProvider eventObjectProvider
     */
    public function testCreateEventObjectsViaAliasWithCallback($eventType)
    {
        $this->assertInstanceOf('\Khill\Lavacharts\Events\\'.$eventType, $this->lava->$eventType('jsCallback'));
    }

    /**
     * @dataProvider eventObjectProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidEventCallback
     */
    public function testCreateEventObjectsViaAliasWithMissingCallback($eventType)
    {
        $this->lava->$eventType();
    }

    /**
     * @dataProvider eventObjectProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidEventCallback
     */
    public function testCreateEventObjectsViaAliasWithBadTypeCallback($eventType)
    {
        $this->lava->$eventType(2372);
    }

    /**
     * @dataProvider formatObjectProvider
     */
    public function testCreateFormatObjectsViaAlias($formatType)
    {
        $this->assertInstanceOf('\Khill\Lavacharts\Formats\\'.$formatType, $this->lava->$formatType());
    }

    public function testCreateConfigObjectViaAliasWithParam()
    {
        $params = array(
            'fontSize' => 4,
            'fontName' => 'Arial'
        );

        $this->assertInstanceOf('\Khill\Lavacharts\Configs\TextStyle', $this->lava->TextStyle($params));
    }

    /**
     * @dataProvider chartTypesProvider
     * @depends testCreateDataTableViaAlias
     */
    public function testRenderChartAliases($chartType)
    {
        $chart = $this->lava->$chartType('test');
        $chart->datatable($this->mdt);

        $render = 'render'.$chartType;

        $this->assertTrue(is_string($this->lava->$render('test', 'test-div')));
    }

    /**
     * @depends testCreateDataTableViaAlias
     */
    public function testDirectRenderChart()
    {
        $chart = $this->lava->LineChart('test');
        $chart->datatable($this->mdt);

        $this->assertTrue(is_string($this->lava->render('LineChart', 'test', 'test-div')));
    }

    /**
     * @depends testCreateDataTableViaAlias
     */
    public function testDirectRenderChartWithDivNoDimensions()
    {
        $chart = $this->lava->LineChart('test');
        $chart->datatable($this->mdt);

        $this->assertTrue(is_string($this->lava->render('LineChart', 'test', 'test-div', true)));
    }

    /**
     * @depends testCreateDataTableViaAlias
     */
    public function testDirectRenderChartWithDivAndDimensions()
    {
        $chart = $this->lava->LineChart('test');
        $chart->datatable($this->mdt);

        $dims = array(
            'height' => 200,
            'width' => 200
        );

        $this->assertTrue(is_string($this->lava->render('LineChart', 'test', 'test-div', $dims)));
    }

    /**
     * @depends testCreateDataTableViaAlias
     * @expectedException Khill\Lavacharts\Exceptions\InvalidDivDimensions
     */
    public function testDirectRenderChartWithDivAndBadDimensionKeys()
    {
        $chart = $this->lava->LineChart('test');
        $chart->datatable($this->mdt);

        $dims = array(
            'heiXght' => 200,
            'wZidth' => 200
        );

        $this->assertTrue(is_string($this->lava->render('LineChart', 'test', 'test-div', $dims)));
    }

    /**
     * @depends testCreateDataTableViaAlias
     * @expectedException Khill\Lavacharts\Exceptions\InvalidDivDimensions
     */
    public function testDirectRenderChartWithDivAndBadDimensionType()
    {
        $chart = $this->lava->LineChart('test');
        $chart->datatable($this->mdt);

        $this->assertTrue(is_string($this->lava->render('LineChart', 'test', 'test-div', 'TacosTacosTacos')));
    }

    /**
     * @depends testCreateDataTableViaAlias
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testDirectRenderChartWithDivAndDimensionsWithBadValues()
    {
        $chart = $this->lava->LineChart('test');
        $chart->datatable($this->mdt);

        $dims = array(
            'height' => 4.6,
            'width' => 'hotdogs'
        );

        $this->assertTrue(is_string($this->lava->render('LineChart', 'test', 'test-div', $dims)));
    }

    /**
     * @depends testCreateDataTableViaAlias
     * @depends testCreateConfigObjectViaAliasWithParam
     */
    public function testCreateFormatObjectViaAliasWithConsructorConfig()
    {
        $dt = $this->lava->DataTable();

        $df = $this->lava->DateFormat(array(
            'formatType' => 'medium'
        ));

        $dt->addDateColumn('dates', $df);

        $chart = $this->lava->LineChart('test');
        $chart->datatable($dt);

        $this->assertTrue(is_string($this->lava->render('LineChart', 'test', 'test-div')));
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidLavaObject
     */
    public function testInvalidLavaObject()
    {
        $this->lava->PizzaChart();
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidLavaObject
     */
    public function testRenderAliasWithInvalidLavaObject()
    {
        $this->lava->renderTacoChart();
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidChartLabel
     */
    public function testCreateChartWithMissingLabel()
    {
        $this->lava->LineChart();
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidChartLabel
     */
    public function testCreateChartWithInvalidLabel()
    {
        $this->lava->LineChart(5);
    }

    public function testCreatingChartAndRetrievingFromVolcano()
    {
        $this->lava->PieChart('volcanoTest');

        $this->assertInstanceOf('\Khill\Lavacharts\Charts\PieChart', $this->lava->PieChart('volcanoTest'));
    }

    public function testJsapiMethodWithCoreJsTracking()
    {
        $this->lava->jsapi();

        $this->assertTrue($this->lava->jsFactory->coreJsRendered());
    }

    public function chartTypesProvider()
    {
        return array(
            array('AreaChart'),
            array('BarChart'),
            array('CalendarChart'),
            array('ColumnChart'),
            array('ComboChart'),
            array('DonutChart'),
            array('GaugeChart'),
            array('GeoChart'),
            array('LineChart'),
            array('PieChart')
        );
    }

    public function configObjectProvider()
    {
        return array(
            array('Animation'),
            array('Annotation'),
            array('BackgroundColor'),
            array('BoxStyle'),
            array('ChartArea'),
            array('Color'),
            array('ColorAxis'),
            array('Gradient'),
            array('HorizontalAxis'),
            array('Legend'),
            array('MagnifyingGlass'),
            array('Series'),
            array('SizeAxis'),
            array('Slice'),
            array('Stroke'),
            array('TextStyle'),
            array('Tooltip'),
            array('VerticalAxis')
        );
    }

    public function eventObjectProvider()
    {
        return array(
            array('AnimationFinish'),
            array('Error'),
            array('MouseOut'),
            array('MouseOver'),
            array('Ready'),
            array('Select')
        );
    }

    public function formatObjectProvider()
    {
        return array(
            array('DateFormat'),
            array('NumberFormat')
        );
    }
}
