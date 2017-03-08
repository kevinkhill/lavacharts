<?php

namespace Khill\Lavacharts\DataTables\Rows;

use ArrayAccess;
use Carbon\Carbon;
use Khill\Lavacharts\DataTables\DataTable;
use Khill\Lavacharts\DataTables\Cells\DateCell;
use Khill\Lavacharts\Exceptions\InvalidCellCount;
use Khill\Lavacharts\Exceptions\InvalidRowDefinition;
use Khill\Lavacharts\Support\Traits\ParameterValidatorsTrait as ParameterValidators;

/**
 * RowFactory Class
 *
 * The RowFactory creates new rows for the DataTables.
 *
 *
 * @package   Khill\Lavacharts\DataTables\Rows
 * @since     3.0.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
class RowFactory
{
    use ParameterValidators;

    /**
     * DataTable to reference when creating new rows.
     *
     * @var \Khill\Lavacharts\DataTables\DataTable
     */
    private $datatable;

    /**
     * Creates a new RowFactory instance.
     *
     * @access public
     * @param  \Khill\Lavacharts\DataTables\DataTable $datatable
     */
    public function __construct(DataTable $datatable)
    {
        $this->datatable = $datatable;
    }

    /**
     * Creates a new Row object from a collection of values.
     *
     * @param  array|ArrayAccess $rowDef Collection of values to assign to the row.
     * @return \Khill\Lavacharts\DataTables\Rows\Row
     * @throws \Khill\Lavacharts\Exceptions\InvalidCellCount
     * @throws \Khill\Lavacharts\Exceptions\InvalidDateTimeString
     * @throws \Khill\Lavacharts\Exceptions\InvalidRowDefinition
     */
    public function create($rowDef)
    {
        if ($rowDef !== null && $this->behavesAsArray($rowDef)) {
            throw new InvalidRowDefinition($rowDef);
        }

        if ($rowDef === null || ($this->behavesAsArray($rowDef) && empty($rowDef))) {
            return new NullRow($this->datatable->getColumnCount());
        }

        $cellCount   = count($rowDef);
        $columnCount = $this->datatable->getColumnCount();

        if ($cellCount > $columnCount) {
            throw new InvalidCellCount($cellCount, $columnCount);
        }

        $columnTypes    = $this->datatable->getColumnTypes();
        $dateTimeFormat = $this->datatable->getDateTimeFormat();

        $rowData = [];

        foreach ($rowDef as $index => $cell) {
            if ((bool) preg_match('/date|datetime|timeofday/', $columnTypes[$index]) === true) {
                if ($cell instanceof Carbon) {
                    $rowData[] = new DateCell($cell);
                } else {
                    if (isset($dateTimeFormat)) {
                        $rowData[] = DateCell::parseString($cell, $dateTimeFormat);
                    } else {
                        $rowData[] = DateCell::parseString($cell);
                    }
                }
            } else {
                $rowData[] = $cell;
            }
        }

        return new Row($rowData);
    }
}
