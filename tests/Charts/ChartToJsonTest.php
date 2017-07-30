<?php

namespace Khill\Lavacharts\Tests\Charts;

use JsonSchema\Validator;
use Khill\Lavacharts\Charts\LineChart;
use Khill\Lavacharts\DataTables\DataTable;
use Khill\Lavacharts\Tests\ProvidersTestCase;

/**
 * @property LineChart chart
 * @property \stdClass chartSchema
 * @property Validator validator
 */
class ChartToJsonTest extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->validator = new Validator();
        $this->chartSchema = (object) [
            '$ref' => 'file://' . realpath('../Schema/chart.json')
        ];

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
            'legend' =>'bottom'
        ]);
    }

    public function testValidateChartToJsonAgainstSchema()
    {
        $data = json_decode($this->chart->toJson());
        $schema = $this->chartSchema;
        // Validate

        $this->validator->check($data, $schema);

        $this->assertTrue($this->validator->isValid());
    }

}
