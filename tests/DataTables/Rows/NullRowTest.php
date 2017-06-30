<?php

namespace Khill\Lavacharts\Tests\DataTables\Rows;

use Khill\Lavacharts\DataTables\Rows\Row;
use Khill\Lavacharts\Tests\ProvidersTestCase;

class NullRowTest extends ProvidersTestCase
{
    /**
     * @covers \Khill\Lavacharts\DataTables\Rows\Row::createNull
     * @covers \Khill\Lavacharts\DataTables\Rows\Row::jsonSerialize
     */
    public function testJsonSerialization()
    {
        $row = Row::createNull(3);

        $json = '{"c":[{"v":null},{"v":null},{"v":null}]}';

        $this->assertEquals($json, json_encode($row));
    }
}



