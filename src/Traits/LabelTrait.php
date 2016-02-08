<?php

namespace Khill\Lavacharts\Traits;

use \Khill\Lavacharts\Values\Label;

trait LabelTrait
{
    /**
     * The chart's unique label.
     *
     * @var \Khill\Lavacharts\Values\Label
     */
    protected $label;

    /**
     * Creates and sets the label
     *
     * @param string $label
     */
    public function createLabel($labelStr)
    {
        $this->label = new Label($labelStr);
    }

    /**
     * Sets the label
     *
     * @param  \Khill\Lavacharts\Values\Label $label
     */
    public function setLabel(Label $label)
    {
        $this->label = $label;
    }

    /**
     * Returns the label.
     *
     * @since  3.1.0
     * @param  bool $asString Toggle to return Object or string
     * @return \Khill\Lavacharts\Values\Label
     */
    public function getLabel($asString = false)
    {
        return $asString ? (string) $this->label : $this->label;
    }
}
