<?php

namespace Khill\Lavacharts\Traits;

use \Khill\Lavacharts\Values\ElementId;

/**
 * Trait for adding html element IDs to classes that will be rendered into a div
 * when passed through the JavascriptFactory
 *
 * @since  3.1.0
 */
trait ElementIdTrait
{
    /**
     * The chart's unique elementId.
     *
     * @var \Khill\Lavacharts\Values\ElementId
     */
    protected $elementId;

    /**
     * Creates and sets the elementId
     *
     * @param string $elemIdStr
     * @throws \Khill\Lavacharts\Exceptions\InvalidElementId
     */
    public function createElementId($elemIdStr)
    {
        $this->elementId = new ElementId($elemIdStr);
    }

    /**
     * Sets the elementId
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
     * @return \Khill\Lavacharts\Values\ElementId
     */
    public function getElementId()
    {
        return $this->elementId;
    }
}
