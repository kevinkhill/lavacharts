<?php

namespace Khill\Lavacharts\Tests\Builders;

use Khill\Lavacharts\Builders\ChartBuilder;
use Khill\Lavacharts\Charts\LineChart;
use Khill\Lavacharts\Tests\ProvidersTestCase;

class ChartBuilderTest extends ProvidersTestCase
{
    /**
     * @var \Khill\Lavacharts\Builders\ChartBuilder
     */
    public $cb;

    public function setUp()
    {
        parent::setUp();

        $this->cb = new ChartBuilder();
    }

    public function testWithLabelAndDataTable()
    {
        $this->cb->setType('LineChart');
        $this->cb->setLabel('taco');
        $this->cb->setDatatable($this->getMockDataTable());

        $chart = $this->cb->getChart();

        $this->assertInstanceOf('\\Khill\\Lavacharts\\Charts\\LineChart', $chart);
    }

    /**
     * @depends testWithLabelAndDataTable
     */
    public function testWithLabelAndDataTableAndOptions()
    {
        $this->cb->setType('LineChart');
        $this->cb->setLabel('taco');
        $this->cb->setDatatable($this->getMockDataTable());
        $this->cb->setOptions(['tacos' => 'good']);

        $chart = $this->cb->getChart();

        $this->assertInstanceOf('\\Khill\\Lavacharts\\Charts\\LineChart', $chart);
        $this->assertEquals('good', $chart->getOptions()->getValues()['tacos']);
    }

    /**
     * @depends testWithLabelAndDataTable
     * @depends testWithLabelAndDataTableAndOptions
     */
    public function testWithLabelAndDataTableAndOptionsAndElementId()
    {
        $this->cb->setType('LineChart');
        $this->cb->setLabel('taco');
        $this->cb->setDatatable($this->getMockDataTable());
        $this->cb->setOptions(['tacos' => 'good']);
        $this->cb->setElementId('platter');

        $chart = $this->cb->getChart();

        $this->assertEquals('platter', $this->getPrivateProperty($chart, 'elementId'));
    }
}
