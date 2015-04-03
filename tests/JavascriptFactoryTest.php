<?php namespace Khill\Lavacharts\Tests;

use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Khill\Lavacharts\Javascript\JavascriptFactory;
use \Mockery as m;

class JavascriptFactoryTest extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->jsf = new JavascriptFactory;

        $this->mlc = m::mock('Khill\Lavacharts\Charts\LineChart', array('TestChart'))->makePartial();
        $this->mdt = m::mock('Khill\Lavacharts\Configs\DataTable')->makePartial();

        $this->mdt->addColumn('number')
                  ->addColumn('number')
                  ->addColumn('number')
                  ->addRow(array(10101, 12345, 67890));
    }

    public function testGetChartJsOutput()
    {
        $this->mlc->datatable($this->mdt);

        $js = $this->jsf->getChartJs($this->mlc, 'div_id');

        $this->assertTrue(strpos($js, 'div_id') > 0 ? true : false);
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\DataTableNotFound
     */
    public function testGetChartJsWithMissingDataTable()
    {
        $this->jsf->getChartJs($this->mlc);
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidElementId
     */
    public function testGetChartJsWithBadElementIdTypes($badTypes)
    {
        $this->mlc->datatable($this->mdt);

        $js = $this->jsf->getChartJs($this->mlc, $badTypes);
    }

    public function testGetCoreJsFromFile()
    {
        $javascript  = '<script type="text/javascript" src="//www.google.com/jsapi"></script>';
        $javascript .= '<script type="text/javascript">';
        $javascript .= 'function onResize(a,e){return window.onresize=function(){clearTimeout(e),e=setTimeout(a,100)},a}var lava=lava||{get:null,event:null,charts:{},registeredCharts:[]};lava.get=function(a){var e,r=Object.keys(lava.charts);return"string"!=typeof a?(console.error("[Lavacharts] The input for lava.get() must be a string."),!1):Array.isArray(r)?void r.some(function(r){return"undefined"!=typeof lava.charts[r][a]?(e=lava.charts[r][a].chart,!0):!1}):!1},lava.event=function(a,e,r){return r(a,e)},lava.register=function(a,e){this.registeredCharts.push(a+":"+e)},window.onload=function(){onResize(function(){for(var a=0;a<lava.registeredCharts.length;a++){var e=lava.registeredCharts[a].split(":");lava.charts[e[0]][e[1]].draw()}})};';
        $javascript .= '</script>';

        $this->assertEquals($javascript, $this->jsf->getCoreJs());
    }
}
