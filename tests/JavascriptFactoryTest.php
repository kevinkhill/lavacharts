<?php namespace Khill\Lavacharts\Tests;

use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Khill\Lavacharts\JavascriptFactory;
use \Mockery as m;

class JavascriptFactoryTest extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->jsf = new JavascriptFactory;

        $this->mlc = m::mock('Khill\Lavacharts\Charts\LineChart', array('TestChart'))->makePartial();
        $this->mdt = m::mock('Khill\Lavacharts\Configs\DataTable')->makePartial();

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
     * @expectedException Khill\Lavacharts\Exceptions\DataTableNotFound
     */
    public function testGetChartJsWithMissingDataTable()
    {
        $this->jsf->getChartJs($this->mlc);
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidElementId
     */
    public function testGetChartJsWithBadElementIdTypes($badTypes)
    {
        $this->mlc->datatable($this->mdt);

        $js = $this->jsf->getChartJs($this->mlc, $badTypes);
    }
}
