<?php namespace Lavacharts\Tests;

use \Lavacharts\Tests\ProvidersTestCase;
use \Lavacharts\JavascriptFactory;
use \Mockery as m;

class JavascriptFactoryTest extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->jsf = new JavascriptFactory;

        $this->mlc = m::mock('Lavacharts\Charts\LineChart', array('TestChart'))->makePartial();
        $this->mdt = m::mock('Lavacharts\Configs\DataTable')->makePartial();

        $this->mdt->addColumn('number')
                  ->addColumn('number')
                  ->addColumn('number')
                  ->addRow(array(10101, 12345, 67890));
    }

    public function testGetChartJsOutput()
    {
        $this->mlc->datatable($this->mdt);

        $js = $this->jsf->getChartJs($this->mlc, 'div_id');

        $this->assertTrue(strpos($js, 'div_id') > 0 ? true : false);
    }

    /**
     * @expectedException Lavacharts\Exceptions\DataTableNotFound
     */
    public function testGetChartJsWithMissingDataTable()
    {
        $this->jsf->getChartJs($this->mlc);
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException Lavacharts\Exceptions\InvalidElementId
     */
    public function testGetChartJsWithBadElementIdTypes($badTypes)
    {
        $this->mlc->datatable($this->mdt);

        $js = $this->jsf->getChartJs($this->mlc, $badTypes);
    }
}
