<?php

namespace Khill\Lavacharts\Tests\Javascript;

use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Khill\Lavacharts\DataTables\DataTable;
use \Khill\Lavacharts\Javascript\JavascriptFactory;
use \Mockery as m;

class JavascriptFactoryTest extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->jsf = new JavascriptFactory;

        $this->mockChartLabel = m::mock('\Khill\Lavacharts\Values\Label', ['TestChart'])->makePartial();
        $this->mockElementId = m::mock('\Khill\Lavacharts\Values\ElementId', ['my-chart'])->makePartial();

        $datatable = new DataTable();

        $datatable->addColumn('number')
                  ->addColumn('number')
                  ->addColumn('number')
                  ->addRow([10101, 12345, 67890]);

        $this->mlc = m::mock('Khill\Lavacharts\Charts\LineChart', [$this->mockChartLabel, $datatable])->makePartial();
    }

    public function testGetChartJsOutput()
    {
        $js = $this->jsf->getChartJs($this->mlc, $this->mockElementId);

        $this->assertTrue(strpos($js, 'my-chart') > 0 ? true : false);
    }

    /**
     * @expectedException \PHPUnit_Framework_Error
     */
    public function testGetChartJsWithNoElementId()
    {
        $this->jsf->getChartJs($this->mlc);
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \PHPUnit_Framework_Error
     */
    public function testGetChartJsWithBadElementIdTypes($badTypes)
    {
        $this->jsf->getChartJs($this->mlc, $badTypes);
    }
}
