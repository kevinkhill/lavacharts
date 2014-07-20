<?php namespace Khill\Lavacharts\Tests\Charts;

class ChartTestCase extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function nonStringProvider()
    {
        return array(
            array(1),
            array(1.2),
            array(true),
            array(false),
            array(array()),
            array(new \stdClass())
        );
    }

    public function nonBooleanProvider()
    {
        return array(
            array('Imastring'),
            array(1),
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
            array(1),
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
            array(1),
            array(1.2),
            array(true),
            array(false),
            array(new \stdClass())
        );
    }
}
