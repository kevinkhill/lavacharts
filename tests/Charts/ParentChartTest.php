<?php

namespace Khill\Lavacharts\Tests\Charts;

use \Khill\Lavacharts\Tests\ProvidersTestCase;

class ChartAndTraitsTest extends ProvidersTestCase
{
    public $mockChart;

    public function setUp()
    {
        parent::setUp();

        $label = \Mockery::mock('\Khill\Lavacharts\Values\Label', ['TestChart'])->makePartial();

        $this->mockChart = new MockChart($label, $this->partialDataTable);
    }

    public function testLabelAssignedViaConstructor()
    {
        $this->assertEquals('TestChart', (string) $this->mockChart->getLabel());
    }

    public function testDataTable()
    {
        $this->mockChart->datatable($this->partialDataTable);

        $this->assertInstanceOf('\Khill\Lavacharts\DataTables\DataTable', $this->mockChart->getDataTable());
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
