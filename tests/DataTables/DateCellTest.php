<?php

namespace Khill\Lavacharts\Tests\DataTables;


class DateCellTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Khill\Lavacharts\DataTables\DataTable
     */
    public $dt;

    public function setUp()
    {
        parent::setUp();

        $this->dt = new DataTable();
    }
}
