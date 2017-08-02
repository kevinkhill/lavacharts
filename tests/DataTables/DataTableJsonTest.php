<?php

namespace Khill\Lavacharts\Tests\Charts;

use Khill\Lavacharts\DataTables\DataTable;
use Khill\Lavacharts\Tests\JsonTestCase;

class DataTableJsonTest extends JsonTestCase
{
    const DATATABLE_SCHEMA = '../JsonSchema/datatable.json';

    const COLUMN_SCHEMA = '../JsonSchema/column.json';

    const ROW_SCHEMA = '../JsonSchema/row.json';

    const CELL_SCHEMA = '../JsonSchema/cell.json';

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
            ['number', 'Company Value']
        ]);

        for ($day = 1; $day < 30; $day++) {
            $this->datatable->addRow(
                ['2016-1-' . $day, rand(9000000,1000000)]
            );
        }
    }

    public function testValidateDataTableJsonAgainstSchema()
    {
        $data = json_decode($this->datatable->toJson());

        $this->assertValidJsonWithSchema($data, static::DATATABLE_SCHEMA);
    }

    public function testValidateColumnJsonAgainstSchema()
    {
        foreach ($this->datatable->getColumns() as $column) {
            $this->assertValidJsonWithSchema(
                json_decode($column->toJson()),
                static::COLUMN_SCHEMA
            );
        }
    }

    public function testValidateRowJsonAgainstSchema()
    {
        foreach ($this->datatable->getRows() as $row) {
            $this->assertValidJsonWithSchema(
                json_decode($row->toJson()),
                static::ROW_SCHEMA
            );

            foreach ($row->getCells() as $cell) {
                $this->assertValidJsonWithSchema(
                    json_decode($cell->toJson()),
                    static::CELL_SCHEMA
                );
            }
        }
    }
}
