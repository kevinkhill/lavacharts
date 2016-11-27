<?php

namespace Khill\Lavacharts\Tests\Values;

use Khill\Lavacharts\Tests\ProvidersTestCase;
use Khill\Lavacharts\Values\Label;

class LabelTest extends ProvidersTestCase
{
    public function testLabelWithString()
    {
        $label = new Label('TheChart');

        $this->assertEquals('TheChart', (string) $label);
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidLabel
     */
    public function testLabelWithBadTypes($badTypes)
    {
        $label = new Label($badTypes);
    }
}
