<?php

namespace Khill\Lavacharts\Tests\Javascript;

use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Khill\Lavacharts\DataTables\DataTable;
use \Khill\Lavacharts\Javascript\JavascriptFactory;

class JavascriptFactoryTest extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->jsf = new JavascriptFactory;

        $this->mockChartLabel = \Mockery::mock('\Khill\Lavacharts\Values\Label', ['TestChart'])->makePartial();
        $this->mockElementId = \Mockery::mock('\Khill\Lavacharts\Values\ElementId', ['my-chart'])->makePartial();

        $datatable = new DataTable();

        $datatable->addColumn('number')
                  ->addColumn('number')
                  ->addColumn('number')
                  ->addRow([10101, 12345, 67890]);

        $this->mlc = \Mockery::mock('Khill\Lavacharts\Charts\LineChart', [$this->mockChartLabel, $datatable])->makePartial();
    }

    public function testGetChartJsOutput()
    {
        $js = $this->jsf->getChartJs($this->mlc, $this->mockElementId);

        $this->assertTrue(strpos($js, 'my-chart') > 0 ? true : false);
    }
}
