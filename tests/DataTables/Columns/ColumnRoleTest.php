<?php

namespace Khill\Lavacharts\Tests\DataTables\Columns;

use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Khill\Lavacharts\DataTables\Columns\ColumnRole;

class ColumnRoleTest extends ProvidersTestCase
{
    public function columnRoleTypeProvider()
    {
        return [
            ['annotation'],
            ['annotationText'],
            ['certainty'],
            ['emphasis'],
            ['interval'],
            ['scope'],
            ['style'],
            ['tooltip']
        ];
    }

    /**
     * @dataProvider columnRoleTypeProvider
     * @covers \Khill\Lavacharts\DataTables\Columns\ColumnRole::__construct
     */
    public function testConstructorWithValidTypes($roleTypes)
    {
        $role = new ColumnRole($roleTypes);

        $this->assertEquals($roleTypes, $this->getPrivateProperty($role, 'type'));
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidColumnRole
     * @covers \Khill\Lavacharts\DataTables\Columns\ColumnRole::__construct
     */
    public function testConstructorWithBadTypes($badVals)
    {
        new ColumnRole($badVals);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidColumnRole
     * @covers \Khill\Lavacharts\DataTables\Columns\ColumnRole::__construct
     */
    public function testConstructorWithInvalidRoleType()
    {
        new ColumnRole('snack captain');
    }

    /**
     * @depends testConstructorWithValidTypes
     * @covers \Khill\Lavacharts\DataTables\Columns\ColumnRole::__toString
     */
    public function testToString()
    {
        $role = new ColumnRole('interval');

        $this->assertEquals('interval', (string) $role);
    }

    /**
     * @depends testConstructorWithValidTypes
     * @covers \Khill\Lavacharts\DataTables\Columns\ColumnRole::jsonSerialize
     */
    public function testJsonSerialization()
    {
        $role = new ColumnRole('interval');

        $json = '{"role":"interval"}';

        $this->assertEquals($json, json_encode($role));
    }
}



