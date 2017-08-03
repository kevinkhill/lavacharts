<?php

namespace Khill\Lavacharts\Tests\Charts;

use Khill\Lavacharts\Charts\Chart;
use Khill\Lavacharts\Charts\ChartFactory;
use Khill\Lavacharts\DataTables\DataTable;
use Khill\Lavacharts\Tests\JsonTestCase;
use Khill\Lavacharts\Tests\Providers\DataTableProvider;

class DataTableJsonTest extends JsonTestCase
{
    const DATATABLE_SCHEMA = '../JsonSchema/datatable.json';

    /**
     * @var DataTable
     */
    private $datatable;

    public function setUp()
    {
        parent::setUp();

        $this->datatable = new DataTable([
            'datetime_format' => 'Y-m-d'
        ]);

        $this->datatable->addColumns([
            ['date'  , 'Report Date'],
            ['number', 'Profits'],
            ['number', 'Loses'],
            ['number', 'Company Value']
        ]);

        for ($day = 1; $day < 30; $day++) {
            $this->datatable->addRow(
                [
                    '2016-1-'.$day,
                    rand(90000, 100000),
                    rand(90000, 100000),
                    rand(9000000, 1000000) * 1.001,
                ]
            );
        }
    }

    public function chartTypeProvider()
    {
        $types = [];

        foreach (ChartFactory::TYPES as $chartType) {
            $types[$chartType] = [$chartType];
        }

        return $types;
    }

    /**
     * @dataProvider chartTypeProvider
     * @param string $chartType
     */
    public function testValidateDataTableJsonAgainstSchema($chartType)
    {
        $datatable = DataTableProvider::get($chartType);

        $decodedDataTableJson = json_decode($datatable->toJson());

        $this->assertValidJsonWithSchema($decodedDataTableJson, static::DATATABLE_SCHEMA);
    }
}
