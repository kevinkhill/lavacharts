<?php

class JsDateTest extends TestCase {

    public function setUp()
    {
        parent::setUp();

        $this->cfgObj = new Khill\Lavacharts\Configs\jsDate();
    }

    public function testIfJsDateClassIsTrue()
    {
        $this->assertTrue(get_class($this->cfgObj) === 'jsDate');
    }

    public function testSomethingIsTrue()
    {
        $this->assertTrue(true);
    }

}
