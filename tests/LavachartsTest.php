<?php

namespace Khill\Lavacharts\Tests;

use \Khill\Lavacharts\Lavacharts;
use \Mockery as m;

class LavachartsTest extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->lava = new Lavacharts;

        $this->partialDataTableWithReceives = m::mock('\Khill\Lavacharts\Configs\DataTable')
                                          ->shouldReceive('toJson')
                                          ->atMost(1)
                                          ->shouldReceive('hasFormats')
                                          ->atLeast(1)
                                          ->getMock();

        $this->mockLineChart = m::mock('\Khill\Lavacharts\Charts\LineChart');
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
        $this->lava->LineChart('TestChart', $this->partialDataTable);

        $this->assertTrue($this->lava->exists('LineChart', 'TestChart'));
    }

    public function testExistsWithNonExistantChartTypeInVolcano()
    {
        $this->lava->LineChart('TestChart', $this->partialDataTable);

        $this->assertFalse($this->lava->exists('SheepChart', 'TestChart'));
    }

    public function testExistsWithNonExistantChartLabelInVolcano()
    {
        $this->lava->LineChart('WhaaaaatChart?', $this->partialDataTable);

        $this->assertFalse($this->lava->exists('LineChart', 'TestChart'));
    }

    /**
     * @dataProvider nonStringProvider
     */
    public function testExistsWithNonStringInputForType($badTypes)
    {
        $this->lava->LineChart('TestChart', $this->partialDataTable);

        $this->assertFalse($this->lava->exists($badTypes, 'TestChart'));
    }

    /**
     * @dataProvider nonStringProvider
     */
    public function testExistsWithNonStringInputForLabel($badTypes)
    {
        $this->lava->LineChart('TestChart', $this->partialDataTable);

        $this->assertFalse($this->lava->exists('LineChart', $badTypes));
    }

    /**
     * @dataProvider chartTypesProvider
     */
    public function testCreateChartsViaAlias($chartType)
    {
        $this->assertInstanceOf('\Khill\Lavacharts\Charts\\'.$chartType, $this->lava->$chartType('testchart', $this->partialDataTable));
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
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidEventCallback
     */
    public function testCreateEventObjectsViaAliasWithMissingCallback($eventType)
    {
        $this->lava->$eventType();
    }

    /**
     * @dataProvider eventObjectProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidEventCallback
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
        $params = [
            'fontSize' => 4,
            'fontName' => 'Arial'
        ];

        $this->assertInstanceOf('\Khill\Lavacharts\Configs\TextStyle', $this->lava->TextStyle($params));
    }

    /**
     * @dataProvider chartTypesProvider
     * @depends testCreateDataTableViaAlias
     */
    public function testRenderChartAliases($chartType)
    {
        $chart = $this->lava->$chartType('test', $this->partialDataTable);

        $renderAlias = 'render'.$chartType;

        $this->assertTrue(is_string($this->lava->$renderAlias('test', 'test-div')));
    }

    /**
     * @depends testCreateDataTableViaAlias
     */
    public function testDirectRenderChart()
    {
        $chart = $this->lava->LineChart('test', $this->partialDataTable);

        $this->assertTrue(is_string($this->lava->render('LineChart', 'test', 'test-div')));
    }

    /**
     * @depends testCreateDataTableViaAlias
     */
    public function testDirectRenderChartWithDivNoDimensions()
    {
        $chart = $this->lava->LineChart('test', $this->partialDataTable);

        $this->assertTrue(is_string($this->lava->render('LineChart', 'test', 'test-div', true)));
    }

    /**
     * @depends testCreateDataTableViaAlias
     */
    public function testDirectRenderChartWithDivAndDimensions()
    {
        $chart = $this->lava->LineChart('test', $this->partialDataTable);

        $dims = [
            'height' => 200,
            'width' => 200
        ];

        $this->assertTrue(is_string($this->lava->render('LineChart', 'test', 'test-div', $dims)));
    }

    /**
     * @depends testCreateDataTableViaAlias
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidDivDimensions
     */
    public function testDirectRenderChartWithDivAndBadDimensionKeys()
    {
        $chart = $this->lava->LineChart('test', $this->partialDataTable);

        $dims = [
            'heiXght' => 200,
            'wZidth' => 200
        ];

        $this->assertTrue(is_string($this->lava->render('LineChart', 'test', 'test-div', $dims)));
    }

    /**
     * @depends testCreateDataTableViaAlias
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidDivDimensions
     */
    public function testDirectRenderChartWithDivAndBadDimensionType()
    {
        $chart = $this->lava->LineChart('test', $this->partialDataTable);

        $this->assertTrue(is_string($this->lava->render('LineChart', 'test', 'test-div', 'TacosTacosTacos')));
    }

    /**
     * @depends testCreateDataTableViaAlias
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testDirectRenderChartWithDivAndDimensionsWithBadValues()
    {
        $chart = $this->lava->LineChart('test', $this->partialDataTable);

        $dims = [
            'height' => 4.6,
            'width' => 'hotdogs'
        ];

        $this->assertTrue(is_string($this->lava->render('LineChart', 'test', 'test-div', $dims)));
    }

    /**
     * @depends testCreateDataTableViaAlias
     * @depends testCreateConfigObjectViaAliasWithParam
     */
    public function testCreateFormatObjectViaAliasWithConsructorConfig()
    {
        $dt = $this->lava->DataTable();

        $df = $this->lava->DateFormat([
            'formatType' => 'medium'
        ]);

        $dt->addDateColumn('dates', $df);

        $chart = $this->lava->LineChart('test', $dt);

        $this->assertTrue(is_string($this->lava->render('LineChart', 'test', 'test-div')));
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidLavaObject
     */
    public function testRenderAliasWithInvalidLavaObject()
    {
        $this->lava->renderTacoChart();
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidLabel
     */
    public function testCreateChartWithMissingLabel()
    {
        $this->lava->LineChart();
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidDataTable
     */
    public function testCreateChartWithMissingDataTable()
    {
        $this->lava->LineChart('Cool Chart');
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidLabel
     */
    public function testCreateChartWithInvalidLabel()
    {
        $this->lava->LineChart(5, $this->partialDataTable);
    }

    public function testStoreChartIntoVolcano()
    {
        $mockPieChart = m::mock('\Khill\Lavacharts\Charts\PieChart', [
            'volcanoTest',
            $this->partialDataTable
        ]);

        $this->assertTrue($this->lava->store($mockPieChart));
        $this->assertInstanceOf('\Khill\Lavacharts\Charts\PieChart', $this->lava->fetch('PieChart', 'volcanoTest'));
    }

    public function testJsapiMethodWithCoreJsTracking()
    {
        $this->lava->jsapi();

        $this->assertTrue($this->lava->jsFactory->coreJsRendered());
    }

    public function chartTypesProvider()
    {
        return [
            ['AreaChart'],
            ['BarChart'],
            ['CalendarChart'],
            ['ColumnChart'],
            ['ComboChart'],
            ['DonutChart'],
            ['GaugeChart'],
            ['GeoChart'],
            ['LineChart'],
            ['PieChart'],
            ['ScatterChart']
        ];
    }

    public function configObjectProvider()
    {
        return [
            ['Animation'],
            ['Annotation'],
            ['BackgroundColor'],
            ['BoxStyle'],
            ['ChartArea'],
            ['Color'],
            ['ColorAxis'],
            ['Crosshair'],
            ['Gradient'],
            ['HorizontalAxis'],
            ['Legend'],
            ['MagnifyingGlass'],
            ['Series'],
            ['SizeAxis'],
            ['Slice'],
            ['Stroke'],
            ['TextStyle'],
            ['Tooltip'],
            ['VerticalAxis']
        ];
    }

    public function eventObjectProvider()
    {
        return [
            ['AnimationFinish'],
            ['Error'],
            ['MouseOut'],
            ['MouseOver'],
            ['Ready'],
            ['Select']
        ];
    }

    public function formatObjectProvider()
    {
        return [
            ['DateFormat'],
            ['NumberFormat']
        ];
    }
}
