<?php

namespace Khill\Lavacharts\Tests;

use Khill\Lavacharts\Charts\Chart;
use Khill\Lavacharts\Dashboards\Dashboard;
use Khill\Lavacharts\Dashboards\Filter;
use Khill\Lavacharts\Dashboards\Wrappers\ChartWrapper;
use Khill\Lavacharts\Dashboards\Wrappers\ControlWrapper;
use Khill\Lavacharts\DataTables\Columns\Format;
use Khill\Lavacharts\DataTables\DataFactory;
use Khill\Lavacharts\DataTables\DataTable;
use Khill\Lavacharts\Javascript\ScriptManager;
use Khill\Lavacharts\Lavacharts;
use Khill\Lavacharts\Volcano;
use Mockery;

class LavachartsTest extends ProvidersTestCase
{
    /**
     * @var Lavacharts
     */
    private $lava;

    public function setUp()
    {
        parent::setUp();

        $this->lava = new Lavacharts;
    }

    public function testGetDefaultOptions()
    {
        $options = Lavacharts::getDefaultOptions();

        $this->assertTrue(is_array($options));

        $this->assertArrayHasKey('auto_run', $options);
        $this->assertArrayHasKey('locale', $options);
        $this->assertArrayHasKey('timezone', $options);
        $this->assertArrayHasKey('datetime_format', $options);
        $this->assertArrayHasKey('maps_api_key', $options);
        $this->assertArrayHasKey('responsive', $options);
        $this->assertArrayHasKey('debounce_timeout', $options);

        $this->assertEquals(true, $options['auto_run']);
        $this->assertEquals('en', $options['locale']);
        $this->assertEquals('America/Los_Angeles', $options['timezone']);
        $this->assertEquals('', $options['datetime_format']);
        $this->assertEquals('', $options['maps_api_key']);
        $this->assertEquals(true, $options['responsive']);
        $this->assertEquals(250, $options['debounce_timeout']);
    }

    public function testGetVolcano()
    {
        $this->assertInstanceOf(Volcano::class, $this->lava->getVolcano());
    }

    public function testGetScriptManager()
    {
        $this->assertInstanceOf(ScriptManager::class, $this->lava->getScriptManager());
    }

    public function testGettingAnInstanceOfDataFactory()
    {
        $this->assertInstanceOf(DataFactory::class, $this->lava->DataFactory());
    }

    public function testToArray()
    {
        $lavaArray = $this->lava->toArray();

        $this->assertTrue(is_array($lavaArray));
        $this->assertArrayHasKey('options', $lavaArray);
        $this->assertArrayHasKey('renderables', $lavaArray);
        $this->assertTrue(is_array($lavaArray['options']));
        $this->assertTrue(is_array($lavaArray['renderables']));
    }

    public function testControlWrapperCreation()
    {
        $controlWrapper = $this->lava->ControlWrapper(
            Mockery::mock(Filter::class),
            'filter-div-id'
        );

        $this->assertInstanceOf(ControlWrapper::class, $controlWrapper);
    }

    public function testChartWrapperCreation()
    {
        $chart = Mockery::mock(Chart::class)
                        ->shouldReceive('setRenderable')
                        ->once()
                        ->with(false)
                        ->getMock();

        $chartWrapper = $this->lava->ChartWrapper(
            $chart,
            'filter-div-id'
        );

        $this->assertInstanceOf(ChartWrapper::class, $chartWrapper);
    }

    public function testCreatingDataTableViaLavaAlias()
    {
        $this->assertInstanceOf(DataTable::class, $this->lava->DataTable());
    }

    public function testCreatingDataTableViaLavaAliasWithTimezone()
    {
        $this->assertInstanceOf(DataTable::class, $this->lava->DataTable('America/Los_Angeles'));
    }

    /**
     * @dataProvider chartTypeProvider
     * @param string $chartType
     */
    public function testCreatingChartsViaLavaAlias($chartType)
    {
        $chart = $this->lava->$chartType('My'.$chartType);

        $this->assertInstanceOf(self::CHART_NAMESPACE.$chartType, $chart);
    }

    /**
     * @dataProvider chartTypeProvider
     * @param string $chartType
     */
    public function testManuallyStoringChartsInTheVolcano($chartType)
    {
        $chartClass = self::CHART_NAMESPACE.$chartType;

        $chart = new $chartClass('My'.$chartType);

        $this->assertInstanceOf(self::CHART_NAMESPACE.$chartType, $chart);

        $this->lava->store($chart);

        $volcano = $this->lava->getVolcano();

        $this->assertInstanceOf(self::CHART_NAMESPACE.$chartType, $volcano->get('My'.$chartType));
    }

    public function testManuallyStoringDashboardsInTheVolcano()
    {
        $dashboard = new Dashboard('MyDash');

        $this->assertInstanceOf(Dashboard::class, $dashboard);

        $this->lava->store($dashboard);

        $volcano = $this->lava->getVolcano();

        $this->assertInstanceOf(Dashboard::class, $volcano->get('MyDash'));
    }

    /**
     * @dataProvider chartTypeProvider
     * @param string $chartType
     */
    public function testChartsCreatedViaLavaAliasAreStoredInVolcano($chartType)
    {
        $this->lava->$chartType('My'.$chartType);

        $volcano = $this->lava->getVolcano();

        $this->assertInstanceOf(self::CHART_NAMESPACE.$chartType, $volcano->get('My'.$chartType));
    }

    public function testCreatingDashboards()
    {
        $dashboard = $this->lava->Dashboard('MyDash');

        $this->assertInstanceOf(Dashboard::class, $dashboard);
    }

    /**
     * @depends testCreatingDashboards
     */
    public function testDashboardsCreatedViaLavaAreStoredInVolcano()
    {
        $this->lava->Dashboard('MyDash');

        $volcano = $this->lava->getVolcano();

        $this->assertInstanceOf(Dashboard::class, $volcano->get('MyDash'));
    }

    /**
     * @dataProvider chartTypeProvider
     * @param string $chartType
     */
    public function testGettingStoredChartsFromVolcano($chartType)
    {
        $this->lava->$chartType('My'.$chartType);

        $this->assertInstanceOf(self::CHART_NAMESPACE.$chartType, $this->lava->get('My'.$chartType));
    }

    /**
     * @dataProvider chartTypeProvider
     * @param string $chartType
     */
    public function testExistsWithChartsInVolcano($chartType)
    {
        $this->lava->$chartType('My'.$chartType);

        $this->assertTrue($this->lava->exists('My'.$chartType));
    }

    /**
     * @dataProvider formatTypeProvider
     * @param string $formatType
     */
    public function testCreatingFormatsViaLavaAlias($formatType)
    {
        $format = $this->lava->$formatType();

        $this->assertInstanceOf(Format::class, $format);
    }

    /**
     * @dataProvider filterTypeProvider
     * @param string $filterType
     */
    public function testCreatingFiltersViaLavaAlias($filterType)
    {
        $filter = $this->lava->$filterType();

        $this->assertInstanceOf(Filter::class, $filter);
    }

    public function testRenderChart()
    {
        $this->markTestSkipped('Re-evaluation of render() calling renderAll() is needed.');

        $this->lava->LineChart('test', Mockery::mock(DataTable::class));

        $this->assertTrue(is_string($this->lava->render('LineChart', 'test', 'test-div')));
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidFormatType
     */
    public function testCreatingInvalidFormatType()
    {
        $this->lava->BookFormat();
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidFilterType
     */
    public function testCreatingInvalidFilterType()
    {
        $this->lava->NoiseFilter();
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidChartType
     */
    public function testCreatingNonExistentChartViaAlias()
    {
        $this->lava->TacoChart();
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testNonExistentLavachartsMethod()
    {
        $this->lava->DataTebal();
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidArgumentException
     */
    public function testCreateChartWithMissingLabel()
    {
        $this->lava->LineChart();
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidArgumentException
     */
    public function testCreateChartWithInvalidLabel()
    {
        $this->lava->LineChart(5);
    }

    public function testLavaJsOutput()
    {
        $lavaJsSrc = file_get_contents('../javascript/dist/lava.js');
        $lavaJsOutput = $this->lava->lavajs();

        // Get the script tag contents
        preg_match('/(?:<script\b[^>]*>)([\s\S]*?)(?:<\/script>)/', $lavaJsOutput, $matches);

        // Replace the "OPTIONS_JSON" placeholder with the actual options in the lava.js source.
        $lavaJsSrc = preg_replace('/OPTIONS_JSON/', $this->lava->getOptions()->toJson(), $lavaJsSrc);

        // The lava.js source, with json-ed options, should now match the lavajs() output
        $this->assertEquals($lavaJsSrc, $matches[1]);

        // The ScriptManager should also know that the lava.js module has be output.
        $this->assertTrue($this->lava->getScriptManager()->lavaJsLoaded());
    }
}
