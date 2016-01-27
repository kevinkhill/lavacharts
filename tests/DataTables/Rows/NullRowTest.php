<?php

namespace Khill\Lavacharts\Tests\DataTables\Rows;

use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Khill\Lavacharts\DataTables\Rows\NullRow;

class NullRowTest extends ProvidersTestCase
{
    /**
     * @covers \Khill\Lavacharts\DataTables\Rows\NullRow::__construct
     */
    public function testConstructorWithInt()
    {
        $row = new NullRow(3);

        $values = $this->getPrivateProperty($row, 'values');

        $this->assertNull($values[0]);
        $this->assertNull($values[1]);
        $this->assertNull($values[2]);
    }

    /**
     * @covers \Khill\Lavacharts\DataTables\Rows\NullRow::__construct
     * @dataProvider nonIntProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testConstructorWithBadTypes($badTypes)
    {
        new NullRow($badTypes);
    }

    /**
     * @depends testConstructorWithInt
     * @covers \Khill\Lavacharts\DataTables\Rows\Row::jsonSerialize
     */
    public function testJsonSerialization()
    {
        $row = new NullRow(3);

        $json = '{"c":[{"v":null},{"v":null},{"v":null}]}';

        $this->assertEquals($json, json_encode($row));
    }
}



