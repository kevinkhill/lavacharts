<?php namespace Khill\Lavacharts\Tests;

use Khill\Lavacharts\Configs\jsDate;

class JsDateTest extends \Orchestra\Testbench\TestCase {

    public function setUp()
    {
        parent::setUp();

        $this->cfgObj = new jsDate();
    }

    public function testIfInstanceOfJsDate()
    {
        $this->assertInstanceOf('Khill\Lavacharts\Configs\jsDate', $this->cfgObj);
    }

}
