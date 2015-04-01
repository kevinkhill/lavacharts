<?php namespace Khill\Lavacharts\Tests\Formats;

use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Khill\Lavacharts\Formats\DateFormat;

class ParentFormatTest extends ProvidersTestCase
{

    public function setUp()
    {
        parent::setUp();

        $this->options = array(
            'formatType' => 'short',
            'pattern'    => 'Y-m-d',
            'timeZone'   => 'PST'
        );

        $this->dateFormat = new DateFormat($this->options);
    }

    /**
     * @dataProvider nonArrayProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testConstructorWithBadTypes($badTypes)
    {
        new DateFormat($badTypes);
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigProperty
     */
    public function testConstructorWithBadOption()
    {
        new DateFormat(array('baked'=>'beans'));
    }

    public function testGetValues()
    {
        $this->assertEquals($this->options, $this->dateFormat->getValues());
    }

    public function testToJson()
    {
        $json = '{"formatType":"short","pattern":"Y-m-d","timeZone":"PST"}';

        $this->assertEquals($json, $this->dateFormat->toJson());
    }
}
