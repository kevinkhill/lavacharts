<?php namespace Khill\Lavacharts\Tests\Configs;

use Khill\Lavacharts\Lavacharts;
use Khill\Lavacharts\Tests\TestCase;
use Khill\Lavacharts\Configs\boxStyle;
use Khill\Lavacharts\Configs\gradient;

class BoxStyleTest extends TestCase {
/*
    public function setUp()
    {
        parent::setUp();
    }
*/
    public function testIfInstanceOfBoxStyle()
    {
        $this->assertInstanceOf('Khill\Lavacharts\Configs\boxStyle', new boxStyle());
    }

    public function testConstructorNoAssignmentsDefaults()
    {
        $boxStyle = new boxStyle();

        $this->assertRegExp('/#([a-fA-F0-9]){3}(([a-fA-F0-9]){3})?\b/', $boxStyle->startColor);
        $this->assertRegExp('/#([a-fA-F0-9]){3}(([a-fA-F0-9]){3})?\b/', $boxStyle->finishColor);
        $this->assertEquals('0%',   $boxStyle->x1);
        $this->assertEquals('0%',   $boxStyle->y1);
        $this->assertEquals('100%', $boxStyle->x2);
        $this->assertEquals('100%', $boxStyle->y2);
    }

    public function testConstructorValuesAssignment()
    {
        $boxStyle = new boxStyle(array(
            'startColor'  => '#F0F0F0',
            'finishColor' => '#3D3D3D',
            'x1'          => '0%',
            'y1'          => '0%',
            'x2'          => '100%',
            'y2'          => '100%'
        ));

        $this->assertEquals('#F0F0F0', $boxStyle->startColor);
        $this->assertEquals('#3D3D3D', $boxStyle->finishColor);
        $this->assertEquals('0%',      $boxStyle->x1);
        $this->assertEquals('0%',      $boxStyle->y1);
        $this->assertEquals('100%',    $boxStyle->x2);
        $this->assertEquals('100%',    $boxStyle->y2);
    }

    public function testInvalidPropertiesKey()
    {
        $boxStyle = new boxStyle(array('tacos' => '#F8C3B0'));

        $this->assertTrue(Lavacharts::hasErrors());
    }

    /**
     * @dataProvider badParamsProvider
     */
    public function testStartColorWithBadParams($badVals)
    {
        $boxStyle = new boxStyle();
        $boxStyle->startColor($badVals);

        $this->assertTrue(Lavacharts::hasErrors());
    }

    /**
     * @dataProvider badParamsProvider
     */
    public function testFinishColorWithBadParams($badVals)
    {
        $boxStyle = new boxStyle();
        $boxStyle->finishColor($badVals);

        $this->assertTrue(Lavacharts::hasErrors());
    }

    /**
     * @dataProvider badParamsProvider
     */
    public function testX1ColorWithBadParams($badVals)
    {
        $boxStyle = new boxStyle();
        $boxStyle->x1($badVals);

        $this->assertTrue(Lavacharts::hasErrors());
    }

    /**
     * @dataProvider badParamsProvider
     */
    public function testY1ColorWithBadParams($badVals)
    {
        $boxStyle = new boxStyle();
        $boxStyle->y1($badVals);

        $this->assertTrue(Lavacharts::hasErrors());
    }

    /**
     * @dataProvider badParamsProvider
     */
    public function testX2ColorWithBadParams($badVals)
    {
        $boxStyle = new boxStyle();
        $boxStyle->x2($badVals);

        $this->assertTrue(Lavacharts::hasErrors());
    }

    /**
     * @dataProvider badParamsProvider
     */
    public function testY2ColorWithBadParams($badVals)
    {
        $boxStyle = new boxStyle();
        $boxStyle->y2($badVals);

        $this->assertTrue(Lavacharts::hasErrors());
    }

    public function badParamsProvider()
    {
        return array(
            array(123),
            array(123.456),
            array(array()),
            array(new \stdClass()),
            array(true),
            array(null)
        );
    }

}

