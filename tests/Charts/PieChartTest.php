<?php namespace Khill\Lavacharts\Tests\Charts;

use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Khill\Lavacharts\Charts\PieChart;
use \Mockery as m;

class PieChartTest extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->pc = new PieChart('MyTestChart');
    }

    public function testInstanceOfPieChartWithType()
    {
        $this->assertInstanceOf('\Khill\Lavacharts\Charts\PieChart', $this->pc);
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

        $this->assertTrue($this->pc->getOption('is3D'));
    }

    /**
     * @dataProvider nonBoolProvider
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

         $slices = $this->pc->getOption('slices');

         $this->assertEquals($sliceVals, $slices[0]);
         $this->assertEquals($sliceVals, $slices[1]);
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

        $this->assertEquals('green', $this->pc->getOption('pieSliceBorderColor'));
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
        $this->assertEquals('percentage', $this->pc->getOption('pieSliceText'));

        $this->pc->pieSliceText('value');
        $this->assertEquals('value', $this->pc->getOption('pieSliceText'));

        $this->pc->pieSliceText('label');
        $this->assertEquals('label', $this->pc->getOption('pieSliceText'));

        $this->pc->pieSliceText('none');
        $this->assertEquals('none', $this->pc->getOption('pieSliceText'));
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

        $this->assertEquals($textStyleVals, $this->pc->getOption('pieSliceTextStyle'));
    }

    public function testPieStartAngle()
    {
        $this->pc->pieStartAngle(12);

        $this->assertEquals(12, $this->pc->getOption('pieStartAngle'));
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

        $this->assertTrue($this->pc->getOption('reverseCategories'));
    }

    /**
     * @dataProvider nonBoolProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testReverseCategoriesWithBadType($badTypes)
    {
        $this->pc->reverseCategories($badTypes);
    }

    public function testSliceVisibilityThreshold()
    {
        $this->pc->sliceVisibilityThreshold(23);

        $this->assertEquals(23, $this->pc->getOption('sliceVisibilityThreshold'));
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

        $this->assertEquals('red', $this->pc->getOption('pieResidueSliceColor'));
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

        $this->assertEquals('leftovers', $this->pc->getOption('pieResidueSliceLabel'));
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
