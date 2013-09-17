<?php namespace Khill\Lavacharts;

use Khill\Lavacharts\Helpers\Helpers as H;

class HelperBetweenTest extends TestCase\HelperTestCase
{

    public function testBetweenWithIntsInRange()
    {
        $this->assertTrue( H::between(0, 5, 10) );
    }

    public function testBetweenWithIntsOutOfLowerRange()
    {
        $this->assertFalse( H::between(3, 1, 10) );
    }

    public function testBetweenWithIntsOutOfUpperRange()
    {
        $this->assertFalse( H::between(6, 12, 10) );
    }

    public function testBetweenWithIntsInRangeInclusive()
    {
        $this->assertTrue( H::between(1, 1, 10) );
    }

    public function testBetweenWithIntsInRangeNonInclusive()
    {
        $this->assertFalse( H::between(1, 1, 10, FALSE) );
    }

    public function testBetweenWithFloatsInRange()
    {
        $this->assertTrue( H::between(5.7, 6.4, 10.6) );
    }

    public function testBetweenWithFloatsOutOfLowerRange()
    {
        $this->assertFalse( H::between(8.8, 6.4, 10.6) );
    }

    public function testBetweenWithFloatsOutOfUpperRange()
    {
        $this->assertFalse( H::between(6.4, 12.5, 10.6) );
    }

    public function testBetweenWithFloatsInRangeInclusive()
    {
        $this->assertTrue( H::between(6.4, 6.4, 10.6) );
    }

    public function testBetweenWithFloatsInRangeNonInclusive()
    {
        $this->assertFalse( H::between(6.4, 6.4, 10.6, FALSE) );
    }

    public function testBetweenWithBadParams()
    {
        $this->assertFalse( H::between('2', '1', '3') );
    }

}
