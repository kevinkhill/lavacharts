<?php

namespace Khill\Lavacharts\Traits;

use \Khill\Lavacharts\Values\ElementId;

trait ElementIdTrait
{
    /**
     * The chart's unique elementId.
     *
     * @var \Khill\Lavacharts\Values\ElementId
     */
    protected $elementID;

    /**
     * Creates and sets the elementId
     *
     * @param string $elemIdStr
     */
    public function createElementId($elemIdStr)
    {
        $this->elementId = new ElementId($elemIdStr);
    }

    /**
     * Sets the element ID
     *
     * @param  \Khill\Lavacharts\Values\ElementId $elementId
     */
    public function setElementId(ElementId $elementId)
    {
        $this->elementId = $elementId;
    }

    /**
     * Returns the elementId.
     *
     * @since  3.1.0
     * @param  bool $asString Toggle to return Object or string
     * @return \Khill\Lavacharts\Values\ElementId
     */
    public function getElementId($asString = false)
    {
        return $asString ? (string) $this->elementID : $this->elementID;
    }
}
