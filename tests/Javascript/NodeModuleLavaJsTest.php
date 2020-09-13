<?php

namespace Khill\Lavacharts\Tests\Javascript;

use PHPUnit\Framework\TestCase;
use Khill\Lavacharts\Javascript\NodeModule;

class NodeModuleLavaJsTest extends TestCase
{
    /**
     * @var NodeModule
     */
    private $module;

    public function setUp(): void
    {
        $this->module = new NodeModule('@lavacharts/lava.js');
    }

    public function testNodeModuleResolvingLavaJs()
    {
        $this->assertFileExists(
            $this->module->resolve('lava.js')
        );
    }

    public function testGettingFileContentsForLavaJs()
    {
        $this->assertStringContainsString(
            "function",
            $this->module->getFileContents('lava.js')
        );
    }

    public function testGetScriptTag()
    {
        $this->assertStringContainsString(
            "<script",
            $this->module->getScriptTag('lava.js')
        );

        $this->assertStringContainsString(
            "</script>",
            $this->module->getScriptTag('lava.js')
        );
    }
}
