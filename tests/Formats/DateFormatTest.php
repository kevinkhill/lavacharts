<?php namespace Khill\Lavacharts\Tests\Formats;

use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Khill\Lavacharts\Formats\DateFormat;

class DateFormatTest extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->dateFormat = new DateFormat;
    }

    public function testInstanceOf()
    {
        $this->assertInstanceOf('\\Khill\\Lavacharts\\Formats\\DateFormat', $this->dateFormat);
    }

    public function testStaticFormatType()
    {
        $this->assertEquals('DateFormat', DateFormat::TYPE);
    }

    public function testFormatTypeMethodWithGoodVals()
    {
        $this->dateFormat->formatType('short');
        $this->assertEquals('short', $this->dateFormat->formatType);

        $this->dateFormat->formatType('medium');
        $this->assertEquals('medium', $this->dateFormat->formatType);

        $this->dateFormat->formatType('long');
        $this->assertEquals('long', $this->dateFormat->formatType);
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testFormatTypeMethodWithBadVal()
    {
        $this->dateFormat->formatType('pickles');
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testFormatTypeMethodWithBadTypes($badTypes)
    {
        $this->dateFormat->formatType($badTypes);
    }

    public function testPatternWithString()
    {
        $this->dateFormat->pattern('Y-m-d');
        $this->assertEquals('Y-m-d', $this->dateFormat->pattern);
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testPatternWithBadTypes($badTypes)
    {
        $this->dateFormat->pattern($badTypes);
    }

    public function testTimeZoneWithString()
    {
        $this->dateFormat->timeZone('PST');
        $this->assertEquals('PST', $this->dateFormat->timeZone);
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testTimezoneWithBadTypes($badTypes)
    {
        $this->dateFormat->timeZone($badTypes);
    }
}
