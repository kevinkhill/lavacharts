<?php

namespace Khill\Lavacharts\Tests\Configs;

use \Khill\Lavacharts\Configs\Options;
use \Khill\Lavacharts\Tests\ProvidersTestCase;

class OptionsTest extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->Options = new Options([
            'fakeOption1',
            'fakeOption2',
            'fakeOption3'
        ]);
    }

    /**
     * @dataProvider nonArrayProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testConstructorWithBadTypes($badTypes)
    {
        new Options($badTypes);
    }

    public function testHasOptionMethod()
    {
        $this->assertTrue($this->Options->hasOption('fakeOption1'));
        $this->assertTrue($this->Options->hasOption('fakeOption2'));
        $this->assertTrue($this->Options->hasOption('fakeOption3'));
    }

    public function testHasOptionWithNonDefinedOption()
    {
        $this->assertFalse($this->Options->hasOption('notRealOption'));
    }

    /**
     * @dataProvider nonStringProvider
     */
    public function testHasOptionMethodWithBadTypes($badTypes)
    {
        $this->assertFalse($this->Options->hasOption($badTypes));
    }

    public function testSetAndGetOption()
    {
        $this->Options->set('fakeOption1', true);
        $this->Options->set('fakeOption2', 1);
        $this->Options->set('fakeOption3', 'Hamburger');

        $this->assertTrue($this->Options->get('fakeOption1', true));
        $this->assertEquals($this->Options->get('fakeOption2'), 1);
        $this->assertEquals($this->Options->get('fakeOption3'), 'Hamburger');
    }

    public function testGetValues()
    {
        $this->Options->set('fakeOption1', true);
        $this->Options->set('fakeOption2', 1);
        $this->Options->set('fakeOption3', 'Hamburger');

        $this->assertEquals($this->Options->getValues(), [
            'fakeOption1' => true,
            'fakeOption2' => 1,
            'fakeOption3' => 'Hamburger'
        ]);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidOption
     */
    public function testSetWithNonValidOption()
    {
        $this->Options->set('NotARealOption', 'What?');
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidOption
     */
    public function testSetWithBadTypes($badTypes)
    {
        $this->Options->set($badTypes, 'option');
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidOption
     */
    public function testGetWithNonValidOption()
    {
        $this->Options->get('NotARealOption');
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidOption
     */
    public function testGetWithBadTypes($badTypes)
    {
        $this->Options->get($badTypes);
    }

    public function testExtendWithMoreOptions()
    {
        $options = new Options([
            'fakeOption1',
            'fakeOption2',
            'fakeOption3'
        ]);

        $options->extend([
            'fakeOption4',
            'fakeOption5'
        ]);

        $this->assertEquals($options->getDefaults(), [
            'fakeOption1',
            'fakeOption2',
            'fakeOption3',
            'fakeOption4',
            'fakeOption5'
        ]);
    }

    /**
     * @dataProvider nonArrayProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testExtendWithBadTypes($badTypes)
    {
        $this->Options->extend($badTypes);
    }

    public function testRemovalOfDefaultOptions()
    {
        $options = new Options([
            'fakeOption1',
            'fakeOption2',
            'fakeOption3',
            'fakeOption4'
        ]);

        $options->remove([
            'fakeOption1',
            'fakeOption3'
        ]);

        $this->assertEquals($options->getDefaults(), [
            'fakeOption2',
            'fakeOption4'
        ]);
    }

    /**
     * @dataProvider nonArrayProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testRemoveWithBadTypes($badTypes)
    {
        $this->Options->remove($badTypes);
    }
}
