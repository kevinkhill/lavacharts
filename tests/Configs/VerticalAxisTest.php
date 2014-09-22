<?php namespace Lavacharts\Tests\Configs;

use \Lavacharts\Tests\ProvidersTestCase;
use \Lavacharts\Configs\VerticalAxis;

class VerticalAxisTest extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->va = new VerticalAxis(array());

        $this->mockTextStyle = $this->getMock(
            '\Lavacharts\Configs\TextStyle',
            array('__construct')
        );
    }

    public function testConstructorValuesAssignment()
    {
        $mockTextStyle = $this->getMock(
            '\Lavacharts\Configs\TextStyle',
            array('__construct')
        );

        $a = new VerticalAxis(array(
            'baselineColor'  => '#F4D4E7',
            'format'         => '999.99',
            'logScale'       => true,
            'textPosition'   => 'in',
            'title'          => 'Taco Graph',
            'titleTextStyle' => $this->mockTextStyle,
            'textStyle'      => $this->mockTextStyle,
            'viewWindowMode' => 'explicit'
        ));

        $this->assertEquals('#F4D4E7', $a->baselineColor);
        $this->assertEquals('999.99', $a->format);
        $this->assertTrue($a->logScale);
        $this->assertEquals('in', $a->textPosition);
        $this->assertTrue(is_array($a->textStyle));
        $this->assertEquals('Taco Graph', $a->title);
        $this->assertTrue(is_array($a->titleTextStyle));
        $this->assertEquals('explicit', $a->viewWindowMode);
    }

    /**
     * @expectedException Lavacharts\Exceptions\InvalidConfigProperty
     */
    public function testConstructorWithInvalidPropertiesKey()
    {
        new VerticalAxis(array('Jellybeans' => array()));
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testBaselineColorWithBadParams($badVals)
    {
        $this->va->baselineColor($badVals);
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testFormatWithBadParams($badVals)
    {
        $this->va->format($badVals);
    }

    /**
     * @dataProvider nonBoolProvider
     * @expectedException Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testLogScaleWithBadParams($badVals)
    {
        $this->va->logScale($badVals);
    }

    public function testTextPositionWithValidValues()
    {
        $this->va->textPosition('out');
        $this->assertEquals('out', $this->va->textPosition);

        $this->va->textPosition('in');
        $this->assertEquals('in', $this->va->textPosition);

        $this->va->textPosition('none');
        $this->assertEquals('none', $this->va->textPosition);
    }

    /**
     * @expectedException Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testTextPositionWithBadValue()
    {
        $this->va->textPosition('underneath');
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testTextPositionWithBadParams($badParams)
    {
        $this->va->textPosition($badParams);
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testTextStyleWithBadParams()
    {
        $this->va->textStyle('not a TextStyle object');
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testTitleWithBadParams($badParams)
    {
        $this->va->title($badParams);
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testTitleTextStyleWithBadParams()
    {
        $this->va->titleTextStyle('not a TextStyle object');
    }

    public function testViewWindowModeWithValidValues()
    {
        $this->va->viewWindowMode('pretty');
        $this->assertEquals('pretty', $this->va->viewWindowMode);

        $this->va->viewWindowMode('maximized');
        $this->assertEquals('maximized', $this->va->viewWindowMode);

        $this->va->viewWindowMode('explicit');
        $this->assertEquals('explicit', $this->va->viewWindowMode);
    }

    public function testViewWindowModeWithBadValueAndViewWindowIsNull()
    {
        $this->va->viewWindowMode('bricks');

        $this->assertEquals('pretty', $this->va->viewWindowMode);
    }

    public function testViewWindowModeWithBadValueAndViewWindowIsSet()
    {
        $this->va->viewWindow([
            'min' => 10,
            'max' => 100
        ]);

        $this->va->viewWindowMode('samsung');

        $this->assertEquals('explicit', $this->va->viewWindowMode);
    }

    /**
     * @dataProvider nonStringProvider
     */
    public function testViewWindowModeWithBadParams($badParams)
    {
        $this->va->viewWindowMode($badParams);

        $this->assertEquals('pretty', $this->va->viewWindowMode);
    }

}
