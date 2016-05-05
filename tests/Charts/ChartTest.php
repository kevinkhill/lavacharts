<?php

namespace Khill\Lavacharts\Tests\Charts;

use Khill\Lavacharts\Tests\ProvidersTestCase;

class ChartTest extends ProvidersTestCase
{
    public $mockChart;

    public function setUp()
    {
        parent::setUp();

        $label = $this->getMockLabel('TestChart');

        //$this->mockChart = new MockChart($label, $this->partialDataTable);
    }

    /**
     * @dataProvider chartTypeProvider
     */
    public function testInstanceCreation($chartType)
    {
        $chartFQN = "Khill\\Lavacharts\\Charts\\".$chartType;

        $chart = new $chartFQN(
            $this->getMockLabel('TestChart'),
            $this->getMockDataTable()
        );

        $this->assertEquals('TestChart', $chart->getLabelStr());
        $this->assertEquals($chartType, $chart->getType());
        $this->assertInstanceOf(DATATABLE_NS.'DataTable', $chart->getDataTable());
    }

    public function testTooltipWithValidValues()
    {
        $this->mockChart->tooltip([]);

        $this->assertInstanceOf('\Khill\Lavacharts\Configs\Tooltip', $this->mockChart->tooltip);
    }

    /**
     * @dataProvider nonArrayProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testTooltipWithBadTypes($badTypes)
    {
        $this->mockChart->tooltip($badTypes);
    }

    public function testWidthWithValidValue()
    {
        $this->mockChart->width(800);
        $this->assertEquals(800, $this->mockChart->width);
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testWidthWithBadTypes($badTypes)
    {
        $this->mockChart->width($badTypes);
    }

    /**
     * @depends testTitleWithValidValue
     * @depends testWidthWithValidValue
     * @depends testHeightWithValidValue
     */
    public function testSetOptionsWithArrayOfValidOptions()
    {
        $expected = [
            'title' => 'My Cool Chart',
            'width' => 1024,
            'height' => 768
        ];

        $this->mockChart->setOptions($expected);

        $this->assertEquals($expected, $this->mockChart->getOptions()->getValues());
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     */
    public function testSetOptionsWithArrayOfBadOptions()
    {
        $this->mockChart->setOptions([
            'tibtle' => 'My Cool Chart',
            'widmth' => 1024,
            'heaight' => 768
        ]);
    }

    /**
     * @dataProvider nonArrayProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testSetOptionsWithBadTypes($badTypes)
    {
        $this->mockChart->setOptions($badTypes);
    }

    public function testGettingNonExistentOptionValue()
    {
        $this->assertNull($this->mockChart->bananas);
    }

    /**
     * @depends testTitleWithValidValue
     * @depends testWidthWithValidValue
     * @depends testHeightWithValidValue
     */
    public function testOptionsToJson()
    {
        $this->mockChart->title('My Cool Chart');
        $this->mockChart->width(1024);
        $this->mockChart->height(768);

        $expected = '{"title":"My Cool Chart","width":1024,"height":768}';

        $this->assertEquals($expected, json_encode($this->mockChart));
    }
}
