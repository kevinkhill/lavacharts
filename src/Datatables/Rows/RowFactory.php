<?php

namespace Khill\Lavacharts\Datatables\Rows;

use \Khill\Lavacharts\Configs\Datatable;
use \Khill\Lavacharts\Exceptions\InvalidCellCount;

class RowFactory
{
    private $datatable;

    public function __construct(DataTable $datatable)
    {
        $this->datatable = $datatable;
    }

    public function create($valueArray)
    {
        $columnCount  = $this->datatable->getColumnCount();
        $rowCellCount = count($valueArray);

        if ($rowCellCount > $columnCount) {
            throw new InvalidCellCount($rowCellCount, $columnCount);
        }

        return new Row($valueArray);
    }

    public function null()
    {
        return new NullRow($this->datatable->getColumnCount());
    }
}
