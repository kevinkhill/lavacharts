<?php namespace Khill\Lavacharts\Tests;

use Khill\Lavacharts\Configs as Config;

class TestCase extends Orchestra\Testbench\TestCase {

    protected function getPackageProviders()
    {
        return array('Khill\Lavacharts\LavachartsServiceProvider');
    }

    protected function getPackageAliases()
    {
        return array('Lava' => 'Khill\Lavacharts\Facades\Lavacharts');
    }
/*
    public function setUp()
    {
        parent::setUp();

        $this->backgroundColor = new Config\backgroundColor();
        $this->chartArea = new Config\chartArea();
        $this->colorAxis = new Config\colorAxis();
        $this->hAxis = new Config\hAxis();
        $this->jsDate = new Config\jsDate();
        $this->legend = new Config\legend();
        $this->magnifyingGlass = new Config\magnifyingGlass();
        $this->textStyle = new Config\textStyle();
        $this->tooltip = new Config\tooltip();
        $this->sizeAxis = new Config\sizeAxis();
        $this->slice = new Config\slice();
        $this->vAxis = new Config\vAxis();

        $this->obj = new \stdClass();
    }
*/
}
