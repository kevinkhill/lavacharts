<?php namespace Khill\Lavacharts\Tests\Configs;

use Khill\Lavacharts\Lavacharts;
use Khill\Lavacharts\Tests\TestCase;
use Khill\Lavacharts\Configs\boxStyle;
use Khill\Lavacharts\Configs\gradient;

class BoxStyleTest extends TestCase {

    public function setUp()
    {
        parent::setUp();

        $this->bs = new boxStyle();
    }

    public function testIfInstanceOfBoxStyle()
    {
        $this->assertInstanceOf('Khill\Lavacharts\Configs\boxStyle', $this->bs);
    }

    public function testConstructorDefaults()
    {
        $this->assertRegExp('/#([a-fA-F0-9]){3}(([a-fA-F0-9]){3})?\b/', $this->bs->stroke);
        $this->assertEquals(null, $this->bs->strokeWidth);
        $this->assertEquals(null, $this->bs->rx);
        $this->assertEquals(null, $this->bs->ry);
        $this->assertEquals(null, $this->bs->gradient);
    }

    public function testConstructorValuesAssignment()
    {
        $boxStyle = new boxStyle(array(
            'stroke'      => '#5B5B5B',
            'strokeWidth' => '5',
            'rx'          => '10',
            'ry'          => '10',
            'gradient'    => new gradient()
        ));

        $this->assertEquals('#5B5B5B', $boxStyle->stroke);
        $this->assertEquals('5',       $boxStyle->strokeWidth);
        $this->assertEquals('10',      $boxStyle->rx);
        $this->assertEquals('10',      $boxStyle->ry);
        $this->assertInstanceOf('Khill\Lavacharts\Configs\gradient', $boxStyle->gradient);
    }

    public function testConstructorWithInvalidPropertiesKey()
    {
        $boxStyle = new boxStyle(array('lasagna' => '50%'));

        $this->assertTrue(Lavacharts::hasErrors());
    }

    /**
     * @dataProvider numericParamsProvider
     */
    public function testStokeWidthWithNumericParams($badVals)
    {
        $this->bs->strokeWidth($badVals);

        $this->assertTrue(Lavacharts::hasErrors());
    }

    /**
     * @dataProvider numericParamsProvider
     */
    public function testRxWithNumericParams($badVals)
    {
        $this->bs->rx($badVals);

        $this->assertTrue(Lavacharts::hasErrors());
    }

    /**
     * @dataProvider numericParamsProvider
     */
    public function testRyWithNumericParams($badVals)
    {
        $this->bs->ry($badVals);

        $this->assertTrue(Lavacharts::hasErrors());
    }

    /**
     * @dataProvider badParamsProvider
     */
    public function testStrokeWithBadParams($badVals)
    {
        $this->bs->stroke($badVals);

        $this->assertTrue(Lavacharts::hasErrors());
    }

    /**
     * @dataProvider badParamsProvider
     */
    public function testStokeWidthWithBadParams($badVals)
    {
        $this->bs->strokeWidth($badVals);

        $this->assertTrue(Lavacharts::hasErrors());
    }

    /**
     * @dataProvider badParamsProvider
     */
    public function testRxWithBadParams($badVals)
    {
        $this->bs->rx($badVals);

        $this->assertTrue(Lavacharts::hasErrors());
    }

    /**
     * @dataProvider badParamsProvider
     */
    public function testRyWithBadParams($badVals)
    {
        $this->bs->ry($badVals);

        $this->assertTrue(Lavacharts::hasErrors());
    }


    public function badParamsProvider()
    {
        return array(
            array(array()),
            array(new \stdClass()),
            array(true),
            array(null)
        );
    }

    public function numericParamsProvider()
    {
        return array(
            array(123),
            array('123')
        );
    }

}
