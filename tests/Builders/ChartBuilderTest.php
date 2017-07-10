<?php

namespace Khill\Lavacharts\Tests\Builders;

use Khill\Lavacharts\Builders\ChartBuilder;
use Khill\Lavacharts\Charts\LineChart;
use Khill\Lavacharts\DataTables\DataTable;
use Khill\Lavacharts\Tests\ProvidersTestCase;

/**
 * @property \Khill\Lavacharts\Builders\ChartBuilder builder
 */
class ChartBuilderTest extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->builder = new ChartBuilder();
    }

    public function testWithLabelAndDataTable()
    {
        $this->builder->setType('LineChart');
        $this->builder->setLabel('taco');
        $this->builder->setDatatable($this->getMockDataTable());

        $chart = $this->builder->getChart();

        $this->assertInstanceOf(LineChart::class, $chart);
        $this->assertEquals('taco', $chart->getLabel());
        $this->assertInstanceOf(DataTable::class, $chart->getDataTable());
    }

    /**
     * @depends testWithLabelAndDataTable
     */
    public function testWithLabelAndDataTableAndOptions()
    {
        $this->builder->setType('LineChart');
        $this->builder->setLabel('taco');
        $this->builder->setDatatable($this->getMockDataTable());
        $this->builder->setOptions(['tacos' => 'good']);

        $chart = $this->builder->getChart();
        $options = $chart->getOptions();

        $this->assertArrayHasKey('tacos', $options);
        $this->assertEquals('good', $options['tacos']);
    }

    /**
     * @depends testWithLabelAndDataTable
     * @depends testWithLabelAndDataTableAndOptions
     */
    public function testWithLabelAndDataTableAndOptionsAndElementId()
    {
        $this->builder->setType('LineChart');
        $this->builder->setLabel('taco');
        $this->builder->setDatatable($this->getMockDataTable());
        $this->builder->setOptions(['tacos' => 'good']);
        $this->builder->setElementId('platter');

        $chart = $this->builder->getChart();

        $elementId = $this->inspect($chart, 'elementId');

        $this->assertInstanceOf('\Khill\Lavacharts\Values\ElementId', $elementId);
        $this->assertEquals('platter', (string) $elementId);
    }
}
