<?php namespace Lavacharts\Tests\Helpers;

use \Lavacharts\Tests\ProvidersTestCase;
use \Lavacharts\Helpers\Helpers as h;

class HelperNonEmptyStringTest extends ProvidersTestCase
{
    public function testWithValidString()
    {
        $this->assertTrue(h::nonEmptyString('Im not empty!'));
    }

    public function testWithEmptyString()
    {
        $this->assertFalse(h::nonEmptyString(''));
    }

    /**
     * @dataProvider nonStringProvider
     */
    public function testWithBadTypes($badTypes)
    {
        $this->assertFalse(h::nonEmptyString($badTypes));
    }
}
