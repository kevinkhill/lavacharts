<?php namespace Khill\Lavacharts\Tests;

use Khill\Lavacharts\JavascriptFactory;
use Khill\Lavacharts\Charts\LineChart;
use Khill\Lavacharts\Configs\DataTable;

class JavascriptFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testIfInstanceOfJavascriptFactory()
    {
        $c = new LineChart('test');
        $c->dataTable(new DataTable);

        $this->assertInstanceOf('Khill\Lavacharts\JavascriptFactory', new JavascriptFactory($c, 'div_id'));
    }

    public function testBuildOutput()
    {
        $c = new LineChart('test');
        $c->dataTable(new DataTable);

        $jsf = new JavascriptFactory($c, 'div_id');

        $js = $jsf->buildOutput();

        $this->assertTrue(is_string($js));
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\DataTableNotFound
     */
    public function testJavascriptFactoryChartMissingDataTable()
    {
        $c = new LineChart('test');

        $jsf = new JavascriptFactory($c, 'div_id');
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidElementId
     */
    public function testJavascriptFactoryInvalidElementId()
    {
        $c = new LineChart('test');
        $c->dataTable(new DataTable);

        $jsf = new JavascriptFactory($c, array());
    }
}
