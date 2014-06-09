<?php namespace Khill\Lavacharts\Tests\Helpers;

use Khill\Lavacharts\Tests\TestCase;
use Khill\Lavacharts\Configs as Config;

class HelperTestCase extends TestCase {

    public function setUp()
    {
        parent::setUp();

        $this->annotation      = new Config\annotation();
        $this->backgroundColor = new Config\backgroundColor();
        $this->boxStyle        = new Config\boxStyle();
        $this->chartArea       = new Config\chartArea();
        $this->colorAxis       = new Config\colorAxis();
        $this->gradient        = new Config\gradient();
        $this->hAxis           = new Config\hAxis();
        $this->jsDate          = new Config\jsDate();
        $this->legend          = new Config\legend();
        $this->magnifyingGlass = new Config\magnifyingGlass();
        $this->textStyle       = new Config\textStyle();
        $this->tooltip         = new Config\tooltip();
        $this->sizeAxis        = new Config\sizeAxis();
        $this->series          = new Config\series();
        $this->slice           = new Config\slice();
        $this->vAxis           = new Config\vAxis();
        $this->obj             = new \stdClass();
    }

}
