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

    /**
     * @dataProvider iterableWithStrings
     *
     * @param \Traversable|string[] $iterable
     */
    public function testNonEmptyStringInArrayWithValidTraversableWithStrings($iterable)
    {
        $this->assertTrue(Utils::nonEmptyStringInArray($iterable[0], $iterable));
    }

    /**
     * @dataProvider iterableInArrayWithBadData
     *
     * @param \Traversable|string[] $iterable
     * @param string                $nonExistingString
     */
    public function testNonEmptyStringInArrayWithBadData($iterable, $nonExistingString)
    {
        $this->assertFalse(Utils::nonEmptyStringInArray($nonExistingString, $iterable));
    }

    /**
     * @dataProvider iterableInArrayWithGoodData
     *
     * @param \Traversable|string[] $iterable
     */
    public function testCheckIfInArrayForValueWithGoodData($iterable)
    {
        $this->assertTrue(Utils::checkInArrayForValue($iterable[1], $iterable));
    }

    /**
     * @dataProvider iterableInArrayWithBadData
     *
     * @param \Traversable|string[] $iterable
     * @param mixed                 $valueToCheck
     */
    public function testCheckIfInArrayForValueWithBadData($iterable, $valueToCheck)
    {
        $this->assertFalse(Utils::checkInArrayForValue($valueToCheck, $iterable));
    }
}
