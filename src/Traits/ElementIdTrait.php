<?php

namespace Khill\Lavacharts\Traits;

use \Khill\Lavacharts\Values\Label;
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

    /**
     * Remove unwanted characters from elementId
     *
     * @link http://stackoverflow.com/a/11330527/2503458
     */
    public function generateElementId(Label $label)
    {
        $string = strtolower((string) $label);
        $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
        $string = preg_replace("/[\s-]+/", " ", $string);
        $string = preg_replace("/[\s_]/", "-", $string);

        return $string;
    }
}
