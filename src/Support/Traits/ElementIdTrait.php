<?php

namespace Khill\Lavacharts\Support\Traits;

use \Khill\Lavacharts\Values\ElementId;

trait ElementIdTrait
{
    /**
     * Creates and/or sets the ElementId.
     *
     * @param  string|\Khill\Lavacharts\Values\ElementId $elementId
     * @throws \Khill\Lavacharts\Exceptions\InvalidElementId
     */
    public function setElementId($elementId)
    {
        if ($elementId instanceof ElementId) {
            $this->elementId = $elementId;
        } else {
            $this->elementId = new ElementId($elementId);
        }
    }

    /**
     * Returns the ElementId.
     *
     * @return \Khill\Lavacharts\Values\ElementId
     */
    public function getElementId()
    {
        return $this->elementId;
    }

    /**
     * Returns the ElementId.
     *
     * @return \Khill\Lavacharts\Values\ElementId
     */
    public function getElementIdStr()
    {
        return (string) $this->elementId;
    }
}
