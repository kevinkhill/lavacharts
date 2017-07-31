<?php

namespace Khill\Lavacharts\Tests\Charts;

use Khill\Lavacharts\Charts\LineChart;
use Khill\Lavacharts\DataTables\DataTable;
use Khill\Lavacharts\Tests\ProvidersTestCase;

class ChartTestCase extends ProvidersTestCase
{
    public function makeChart($type, $options = [])
    {
        return new $type(
            $this->getMockLabel('TestChart'),
            $this->getMockDataTable(),
            $options
        );
    }

    /**
     * @dataProvider chartTypeProvider
     * @param string $chartType
     */
    public function testInstanceCreation($chartType)
    {
        $chartFQN = "Khill\\Lavacharts\\Charts\\".$chartType;

        $chart = new $chartFQN(
            $this->getMockLabel('TestChart'),
            $this->getMockDataTable()
        );

        $this->assertEquals('TestChart', $chart->getLabel());
        $this->assertEquals($chartType, $chart->getType());
        $this->assertInstanceOf(DataTable::class, $chart->getDataTable());
    }

    /**
     * @depends testInstanceCreation
     */
    public function testSettingOptionsWithConstructor()
    {
        $chart = $this->makeLineChart(
            ['colors' => ['red', 'green']]
        );

        $options = $this->inspect($chart, 'options');

        $this->assertTrue(is_array($options));
        $this->assertArrayHasKey('colors', $options);
        $this->assertEquals(['red', 'green'], $options['colors']);
    }

    /**
     * @depends testSettingOptionsWithConstructor
     */
    public function testGetOptions()
    {
        $chart = $this->makeLineChart(
            ['colors' => ['red', 'green']]
        );

        $options = $chart->getOptions();

        $this->assertTrue(is_array($options));
        $this->assertEquals(['red', 'green'], $options['colors']);
    }

    /**
     * @depends testGetOptions
     */
    public function testSettingOptionsViaMagicMethod()
    {
        $chart = $this->makeLineChart();

        $chart->legend(['position' => 'out']);

        $options = $chart->getOptions();

        $this->assertArrayHasKey('legend', $options);
        $this->assertArrayHasKey('position', $options['legend']);
        $this->assertEquals('out', $options['legend']['position']);
    }

    /**
     * @depends testGetOptions
     */
    public function testSettingStringValueOptionViaMagicMethod()
    {
        $chart = $this->makeLineChart();

        $chart->title('Charts!');

        $options = $chart->getOptions();

        $this->assertEquals('Charts!', $options['title']);
    }

    /**
     * @depends testGetOptions
     */
    public function testSetOptions()
    {
        $expected = [
            'title' => 'My Cool Chart',
            'width' => 1024,
            'height' => 768
        ];

        $chart = $this->makeLineChart();
        $chart->setOptions($expected);

        $options = $chart->getOptions();

        $this->assertTrue(is_array($options));
        $this->assertEquals('My Cool Chart', $options['title']);
        $this->assertEquals(1024, $options['width']);
        $this->assertEquals(768, $options['height']);
    }

    /**
     * @depends testSetOptions
     * @depends testGetOptions
     */
    public function testMergeOptions()
    {
        $expected = [
            'title' => 'My Cool Chart'
        ];

        $chart = $this->makeLineChart();

        $chart->setOptions($expected);

        $chart->mergeOptions(['width' => 1024]);

        $options = $chart->getOptions();

        $this->assertEquals('My Cool Chart', $options['title']);
        $this->assertEquals(1024, $options['width']);
    }

    /**
     * @depends testSetOptions
     * @depends testGetOptions
     */
    public function testCustomize()
    {
        $expected = [
            'title' => 'My Cool Chart',
            'width' => 1024,
            'height' => 768
        ];

        $chart = $this->makeLineChart();
        $chart->customize($expected);

        $options = $chart->getOptions();

        $this->assertEquals('My Cool Chart', $options['title']);
        $this->assertEquals(1024, $options['width']);
        $this->assertEquals(768, $options['height']);
    }

    /**
     * @depends testSettingOptionsViaMagicMethod
     */
    public function testOptionsToJson()
    {
        $chart = $this->makeLineChart();

        $chart->title('My Cool Chart');
        $chart->width(1024);
        $chart->height(768);

        $expected = '{"type":"LineChart","label":"TestChart","options":{"title":"My Cool Chart","width":1024,"height":768},"datatable":{"cols":[],"rows":[]},"element_id":""}';

        $this->assertEquals($expected, $chart->getOptions()->toJson());
    }
}
