<?php

namespace Khill\Lavacharts\Tests\DataTables\Cells;

use Khill\Lavacharts\Support\Options;
use Khill\Lavacharts\Tests\ProvidersTestCase;
use Khill\Lavacharts\DataTables\Cells\Cell;

/**
 * @property Cell   testCell
 * @property string cellJson
 */
class CellTest extends ProvidersTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->cellJson = '{"v":1,"f":"low","p":{"textStyle":{"fontName":"Arial"}}}';

        $this->testCell = $cell = new Cell(1, 'low', ['textStyle' => ['fontName' => 'Arial']]);
    }

    public function testConstructorArgs()
    {
        $this->assertEquals(1, $this->testCell->getValue());
        $this->assertEquals('low', $this->testCell->getFormat());
        $this->assertInstanceOf(Options::class, $this->testCell->getOptions());
    }

    public function testCellToArray()
    {
        $cellArr = $this->testCell->toArray();

        $this->assertTrue(is_array($cellArr));

        $this->assertArrayHasKey('v', $cellArr);
        $this->assertEquals(1, $cellArr['v']);

        $this->assertArrayHasKey('f', $cellArr);
        $this->assertEquals('low', $cellArr['f']);

        $this->assertArrayHasKey('p', $cellArr);
        $this->assertTrue(is_array($cellArr['p']));

        $this->assertArrayHasKey('textStyle', $cellArr['p']);
        $this->assertTrue(is_array($cellArr['p']['textStyle']));

        $this->assertArrayHasKey('fontName', $cellArr['p']['textStyle']);
        $this->assertEquals('Arial', $cellArr['p']['textStyle']['fontName']);

    }

    public function testCellJsonSerialization()
    {
        $this->assertEquals(
            $this->cellJson,
            json_encode($this->testCell)
        );
    }

    public function testCellToJson()
    {
        $this->assertEquals(
            $this->cellJson,
            $this->testCell->toJson()
        );
    }

    /**
     * @group error
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidArgumentException
     */
    public function testCreatingCellWithInvalidFormatString()
    {
        new Cell(1, ['NotString']);
    }

    /**
     * @group error
     * @expectedException \PHPUnit_Framework_Error
     */
    public function testCreatingCellWithInvalidOptions()
    {
        new Cell(1, 'low', 5.2);
    }
}



