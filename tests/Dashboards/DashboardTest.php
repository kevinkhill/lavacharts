<?php

namespace Khill\Lavacharts\Tests\Dashboards;

use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Khill\Lavacharts\Dashboards\Dashboard;
use \Mockery as m;

class DashboardTest extends ProvidersTestCase
{
    public $Dashboard;

    public function setUp()
    {
        parent::setUp();

        $mockLabel = m::mock('\Khill\Lavacharts\Values\Label', ['myDash'])->makePartial();

        $this->Dashboard = new Dashboard($mockLabel);
    }

    public function testGetLabel()
    {
        $this->assertEquals('myDash', (string) $this->Dashboard->getLabel());
    }
}
