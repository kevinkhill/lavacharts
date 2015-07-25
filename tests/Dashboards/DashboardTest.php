<?php

namespace Khill\Lavacharts\Tests\Values;

use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Khill\Lavacharts\Values\ElementId;

class DashboardTest extends ProvidersTestCase
{
    public function testElementIdWithString()
    {
        $elementId = new ElementId('chart');

        $this->assertEquals('chart', (string) $elementId);
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidElementId
     */
    public function testElementIdWithBadTypes($badTypes)
    {
        $elementId = new ElementId($badTypes);
    }
}
