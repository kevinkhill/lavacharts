<?php namespace Khill\Lavacharts\Tests;

use \Khill\Lavacharts\Volcano;
use \Khill\Lavacharts\Charts\LineChart;

 //@TODO fix this to mockery

class VolcanoTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->volcano = new Volcano;
    }

    public function testStoreChart()
    {
        $c = new LineChart('testchart', $this->mockDataTable);

        $this->assertTrue($this->volcano->storeChart($c));
    }

    public function testGetChart()
    {
        $c = new LineChart('testchart', $this->mockDataTable);

        $this->volcano->storeChart($c);

        $this->assertInstanceOf('\Khill\Lavacharts\Charts\LineChart', $this->volcano->getChart('LineChart', 'testchart'));
    }

    public function testCheckChart()
    {
        $c = new LineChart('testchart', $this->mockDataTable);

        $this->volcano->storeChart($c);

        $this->assertTrue($this->volcano->checkChart('LineChart', 'testchart'));

        $this->assertFalse($this->volcano->checkChart('LaserChart', 'testchart'));
        $this->assertFalse($this->volcano->checkChart('LineChart', 'testing123chart'));
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\ChartNotFound
     */
    public function testGetNonExistantTypeChart()
    {
        $c = new LineChart('testchart', $this->mockDataTable);

        $this->volcano->storeChart($c);
        $this->volcano->getChart('LaserChart', 'testchart');
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\ChartNotFound
     */
    public function testGetNonExistantLabelChart()
    {
        $c = new LineChart('testchart', $this->mockDataTable);

        $this->volcano->storeChart($c);
        $this->volcano->getChart('LineChart', 'superduperchart');
    }
}
