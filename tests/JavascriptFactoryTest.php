<?php

namespace Khill\Lavacharts\Tests;


use \Khill\Lavacharts\Javascript\JavascriptFactory;
use \Mockery as m;

class JavascriptFactoryTest extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->jsf = new JavascriptFactory;

        $this->mockChartLabel = m::mock('\Khill\Lavacharts\Values\Label', ['TestChart'])->makePartial();
        $this->mockElementId = m::mock('\Khill\Lavacharts\Values\ElementId', ['chart'])->makePartial();

        $this->mdt = m::mock('Khill\Lavacharts\Configs\DataTable')->makePartial();
        $this->mdt->addColumn('number')
                  ->addColumn('number')
                  ->addColumn('number')
                  ->addRow([10101, 12345, 67890]);

        $this->mlc = m::mock('Khill\Lavacharts\Charts\LineChart', [$this->mockChartLabel, $this->mdt])->makePartial();
    }

    public function testGetChartJsOutput()
    {
        $this->mlc->datatable($this->mdt);

        $js = $this->jsf->getChartJs($this->mlc, $this->mockElementId);

        $this->assertTrue(strpos($js, 'chart') > 0 ? true : false);
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
        $this->mlc->datatable($this->mdt);

        $this->jsf->getChartJs($this->mlc, $badTypes);
    }
}
