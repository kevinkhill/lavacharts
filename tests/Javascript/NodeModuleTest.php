<?php

namespace Khill\Lavacharts\Tests\Javascript;

use PHPUnit\Framework\TestCase;
use Khill\Lavacharts\Javascript\NodeModule;

class NodeModuleTest extends TestCase
{
    public function testLavaJsAsNodeModule()
    {
        $module = new NodeModule('@lavacharts/lava.js');
var_dump($module);
        $this->assertFileExists(
            $module->resolve('lava.js')
        );
    }

    public function testLavachartsJsAsNodeModule()
    {
        $module = new NodeModule('@lavacharts/lava.js');

        $this->assertFileExists(
            $module->resolve('lavacharts.js')
        );
    }
}
