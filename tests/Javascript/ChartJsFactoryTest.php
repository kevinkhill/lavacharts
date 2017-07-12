<?php

namespace Khill\Lavacharts\Tests\Javascript;

use Khill\Lavacharts\Javascript\ChartJsFactory;
use Khill\Lavacharts\Tests\ProvidersTestCase;
use Khill\Lavacharts\DataTables\DataTable;
use Khill\Lavacharts\Javascript\JavascriptFactory;

/**
 * @property \Mockery\Mock                               mockChartLabel
 * @property \Mockery\Mock                               mockElementId
 * @property \Khill\Lavacharts\Javascript\ChartJsFactory factory
 * @property \Mockery\Mock                               mlc
 */
class ChartJsFactoryTest extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->mockChartLabel = \Mockery::mock('\Khill\Lavacharts\Values\Label', ['TestChart'])->makePartial();
        $this->mockElementId = \Mockery::mock('\Khill\Lavacharts\Values\ElementId', ['my-chart'])->makePartial();

        $datatable = new DataTable();

        $datatable->addColumn('number')
                  ->addColumn('number')
                  ->addColumn('number')
                  ->addRow([10101, 12345, 67890]);

        $this->mlc = \Mockery::mock('Khill\Lavacharts\Charts\LineChart', [$this->mockChartLabel, $datatable, [
            'elementId' => 'chart-div',
            'legend' => 'none'
        ]])->makePartial();

        $this->factory = new ChartJsFactory($this->mlc);
    }

    public function testGetTemplateVars()
    {
        $getTemplateVars = new \ReflectionMethod($this->factory, 'getTemplateVars');
        $getTemplateVars->setAccessible(true);

        $templateVars = $getTemplateVars->invoke($this->factory);



        $this->assertEquals($templateVars['chartLabel'], 'TestChart');
        $this->assertEquals($templateVars['chartType'], 'LineChart');
        $this->assertEquals($templateVars['chartVer'], '1');
        $this->assertEquals($templateVars['chartClass'], 'google.visualization.LineChart');
        $this->assertEquals($templateVars['chartPackage'], 'corechart');

        $this->assertEquals($templateVars["chartData"], '{"cols":[{"type":"number"},{"type":"number"},{"type":"number"}],"rows":[{"c":[{"v":10101},{"v":12345},{"v":67890}]}]}');

        $this->assertEquals($templateVars['elemId'], 'chart-div');
        $this->assertEquals($templateVars['pngOutput'], false);
        $this->assertEquals($templateVars['formats'], '');
        $this->assertEquals($templateVars['events'], '');
        $this->assertEquals($templateVars['chartOptions'], '{"elementId":"chart-div","legend":"none"}');
    }
}
