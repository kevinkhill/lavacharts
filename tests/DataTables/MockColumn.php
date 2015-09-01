<?php

namespace Khill\Lavacharts\Tests\DataTables;

use \Khill\Lavacharts\DataTables\Columns\Column;

class MockColumn extends Column
{
    /**
     * Type of column.
     *
     * @var string
     */
    const TYPE = 'mock';

    /**
     * Creates a new column object.
     *
     * @access public
     * @param  string $label Label for the column.
     */
    public function __construct($label = '')
    {
        parent::__construct($label);
    }
}
