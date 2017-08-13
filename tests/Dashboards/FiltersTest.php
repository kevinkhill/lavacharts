<?php

namespace Khill\Lavacharts\Tests\Dashboards\Filters;

use Khill\Lavacharts\Dashboards\Filter;
use Khill\Lavacharts\Lavacharts;
use Khill\Lavacharts\Support\Options;
use Khill\Lavacharts\Tests\ProvidersTestCase;

class FiltersTest extends ProvidersTestCase
{
    /**
     * @var Lavacharts
     */
    protected $lava;

    public function setUp()
    {
        parent::setUp();

        $this->lava = new Lavacharts;
    }

    /**
     * @dataProvider filterTypeProvider
     * @param $filterType
     */
    public function testGetType($filterType)
    {
        /** @var Filter $filter */
        $filter = $this->lava->$filterType(1);

        $this->assertEquals($filterType, $filter->getType());
    }

    /**
     * @dataProvider filterTypeProvider
     * @param $filterType
     */
    public function testGetWrapType($filterType)
    {
        /** @var Filter $filter */
        $filter = $this->lava->$filterType(1);

        $this->assertEquals('controlType', $filter->getWrapType());
    }

    /**
     * @dataProvider shortnameFilterTypeProvider
     * @param $filterType
     */
    public function testCreatingFiltersStaticallyByNameAndColumnIndex($filterType)
    {
        $filter = Filter::create($filterType, [1]);

        $this->assertEquals(1, $filter->getOption('filterColumnIndex'));
    }

    /**
     * @dataProvider filterTypeProvider
     * @param $filterType
     */
    public function testCreatingFiltersStaticallyByFullNameAndColumnIndex($filterType)
    {
        $filter = Filter::create($filterType, [1]);

        $this->assertEquals(1, $filter->getOption('filterColumnIndex'));
    }

    /**
     * @dataProvider shortnameFilterTypeProvider
     * @param $filterType
     */
    public function testCreatingFiltersStaticallyByNameAndColumnIndexWithOptions($filterType)
    {
        $filter = Filter::create($filterType, [1, ['thisOption' => 'hasValue']]);

        $this->assertEquals(1, $filter->getOption('filterColumnIndex'));
        $this->assertInstanceOf(Options::class, $filter->getOptions());
        $this->assertEquals('hasValue', $filter->getOption('thisOption'));
    }

    /**
     * @dataProvider shortnameFilterTypeProvider
     * @param $filterType
     */
    public function testCreatingFiltersByNameAndColumnIndex($filterType)
    {
        $filter = new Filter($filterType, 1);

        $this->assertEquals(1, $filter->getOption('filterColumnIndex'));
    }

    /**
     * @dataProvider filterTypeProvider
     * @param $filterType
     */
    public function testCreatingFiltersByFullNameAndColumnIndex($filterType)
    {
        $filter = new Filter($filterType, 1);

        $this->assertEquals(1, $filter->getOption('filterColumnIndex'));
    }

    /**
     * @dataProvider filterTypeProvider
     * @param $filterType
     */
    public function testCreatingFiltersViaLavaByNamedAliasAndColumnIndex($filterType)
    {
        /** @var Filter $filter */
        $filter = $this->lava->$filterType(1);

        $this->assertEquals(1, $filter->getOption('filterColumnIndex'));
    }

    /**
     * @dataProvider shortnameFilterTypeProvider
     * @param $filterType
     */
    public function testCreatingFiltersByNameAndColumnLabel($filterType)
    {
        $filter = new Filter($filterType, 'TheLabel');

        $this->assertEquals('TheLabel', $filter->getOption('filterColumnLabel'));
    }

    /**
     * @dataProvider filterTypeProvider
     * @param $filterType
     */
    public function testCreatingFiltersByFullNameAndColumnLabel($filterType)
    {
        $filter = new Filter($filterType, 'TheLabel');

        $this->assertEquals('TheLabel', $filter->getOption('filterColumnLabel'));
    }

    /**
     * @dataProvider filterTypeProvider
     * @param $filterType
     */
    public function testCreatingFiltersViaLavaByNamedAliasAndColumnLabel($filterType)
    {
        /** @var Filter $filter */
        $filter = $this->lava->$filterType('TheLabel');

        $this->assertEquals('TheLabel', $filter->getOption('filterColumnLabel'));
    }

    /**
     * @dataProvider shortnameFilterTypeProvider
     * @param $filterType
     */
    public function testCreatingFiltersByNameAndColumnIndexWithOptions($filterType)
    {
        $filter = new Filter($filterType, 1, ['thisOption' => 'hasValue']);

        $this->assertEquals(1, $filter->getOption('filterColumnIndex'));
        $this->assertInstanceOf(Options::class, $filter->getOptions());
        $this->assertEquals('hasValue', $filter->getOption('thisOption'));
    }

    /**
     * @dataProvider filterTypeProvider
     * @param $filterType
     */
    public function testCreatingFiltersByFullNameAndColumnIndexWithOptions($filterType)
    {
        $filter = new Filter($filterType, 1, ['thisOption' => 'hasValue']);

        $this->assertEquals(1, $filter->getOption('filterColumnIndex'));
        $this->assertInstanceOf(Options::class, $filter->getOptions());
        $this->assertEquals('hasValue', $filter->getOption('thisOption'));
    }

    /**
     * @dataProvider filterTypeProvider
     * @param $filterType
     */
    public function testCreatingFiltersViaLavaByNamedAliasAndColumnIndexWithOptions($filterType)
    {
        /** @var Filter $filter */
        $filter = $this->lava->$filterType(1, ['thisOption' => 'hasValue']);

        $this->assertEquals(1, $filter->getOption('filterColumnIndex'));
        $this->assertInstanceOf(Options::class, $filter->getOptions());
        $this->assertEquals('hasValue', $filter->getOption('thisOption'));
    }

    /**
     * @expectedException \PHPUnit_Framework_Error_Warning
     */
    public function testCreatingFiltersStaticallyByNameWithMissingColumnIndex()
    {
        Filter::create('NumberFilter');
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidFilterType
     */
    public function testCreatingInvalidFilterTypeViaLava()
    {
        $this->lava->Filter('TacoFilter', 1);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidArgumentException
     */
    public function testInvalidArgumentForFilterLabel()
    {
        $this->lava->StringFilter(['ThisShouldFail']);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\BadMethodCallException
     */
    public function testLavaAliasForInvalidFilterType()
    {
        $this->lava->TacoFilter();
    }
}
