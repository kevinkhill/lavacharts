<?php

namespace Khill\Lavacharts\Tests\Utils;

use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Tests\ProvidersTestCase;

class UtilsNonEmptyStringTest extends ProvidersTestCase
{

    /**
     * @dataProvider validStringProvider
     *
     * @param string|object $variable
     */
    public function testWithValidString($variable)
    {
        $this->assertTrue(Utils::nonEmptyString($variable));
    }

    public function testWithEmptyString()
    {
        $this->assertFalse(Utils::nonEmptyString(''));
    }

    /**
     * @dataProvider nonStringProvider
     *
     * @param mixed $badTypes
     */
    public function testWithBadTypes($badTypes)
    {
        $this->assertFalse(Utils::nonEmptyString($badTypes));
    }
}
