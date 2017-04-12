<?php

namespace Khill\Lavacharts\Tests;

use Khill\Lavacharts\Charts\ChartFactory;
use Khill\Lavacharts\Lavacharts;

/**
 * @property \Khill\Lavacharts\Lavacharts lava
 */
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
        $this->assertInstanceOf(DATATABLE_NS.'DataTable', $this->lava->DataTable());
    }

    public function testCreateDataTableViaAliasWithTimezone()
    {
        $this->assertInstanceOf(DATATABLE_NS.'DataTable', $this->lava->DataTable('America/Los_Angeles'));
    }

    public function testExistsWithExistingChartInVolcano()
    {
        $this->lava->LineChart('TestChart', $this->partialDataTable);

        $this->assertTrue($this->lava->exists('LineChart', 'TestChart'));
    }

    public function testExistsWithNonExistentChartTypeInVolcano()
    {
        $this->lava->LineChart('TestChart', $this->partialDataTable);

        $this->assertFalse($this->lava->exists('SheepChart', 'TestChart'));
    }

    public function testExistsWithNonExistentChartLabelInVolcano()
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
     * @dataProvider chartTypeProvider
     */
    public function testCreatingChartsViaMagicMethodOfLavaObject($chartType)
    {
        $chart = $this->lava->$chartType(
            'My Fancy '.$chartType,
            $this->getMockDataTable()
        );

        $this->assertEquals('My Fancy '.$chartType, $chart->getLabelStr());
        $this->assertEquals($chartType, $chart->getType());
        $this->assertInstanceOf(DATATABLE_NS.'DataTable', $chart->getDataTable());
    }

    /**
     * @depends testCreateDataTableViaAlias
     */
    public function testDirectRenderChart()
    {
        $this->lava->LineChart('test', $this->partialDataTable);

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
        $this->lava->LineChart('test', $this->partialDataTable);

        $dims = [
            'height' => 200,
            'width' => 200
        ];

        $this->assertTrue(is_string($this->lava->render('LineChart', 'test', 'test-div', $dims)));
    }

    /**
     * @depends testCreateDataTableViaAlias
     */
    public function testDirectRenderChartWithDivAndBadDimensionKeys()
    {
        $this->lava->LineChart('test', $this->partialDataTable);

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
        $this->lava->LineChart('test', $this->partialDataTable);

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
    public function testCreateFormatObjectViaAliasWithConstructorConfig()
    {
        $dt = $this->lava->DataTable();

        $df = $this->lava->DateFormat([
            'formatType' => 'medium'
        ]);

        $dt->addDateColumn('dates', $df);

        $this->lava->LineChart('test', $dt);

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
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidLabel
     */
    public function testCreateChartWithInvalidLabel()
    {
        $this->lava->LineChart(5, $this->partialDataTable);
    }

    /**
     * @depends testCreatingChartsViaMagicMethodOfLavaObject
     */
    public function testStoreChartIntoVolcano()
    {
        $mockPieChart = \Mockery::mock('\Khill\Lavacharts\Charts\PieChart', [
            $this->mockLabel,
            $this->getMockDataTable()
        ])->shouldReceive('getType')
          ->andReturn('PieChart')
          ->shouldReceive('getLabel')
          ->andReturn('MockLabel')
          ->getMock();

        $this->lava->store($mockPieChart);

        $volcano = $this->inspect($this->lava, 'volcano');
        $charts = $this->inspect($volcano, 'charts');

        $this->assertArrayHasKey('PieChart', $charts);
        $this->assertInstanceOf('\Khill\Lavacharts\Charts\PieChart', $charts['PieChart']['MockLabel']);
    }

    public function testJsapiMethodWithCoreJsTracking()
    {
        $this->lava->jsapi();

        $this->assertTrue(
            $this->inspect($this->lava, 'scriptManager')->lavaJsRendered()
        );
    }

    public function testLavaJsMethodWithCoreJsTracking()
    {
        $this->lava->lavajs();

        $this->assertTrue(
            $this->inspect($this->lava, 'scriptManager')->lavaJsRendered()
        );
    }

    public function formatTypeProvider()
    {
        return [
            ['ArrowFormat'],
            ['BarFormat'],
            ['DateFormat'],
            ['NumberFormat']
        ];
    }
}
