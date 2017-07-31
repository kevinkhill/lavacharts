<?php

namespace Khill\Lavacharts\Tests\Charts;

use Khill\Lavacharts\Charts\LineChart;
use Khill\Lavacharts\DataTables\DataTable;
use Khill\Lavacharts\Tests\JsonTestCase;

/**
 * @property LineChart chart
 */
class ChartJsonTest extends JsonTestCase
{
    const CHART_SCHEMA = '../Schema/chart.json';

    public function setUp()
    {
        parent::setUp();

        $datatable = new DataTable();
        $datatable->addDateColumn('Date');
        $datatable->addNumberColumn('Employee');
        $datatable->setDateTimeFormat('Y-m-d');

        for ($day = 1; $day < 30; $day++) {
            $datatable->addRow(
                ['2016-1-' . $day, rand(550000,1000000)]
            );
        }

        $this->chart = new LineChart('Sales', $datatable, [
            'elementId' => 'chart',
            'legend' => 'bottom'
        ]);
    }

    public function getJson()
    {
        return $this->chart->toJson();
    }

    /** @test */
    public function validate_chart_json_against_schema()
    {
        $data = json_decode($this->getJson());

        $this->assertValidJsonWithSchema($data, static::CHART_SCHEMA);
    }

    /** @test */
    public function has_element_id()
    {
        $this->assertJsonFragment([
            'elementId' => 'chart'
        ]);
    }

    /** @test */
    public function has_label()
    {
        $this->assertJsonFragment([
            'label' => 'Sales'
        ]);
    }

    /** @test */
    public function has_options()
    {
        $this->assertJsonFragment([
            'options' => [
                'legend' => 'bottom'
            ]
        ]);
    }
}
