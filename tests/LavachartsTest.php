<?php

namespace Khill\Lavacharts\Tests;

use Khill\Lavacharts\Charts\ChartFactory;
use Khill\Lavacharts\Charts\LineChart;
use Khill\Lavacharts\Charts\PieChart;
use Khill\Lavacharts\DataTables\DataTable;
use Khill\Lavacharts\Lavacharts;
use Khill\Lavacharts\Tests\Traits\DataProviders;
use Mockery;

class LavachartsTest extends ProvidersTestCase
{
    /**
     * @var  Lavacharts
     */
    private $lava;

    public function setUp()
    {
        parent::setUp();

        $this->lava = new Lavacharts;
    }

    public function testCreatingDataTableViaMagicMethod()
    {
        $this->assertInstanceOf(DataTable::class, $this->lava->DataTable());
    }

    public function testCreatingDataTableViaMagicMethodWithTimezone()
    {
        $this->assertInstanceOf(DataTable::class, $this->lava->DataTable('America/Los_Angeles'));
    }

    /**
     * @dataProvider chartTypeProvider
     * @param string $chartType
     */
    public function testCreatingChartsViaMagicMethod($chartType)
    {
        $chart = $this->lava->$chartType('My'.$chartType);

        $this->assertInstanceOf('\\Khill\\Lavacharts\\Charts\\'.$chartType, $chart);
    }

    /**
     * @dataProvider chartTypeProvider
     * @param string $chartType
     */
    public function testCreatingChartsViaMagicMethodAreStoredInVolcano($chartType)
    {
        $this->lava->$chartType('My'.$chartType);

        $volcano = $this->lava->getVolcano();

        $this->assertInstanceOf('\\Khill\\Lavacharts\\Charts\\'.$chartType, $volcano->get('My'.$chartType));
    }

    /**
     * @covers Lavacharts::exists()
     * @dataProvider chartTypeProvider
     * @param string $chartType
     */
    public function testExistsWithChartsInVolcano($chartType)
    {
        $this->lava->$chartType('My'.$chartType);

        $this->assertTrue($this->lava->exists('My'.$chartType));
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
        $this->assertInstanceOf(DataTable::class, $chart->getDataTable());
    }

    /**
     * @depends testCreateDataTableViaMagicMethod
     */
    public function testRenderChart()
    {
        $this->lava->LineChart('test', $this->partialDataTable);

        $this->assertTrue(is_string($this->lava->render('LineChart', 'test', 'test-div')));
    }

    /**
     * depends testCreateDataTableViaMagicMethod
     */
    public function testRenderChartWithElementIdAndDivWithNoDimensions()
    {
        $this->lava->LineChart('test', $this->partialDataTable);

        $output = $this->lava->render('LineChart', 'test', 'test-div', true);

        $this->assertStringHasString($output, '<div id="test-div"></div>');
    }

    /**
     * depends testCreateDataTableViaMagicMethod
     */
    public function testRenderChartWithNoElementIdAndDivNoDimensions()
    {
        $this->lava->LineChart('test', $this->partialDataTable, [
            'elementId' => 'test-div'
        ]);

        $output = $this->lava->render('LineChart', 'test', true);

        $this->assertStringHasString($output, '<div id="test-div"></div>');
    }

    /**
     * @depends testCreateDataTableViaMagicMethod
     */
    public function testRenderChartWithDivAndDimensions()
    {
        $this->lava->LineChart('test', $this->partialDataTable);

        $dims = [
            'height' => 200,
            'width' => 200
        ];

        $this->assertTrue(is_string($this->lava->render('LineChart', 'test', 'test-div', $dims)));
    }

    /**
     * @depends testCreateDataTableViaMagicMethod
     */
    public function testRenderChartWithDivAndBadDimensionKeys()
    {
        $this->lava->LineChart('test', $this->partialDataTable);

        $dims = [
            'heiXght' => 200,
            'wZidth' => 200
        ];

        $this->assertTrue(is_string($this->lava->render('LineChart', 'test', 'test-div', $dims)));
    }

    /**
     * @depends testCreateDataTableViaMagicMethod
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidDivDimensions
     */
    public function testRenderChartWithDivAndBadDimensionType()
    {
        $this->lava->LineChart('test', $this->partialDataTable);

        $this->assertTrue(is_string($this->lava->render('LineChart', 'test', 'test-div', 'TacosTacosTacos')));
    }

    /**
     * @depends testCreateDataTableViaMagicMethod
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testRenderChartWithDivAndDimensionsWithBadValues()
    {
        $this->lava->LineChart('my-chart', $this->partialDataTable);

        $dims = [
            'height' => 4.6,
            'width' => 'hotdogs'
        ];

        $this->assertTrue(is_string($this->lava->render('LineChart', 'my-chart', 'test-div', $dims)));
    }

    /**
     * @depends testCreateDataTableViaMagicMethod
     */
    public function testCreateFormatObjectViaMagicMethodWithConstructorConfig()
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
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidRenderable
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
        $mockPieChart = Mockery::mock(PieChart::class, [
            $this->mockLabel,
            Mockery::mock(DataTable::class)
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
