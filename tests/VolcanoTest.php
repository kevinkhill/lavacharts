<?php namespace Khill\Lavacharts\Tests;

use \Khill\Lavacharts\Volcano;
use \Khill\Lavacharts\Charts\LineChart;

 //@TODO fix this to mockery

class VolcanoTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testStoreChart()
    {
        $v = new Volcano;
        $c = new LineChart('testchart');

        $this->assertTrue($v->storeChart($c));
    }

    public function testGetChart()
    {
        $v = new Volcano;
        $c = new LineChart('testchart');

        $v->storeChart($c);

        $this->assertInstanceOf('\Khill\Lavacharts\Charts\LineChart', $v->getChart('LineChart', 'testchart'));
    }

    public function testCheckChart()
    {
        $v = new Volcano;
        $c = new LineChart('testchart');

        $v->storeChart($c);

        $this->assertTrue($v->checkChart('LineChart', 'testchart'));

        $this->assertFalse($v->checkChart('LaserChart', 'testchart'));
        $this->assertFalse($v->checkChart('LineChart', 'testing123chart'));
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\ChartNotFound
     */
    public function testGetNonExistantTypeChart()
    {
        $v = new Volcano;
        $c = new LineChart('testchart');

        $v->storeChart($c);
        $v->getChart('LaserChart', 'testchart');
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\ChartNotFound
     */
    public function testGetNonExistantLabelChart()
    {
        $v = new Volcano;
        $c = new LineChart('testchart');

        $v->storeChart($c);
        $v->getChart('LineChart', 'superduperchart');
    }
}
