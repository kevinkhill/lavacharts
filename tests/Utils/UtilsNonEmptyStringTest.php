<?php namespace Khill\Lavacharts\Tests\Helpers;

use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Tests\ProvidersTestCase;

class HelperNonEmptyStringTest extends ProvidersTestCase
{
    public function testWithValidString()
    {
        $this->assertTrue(Utils::nonEmptyString('Im not empty!'));
    }

    public function testWithEmptyString()
    {
        $this->assertFalse(Utils::nonEmptyString(''));
    }

    /**
     * @dataProvider nonStringProvider
     */
    public function testWithBadTypes($badTypes)
    {
        $this->assertFalse(Utils::nonEmptyString($badTypes));
    }
}
