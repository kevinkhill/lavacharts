<?php namespace Khill\Lavacharts\Tests;

use Khill\Lavacharts\Lavacharts;

class LavachartsTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testIfInstanceOfJavascriptFactory()
    {
        $lc = new Lavacharts;

        $this->assertInstanceOf('Khill\Lavacharts\Lavacharts', $lc);
        $this->assertInstanceOf('Khill\Lavacharts\Volcano', $lc->volcano);
    }
    
    public function testCreateDataTable()
    {
        $lc = new Lavacharts;

        $this->assertInstanceOf('Khill\Lavacharts\Configs\DataTable', $lc->DataTable());
    }

    /**
     * @dataProvider chartTypeProvider
     */
    public function testCreateCharts($chartType)
    {
        $lc = new Lavacharts;

        $this->assertInstanceOf('Khill\Lavacharts\Charts\\'.$chartType, $lc->$chartType('testchart'));
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\DataTableNotFound
     */
    public function testJavascriptFactoryChartMissingDataTable()
    {
        //
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidElementId
     */
    public function testJavascriptFactoryInvalidElementId()
    {
        //
    }

    public function chartTypeProvider()
    {
        return array(
            array('LineChart'),
            array('AreaChart'),
            array('PieChart'),
            array('DonutChart'),
            array('ColumnChart'),
            array('GeoChart'),
            array('ComboChart')
        );
    }

}
