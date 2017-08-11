<?php

namespace Khill\Lavacharts\Tests\Dashboards\Formats;

use Khill\Lavacharts\DataTables\Columns\Format;
use Khill\Lavacharts\Exceptions\InvalidArgumentException;
use Khill\Lavacharts\Lavacharts;
use Khill\Lavacharts\Support\Options;
use Khill\Lavacharts\Tests\ProvidersTestCase;

class FormatsTest extends ProvidersTestCase
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
     * @dataProvider formatTypeProvider
     * @param $formatType
     */
    public function testGetType($formatType)
    {
        /** @var Format $format */
        $format = $this->lava->$formatType();

        $this->assertEquals($formatType, $format->getType());
    }

    /**
     * @dataProvider formatTypeProvider
     * @param $formatType
     */
    public function testGetIndex($formatType)
    {$this->markTestIncomplete();
        /** @var Format $format */
        $format = $this->lava->$formatType([]);

        $this->assertEquals(1, $format->getIndex());
    }

    /**
     * @group static
     * @dataProvider shortnameFormatTypeProvider
     * @param $formatType
     */
    public function testCreatingFormatsStaticlyByShortName($formatType)
    {
        $type = $formatType.'Format';

        $format = Format::create($formatType, []);

        $this->assertInstanceOf(Format::class, $format);
        $this->assertEquals($type, $format->getType());
    }

    /**
     * @group static
     * @dataProvider formatTypeProvider
     * @param $formatType
     */
    public function testCreatingFormatsStaticlyByFullName($formatType)
    {
        $format = Format::create($formatType, []);

        $this->assertInstanceOf(Format::class, $format);
        $this->assertEquals($formatType, $format->getType());
    }

    /**
     * @group static
     * @dataProvider shortnameFormatTypeProvider
     * @depends testCreatingFormatsStaticlyByShortName
     * @param $formatType
     */
    public function testCreatingFormatsStaticlyByShortNameWithOptions($formatType)
    {
        $format = Format::create($formatType, [['thisOption' => 'hasValue']]);

        $this->assertInstanceOf(Format::class, $format);
        $this->assertInstanceOf(Options::class, $format->getOptions());
        $this->assertEquals('hasValue', $format->getOption('thisOption'));
    }

    /**
     * @group manual
     * @dataProvider shortnameFormatTypeProvider
     * @param $formatType
     */
    public function testCreatingFormatsManuallyByShortName($formatType)
    {
        $type = $formatType.'Format';

        $format = new Format($formatType);

        $this->assertEquals($type, $format->getType());
    }

    /**
     * @group manual
     * @dataProvider shortnameFormatTypeProvider
     * @depends testCreatingFormatsManuallyByShortName
     * @param $formatType
     */
    public function testCreatingFormatsManuallyByShortNameWithOptions($formatType)
    {
        $format = new Format($formatType, ['thisOption' => 'hasValue']);

        $this->assertInstanceOf(Options::class, $format->getOptions());
        $this->assertEquals('hasValue', $format->getOption('thisOption'));
    }

    /**
     * @group manual
     * @dataProvider formatTypeProvider
     * @param $formatType
     */
    public function testCreatingFormatsManuallyByFullName($formatType)
    {
        $format = new Format($formatType);

        $this->assertEquals($formatType, $format->getType());
    }

    /**
     * @group manual
     * @dataProvider formatTypeProvider
     * @depends testCreatingFormatsManuallyByFullName
     * @param $formatType
     */
    public function testCreatingFormatsManuallyByFullNameWithOptions($formatType)
    {
        $format = new Format($formatType, ['thisOption' => 'hasValue']);

        $this->assertInstanceOf(Options::class, $format->getOptions());
        $this->assertEquals('hasValue', $format->getOption('thisOption'));
    }

    /**
     * @group alias
     * @dataProvider formatTypeProvider
     * @param $formatType
     */
    public function testCreatingFormatsViaLavaAlias($formatType)
    {
        /** @var Format $format */
        $format = $this->lava->$formatType();

        $this->assertInstanceOf(Format::class, $format);
        $this->assertEquals($formatType, $format->getType());;
    }

    /**
     * @group alias
     * @dataProvider formatTypeProvider
     * @depends testCreatingFormatsViaLavaAlias
     * @param $formatType
     */
    public function testCreatingFormatsViaLavaAliasWithOptions($formatType)
    {
        /** @var Format $format */
        $format = $this->lava->$formatType(['thisOption' => 'hasValue']);

        $this->assertInstanceOf(Options::class, $format->getOptions());
        $this->assertEquals('hasValue', $format->getOption('thisOption'));
    }

    /**
     * @group errors
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidFormatType
     */
    public function testCreatingFormatViaLavaWithInvalidType()
    {
        $this->lava->Format('TacoFormat', []);
    }

    /**
     * @group alias
     * @group errors
     * @expectedException \Khill\Lavacharts\Exceptions\BadMethodCallException
     */
    public function testCreatingFormatsViaLavaAliasWithNoArgs()
    {
        $this->lava->StringFormat();
    }

    /**
     * @group manual
     * @group errors
     * @expectedException \PHPUnit_Framework_Error_Warning
     */
    public function testCreatingFormatStaticlyWithInvalidType()
    {
        Format::create('TacoFormat');
    }

    /**
     * @group alias
     * @group errors
     * @expectedException \PHPUnit_Framework_Error
     */
    public function testCreatingFormatViaLavaWithInvalidArgument()
    {
        $this->lava->ArrowFormat(4.2);
    }

    /**
     * @group alias
     * @group errors
     * @expectedException \Khill\Lavacharts\Exceptions\BadMethodCallException
     */
    public function testCreatingFormatViaLavaAliasWithInvalidFormatType()
    {
        $this->lava->TacoFormat();
    }
}
