<?php namespace Khill\Lavacharts\Tests\Utilss;

use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Tests\ProvidersTestCase;

class UtilsNonEmptyStringTest extends ProvidersTestCase
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
