<?php

namespace Khill\Lavacharts\Tests\Configs;

use \Khill\Lavacharts\Configs\Gridlines;
use \Khill\Lavacharts\Tests\ProvidersTestCase;

class GridlinesTest extends ProvidersTestCase
{
    public $Gridlines;

    public function setUp()
    {
        parent::setUp();

        $this->Gridlines = new Gridlines;
    }

    public function testConstructorValuesAssignment()
    {
        $gradient = new Gridlines([
            'color' => '#F0F0F0',
            'count' => 5
        ]);

        $this->assertEquals('#F0F0F0', $gradient->color);
        $this->assertEquals(5, $gradient->count);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     */
    public function testConstructorWithInvalidPropertiesKey()
    {
        new Gridlines(['tacos' => '#F8C3B0']);
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testColorWithBadParams($badVals)
    {
        $this->Gridlines->color($badVals);
    }

    public function testCountWithAutoValue()
    {
        $this->Gridlines->count(-1);
        $this->assertEquals(-1, $this->Gridlines->count);
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testCountWithBadParams($badVals)
    {
        $this->Gridlines->count($badVals);
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testCountWithOutOfRangeValues()
    {
        $this->Gridlines->count(1);
        $this->Gridlines->count(0);
        $this->Gridlines->count(-2);
    }

    public function testUnitsWithValidKeysAndValues()
    {
        $this->Gridlines->units([
            'years'        => 'Y',
            'months'       => 'M',
            'days'         => 'D',
            'hours'        => 'H',
            'minutes'      => 'm',
            'seconds'      => 's',
            'milliseconds' => 'ms'
        ]);

        $this->assertEquals('Y', $this->Gridlines->units['years']);
        $this->assertEquals('M', $this->Gridlines->units['months']);
        $this->assertEquals('D', $this->Gridlines->units['days']);
        $this->assertEquals('H', $this->Gridlines->units['hours']);
        $this->assertEquals('m', $this->Gridlines->units['minutes']);
        $this->assertEquals('s', $this->Gridlines->units['seconds']);
        $this->assertEquals('ms', $this->Gridlines->units['milliseconds']);
    }

    /**
     * @dataProvider nonArrayProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testUnitsWithBadTypes($badVals)
    {
        $this->Gridlines->units($badVals);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testUnitsWithBadKeys()
    {
        $this->Gridlines->units(['Tacos' => 'Good']);
    }

    /**
     * @depends testUnitsWithValidKeysAndValues
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testUnitsWithBadValues($notStrings)
    {
        foreach ([
             'years',
             'months',
             'days',
             'hours',
             'minutes',
             'seconds',
             'milliseconds'
         ] as $unit) {
            $this->Gridlines->units([$unit => $notStrings]);
        }
    }
}
