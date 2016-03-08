<?php

namespace Khill\Lavacharts\Tests\Configs;

use \Khill\Lavacharts\Support\Renderable;
use \Khill\Lavacharts\Tests\ProvidersTestCase;

class RenderableTest extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->renderable = new Renderable(
            $this->getMockElemId('my-div')
        );
    }

    public function testGetElementId()
    {
        $this->assertEquals($this->renderable->getElementId(), 'my-div');
    }

    public function testSetElementId()
    {
        $this->renderable->setElementId(
            $this->getMockElemId('my-new-div')
        );

        $newElemId = (string) $this->getPrivateProperty($this->renderable, 'elementId');

        $this->assertEquals($newElemId, 'my-new-div');
    }
}
