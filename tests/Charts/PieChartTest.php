<?php namespace Khill\Lavacharts\Tests\Charts;

use Khill\Lavacharts\Tests\DataProviders;
use Khill\Lavacharts\Charts\PieChart;
use Mockery as m;

class PieChartTest extends DataProviders
{
    public function setUp()
    {
        parent::setUp();

        $this->pc = new PieChart('MyTestChart');
    }

    public function testInstanceOfPieChartWithType()
    {
    	$this->assertInstanceOf('Khill\Lavacharts\Charts\PieChart', $this->pc);
    }

    public function testTypePieChart()
    {
    	$this->assertEquals('PieChart', $this->pc->type);
    }

    public function testLabelAssignedViaConstructor()
    {
    	$this->assertEquals('MyTestChart', $this->pc->label);
    }

    public function testIs3D()
    {
        $this->pc->is3D(true);

        $this->assertTrue($this->pc->options['is3D']);
    }

    /**
     * @dataProvider nonBooleanProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testIs3DWithBadType($badTypes)
    {
        $this->pc->is3D($badTypes);
    }

    public function testSlices()
    {
         $textStyleVals = array(
            'color'    => 'blue',
            'fontName' => 'Arial',
            'fontSize' => 16
        );

        $mockTextStyle = m::mock('Khill\Lavacharts\Configs\TextStyle');
        //$mockTextStyle->shouldReceive('getValues')->once()->andReturn($textStyleVals);

        $sliceVals = array(
            'color'     => 'blue',
            'offset'    => 'Arial',
            'textStyle' => $mockTextStyle
        );

        $mockSlice1 = m::mock('Khill\Lavacharts\Configs\Slice');
        $mockSlice1->shouldReceive('getValues')->once()->andReturn($sliceVals);

        $mockSlice2 = m::mock('Khill\Lavacharts\Configs\Slice');
        $mockSlice2->shouldReceive('getValues')->once()->andReturn($sliceVals);

        $this->pc->slices(array($mockSlice1, $mockSlice2));

        $this->assertEquals($sliceVals, $this->pc->options['slices'][0]);
        $this->assertEquals($sliceVals, $this->pc->options['slices'][1]);
    }

    /**
     * @dataProvider nonArrayProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testSlicesWithBadTypes($badTypes)
    {
        $this->pc->slices($badTypes);
    }

    public function testPieSliceBorderColorValidValues()
    {
        $this->pc->pieSliceBorderColor('green');

        $this->assertEquals('green', $this->pc->options['pieSliceBorderColor']);
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testPieSliceBorderColorWithBadTypes($badTypes)
    {
        $this->pc->pieSliceBorderColor($badTypes);
    }

    public function testPieSliceTextWithValidValues()
    {
        $this->pc->pieSliceText('percentage');
        $this->assertEquals('percentage', $this->pc->options['pieSliceText']);

        $this->pc->pieSliceText('value');
        $this->assertEquals('value', $this->pc->options['pieSliceText']);

        $this->pc->pieSliceText('label');
        $this->assertEquals('label', $this->pc->options['pieSliceText']);

        $this->pc->pieSliceText('none');
        $this->assertEquals('none', $this->pc->options['pieSliceText']);
    }

    /**
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testPieSliceTextWithBadValue()
    {
        $this->pc->pieSliceText('beer');
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testPieSliceTextWithBadTypes($badTypes)
    {
        $this->pc->pieSliceText($badTypes);
    }

    public function testPieSliceTextStyle()
    {
        $textStyleVals = array(
            'color'    => 'blue',
            'fontName' => 'Arial',
            'fontSize' => 16
        );

        $mockTextStyle = m::mock('Khill\Lavacharts\Configs\TextStyle');
        $mockTextStyle->shouldReceive('getValues')->once()->andReturn($textStyleVals);

        $this->pc->pieSliceTextStyle($mockTextStyle);

        $this->assertEquals($textStyleVals, $this->pc->options['pieSliceTextStyle']);
    }

    public function testPieStartAngle()
    {
        $this->pc->pieStartAngle(12);

        $this->assertEquals(12, $this->pc->options['pieStartAngle']);
    }

    /**
     * @dataProvider nonIntProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testPieStartAngleWithBadTypes($badTypes)
    {
        $this->pc->pieStartAngle($badTypes);
    }

    public function testReverseCategories()
    {
        $this->pc->reverseCategories(true);

        $this->assertTrue($this->pc->options['reverseCategories']);
    }

    /**
     * @dataProvider nonBooleanProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testReverseCategoriesWithBadType($badTypes)
    {
        $this->pc->reverseCategories($badTypes);
    }

    public function testSliceVisibilityThreshold()
    {
        $this->pc->sliceVisibilityThreshold(23);

        $this->assertEquals(23, $this->pc->options['sliceVisibilityThreshold']);
    }

    /**
     * @dataProvider nonNumericProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testSliceVisibilityThresholdWithBadTypes($badTypes)
    {
        $this->pc->sliceVisibilityThreshold($badTypes);
    }

    public function testPieResidueSliceColor()
    {
        $this->pc->pieResidueSliceColor('red');

        $this->assertEquals('red', $this->pc->options['pieResidueSliceColor']);
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testPieResidueSliceColorWithBadTypes($badTypes)
    {
        $this->pc->pieResidueSliceColor($badTypes);
    }

    public function testPieResidueSliceLabel()
    {
        $this->pc->pieResidueSliceLabel('leftovers');

        $this->assertEquals('leftovers', $this->pc->options['pieResidueSliceLabel']);
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testPieResidueSliceLabelWithBadTypes($badTypes)
    {
        $this->pc->pieResidueSliceLabel($badTypes);
    }
}
