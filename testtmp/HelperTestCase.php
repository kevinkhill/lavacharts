<?php namespace Khill\Lavacharts;

use Khill\Lavacharts\Configs as C;

class HelperTestCase extends LavaTestCase {

    public function setUp()
    {
        $this->backgroundColor = new C\backgroundColor();
        $this->chartArea = new C\chartArea();
        $this->colorAxis = new C\colorAxis();
        $this->hAxis = new C\hAxis();
        $this->jsDate = new C\jsDate();
        $this->legend = new C\legend();
        $this->magnifyingGlass = new C\magnifyingGlass();
        $this->textStyle = new C\textStyle();
        $this->tooltip = new C\tooltip();
        $this->sizeAxis = new C\sizeAxis();
        $this->slice = new C\slice();
        $this->vAxis = new C\vAxis();

        $this->obj = new \stdClass();
    }

}
