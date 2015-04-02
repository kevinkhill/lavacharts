<?php namespace Khill\Lavacharts\Tests;

use \Khill\Lavacharts\Lavacharts;
use \Mockery as m;

class LavachartsTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->lc = new Lavacharts;

        $this->mdt = m::mock('\Khill\Lavacharts\Configs\DataTable')
                      ->shouldReceive('toJson')
                      ->atMost(1)
                      ->shouldReceive('hasFormats')
                      ->atLeast(1)
                      ->getMock();

    }

    public function testIfInstanceOfVolcano()
    {
        $this->assertInstanceOf('\\Khill\\Lavacharts\\Volcano', $this->lc->volcano);
    }

    public function testCreateDataTableViaAlias()
    {
        $this->assertInstanceOf('\\Khill\\Lavacharts\\Configs\\DataTable', $this->lc->DataTable());
    }

    /**
     * @dataProvider chartTypeProvider
     */
    public function testCreateChartsViaAlias($chartType)
    {
        $this->assertInstanceOf('\\Khill\\Lavacharts\\Charts\\'.$chartType, $this->lc->$chartType('testchart'));
    }

    /**
     * @dataProvider configObjectProvider
     */
    public function testCreateConfigObjectsViaAliasNoParams($configType)
    {
        $this->assertInstanceOf('\\Khill\\Lavacharts\\Configs\\'.$configType, $this->lc->$configType());
    }

    public function testCreateConfigObjectViaAliasWithParam()
    {
        $params = array(
            'fontSize' => 4,
            'fontName' => 'Arial'
        );

        $this->assertInstanceOf('\\Khill\\Lavacharts\\Configs\TextStyle', $this->lc->TextStyle($params));
    }

    /**
     * @dataProvider chartTypeProvider
     * @depends testCreateDataTableViaAlias
     */
    public function testRenderChartAliases($chartType)
    {
        $chart = $this->lc->$chartType('test');
        $chart->datatable($this->mdt);

        $render = 'render'.$chartType;

        $this->assertTrue(is_string($this->lc->$render('test', 'test-div')));
    }

    /**
     * @depends testCreateDataTableViaAlias
     */
    public function testDirectRenderChart()
    {
        $chart = $this->lc->LineChart('test');
        $chart->datatable($this->mdt);

        $this->assertTrue(is_string($this->lc->render('LineChart', 'test', 'test-div')));
    }

    /**
     * @depends testCreateDataTableViaAlias
     */
    public function testDirectRenderChartWithDivNoDimensions()
    {
        $chart = $this->lc->LineChart('test');
        $chart->datatable($this->mdt);

        $this->assertTrue(is_string($this->lc->render('LineChart', 'test', 'test-div', true)));
    }

    /**
     * @depends testCreateDataTableViaAlias
     */
    public function testDirectRenderChartWithDivAndDimensions()
    {
        $chart = $this->lc->LineChart('test');
        $chart->datatable($this->mdt);

        $dims = array(
            'height' => 200,
            'width' => 200
        );

        $this->assertTrue(is_string($this->lc->render('LineChart', 'test', 'test-div', $dims)));
    }

    /**
     * @depends testCreateDataTableViaAlias
     * @expectedException Khill\Lavacharts\Exceptions\InvalidDivDimensions
     */
    public function testDirectRenderChartWithDivAndBadDimensionKeys()
    {
        $chart = $this->lc->LineChart('test');
        $chart->datatable($this->mdt);

        $dims = array(
            'heiXght' => 200,
            'wZidth' => 200
        );

        $this->assertTrue(is_string($this->lc->render('LineChart', 'test', 'test-div', $dims)));
    }

    /**
     * @depends testCreateDataTableViaAlias
     * @expectedException Khill\Lavacharts\Exceptions\InvalidDivDimensions
     */
    public function testDirectRenderChartWithDivAndBadDimensionType()
    {
        $chart = $this->lc->LineChart('test');
        $chart->datatable($this->mdt);

        $this->assertTrue(is_string($this->lc->render('LineChart', 'test', 'test-div', 'TacosTacosTacos')));
    }

    /**
     * @depends testCreateDataTableViaAlias
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testDirectRenderChartWithDivAndDimensionsWithBadValues()
    {
        $chart = $this->lc->LineChart('test');
        $chart->datatable($this->mdt);

        $dims = array(
            'height' => 4.6,
            'width' => 'hotdogs'
        );

        $this->assertTrue(is_string($this->lc->render('LineChart', 'test', 'test-div', $dims)));
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidLavaObject
     */
    public function testInvalidLavaObject()
    {
        $this->lc->PizzaChart();
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidLavaObject
     */
    public function testRenderAliasWithInvalidLavaObject()
    {
        $this->lc->renderTacoChart();
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidChartLabel
     */
    public function testCreateChartWithMissingLabel()
    {
        $this->lc->LineChart();
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidChartLabel
     */
    public function testCreateChartWithInvalidLabel()
    {
        $this->lc->LineChart(5);
    }

    public function testJsapiMethodWithCoreJsTracking()
    {
        $javascript  = '<script type="text/javascript" src="//www.google.com/jsapi"></script>';
        $javascript .= '<script type="text/javascript">';
        $javascript .= 'function onResize(a,e){return window.onresize=function(){clearTimeout(e),e=setTimeout(a,100)},a}var lava=lava||{get:null,event:null,charts:{},registeredCharts:[]};lava.get=function(a){var e,r=Object.keys(lava.charts);return"string"!=typeof a?(console.error("[Lavacharts] The input for lava.get() must be a string."),!1):Array.isArray(r)?void r.some(function(r){return"undefined"!=typeof lava.charts[r][a]?(e=lava.charts[r][a].chart,!0):!1}):!1},lava.event=function(a,e,r){return r(a,e)},lava.register=function(a,e){this.registeredCharts.push(a+":"+e)},window.onload=function(){onResize(function(){for(var a=0;a<lava.registeredCharts.length;a++){var e=lava.registeredCharts[a].split(":");lava.charts[e[0]][e[1]].draw()}})};';
        $javascript .= '</script>';

        $this->assertEquals($javascript, $this->lc->jsapi());

        $this->assertTrue($this->lc->jsFactory->coreJsRendered());
    }

    public function chartTypeProvider()
    {
        foreach (Lavacharts::chartClasses as $chart) {
            $charts[] = array($chart);
        }

        return $charts;
    }

    public function configObjectProvider()
    {
        foreach (Lavacharts::configClasses as $config) {
            $configs[] = array($config);
        }

        return $configs;
    }
}
