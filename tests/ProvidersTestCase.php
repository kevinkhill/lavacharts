<?php namespace Khill\Lavacharts\Tests;

use Khill\Lavacharts\Lavacharts;

abstract class ProvidersTestCase extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function nonIntOrPercentProvider()
    {
        return array(
            array(3.2),
            array(true),
            array(false),
            array(array()),
            array(new \stdClass)
        );
    }

    public function nonCarbonOrDateOrEmptyArrayProvider()
    {
        return array(
            array('cheese'),
            array(9),
            array(1.2),
            array(true),
            array(false),
            array(new \stdClass())
        );
    }

    public function nonConfigObjectProvider()
    {
        return array(
            array('stringy'),
            array(9),
            array(1.2),
            array(true),
            array(false),
            array(array()),
            array(new \stdClass())
        );
    }

    public function nonStringProvider()
    {
        return array(
            array(9),
            array(1.2),
            array(true),
            array(false),
            array(array()),
            array(new \stdClass())
        );
    }

    public function nonBoolProvider()
    {
        return array(
            array('Imastring'),
            array(9),
            array(1.2),
            array(array()),
            array(new \stdClass())
        );
    }

    public function nonIntProvider()
    {
        return array(
            array('Imastring'),
            array(1.2),
            array(true),
            array(false),
            array(array()),
            array(new \stdClass())
        );
    }

    public function nonFloatProvider()
    {
        return array(
            array('Imastring'),
            array(9),
            array(true),
            array(false),
            array(array()),
            array(new \stdClass())
        );
    }

    public function nonNumericProvider()
    {
        return array(
            array('Imastring'),
            array(true),
            array(false),
            array(array()),
            array(new \stdClass())
        );
    }

    public function nonArrayProvider()
    {
        return array(
            array('Imastring'),
            array(9),
            array(1.2),
            array(true),
            array(false),
            array(new \stdClass())
        );
    }
}
