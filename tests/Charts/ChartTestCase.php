<?php namespace Khill\Lavacharts\Tests\Charts;

use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Mockery as m;

class ChartTestCase extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->mockDataTable = m::mock('Khill\Lavacharts\Configs\DataTable');
    }
}
