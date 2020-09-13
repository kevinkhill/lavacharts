<?php

namespace Khill\Lavacharts\Tests\Javascript;

use PHPUnit\Framework\TestCase;
use Khill\Lavacharts\Javascript\NodeModule;

class NodeModuleLavachartJsTest extends TestCase
{
    /**
     * @var NodeModule
     */
    private $module;

    public function setUp(): void
    {
        $this->module = new NodeModule('@lavacharts/lava.js');
    }

    public function testNodeModuleResolvingLavachartsJs()
    {
        $this->assertFileExists(
            $this->module->resolve('lavacharts.js')
        );
    }

    public function testGettingFileContentsForLavachartsJs()
    {
        $this->assertStringContainsString(
            "window.lavacharts",
            $this->module->getFileContents('lavacharts.js')
        );
    }

    public function testGetScriptTag()
    {
        $this->assertStringContainsString(
            "<script",
            $this->module->getScriptTag('lavacharts.js')
        );

        $this->assertStringContainsString(
            "</script>",
            $this->module->getScriptTag('lavacharts.js')
        );
    }
}
