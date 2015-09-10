<?php

namespace Khill\Lavacharts\Tests;

use \Khill\Lavacharts\Lavacharts;

class LavachartsTest extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->lava = new Lavacharts;

        $this->mockLabel = \Mockery::mock('\\Khill\\Lavacharts\\Values\\Label', ['MockLabel'])->makePartial();

        $this->partialDataTableWithReceives = \Mockery::mock('\\Khill\\Lavacharts\\DataTables\\DataTable')
                                          ->shouldReceive('toJson')
                                          ->atMost(1)
                                          ->shouldReceive('hasFormats')
                                          ->atLeast(1)
                                          ->getMock();

        $this->mockLineChart = \Mockery::mock('\\Khill\\Lavacharts\\Charts\\LineChart');
    }

    public function testCreateDataTableViaAlias()
    {
        $this->assertInstanceOf('\\Khill\\Lavacharts\\DataTables\\DataTable', $this->lava->DataTable());
    }

    public function testCreateDataTableViaAliasWithTimezone()
    {
        $this->assertInstanceOf('\\Khill\\Lavacharts\\DataTables\\DataTable', $this->lava->DataTable('America/Los_Angeles'));
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
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidLabel
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
        $this->assertInstanceOf('\\Khill\\Lavacharts\\Charts\\'.$chartType, $this->lava->$chartType('testchart', $this->partialDataTable));
    }

    /**
     * @dataProvider formatObjectProvider
     */
    public function testCreateFormatObjectsViaAlias($formatType)
    {
        $this->assertInstanceOf('\\Khill\\Lavacharts\\DataTables\\Formats\\'.$formatType, $this->lava->$formatType());
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
        $this->lava->LineChart('test', $this->partialDataTable);

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
        $this->lava->LineChart('my-chart', $this->partialDataTable);

        $dims = [
            'height' => 4.6,
            'width' => 'hotdogs'
        ];

        $this->assertTrue(is_string($this->lava->render('LineChart', 'my-chart', 'test-div', $dims)));
    }

    /**
     * @depends testCreateDataTableViaAlias
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
        $mockPieChart = \Mockery::mock('\\Khill\\Lavacharts\\Charts\PieChart', [
            $this->mockLabel,
            $this->partialDataTable
        ])->shouldReceive('getLabel')->andReturn('MockLabel')->getMock();

        $this->assertTrue($this->lava->store($mockPieChart));
        $this->assertInstanceOf('\\Khill\\Lavacharts\\Charts\PieChart', $this->lava->fetch('PieChart', 'MockLabel'));
    }

    public function testJsapiMethodWithCoreJsTracking()
    {
        $this->lava->jsapi();

        $this->assertTrue($this->getPrivateProperty($this->lava, 'jsFactory')->coreJsRendered());
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
            ['ScatterChart'],
            ['TableChart']
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

    public function formatObjectProvider()
    {
        return [
            ['ArrowFormat'],
            ['BarFormat'],
            ['DateFormat'],
            ['NumberFormat']
        ];
    }
}
