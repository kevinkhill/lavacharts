<?php

namespace Khill\Lavacharts\Tests;

use \Mockery as m;
use \Khill\Lavacharts\Volcano;
use \Khill\Lavacharts\Charts\LineChart;

 //@TODO fix this to mockery

class VolcanoTest extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->volcano = new Volcano;

        $this->mockLineChart = m::mock('\Khill\Lavacharts\Charts\LineChart', [
            'TestChart',
            $this->partialDataTable
        ]);
    }

    public function testStoreChart()
    {
        $this->assertTrue($this->volcano->storeChart($this->mockLineChart));
    }

    public function testGetChart()
    {
        $this->volcano->storeChart($this->mockLineChart);

        $this->assertInstanceOf('\Khill\Lavacharts\Charts\LineChart', $this->volcano->getChart('LineChart', 'TestChart'));
    }

    public function testCheckChart()
    {
        $this->volcano->storeChart($this->mockLineChart);

        $this->assertTrue($this->volcano->checkChart('LineChart', 'TestChart'));

        $this->assertFalse($this->volcano->checkChart('LaserChart', 'TestChart'));
        $this->assertFalse($this->volcano->checkChart('LineChart', 'testing123chart'));
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\ChartNotFound
     */
    public function testGetNonExistantTypeChart()
    {
        $this->volcano->storeChart($this->mockLineChart);
        $this->volcano->getChart('LaserChart', 'TestChart');
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\ChartNotFound
     */
    public function testGetNonExistantLabelChart()
    {
        $this->volcano->storeChart($this->mockLineChart);
        $this->volcano->getChart('LineChart', 'superduperchart');
    }
}
