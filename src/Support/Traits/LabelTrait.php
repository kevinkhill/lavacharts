<?php

namespace Khill\Lavacharts\Support\Traits;

use Khill\Lavacharts\Values\Label;

/**
 * Trait LabelTrait
 *
 * Trait for adding a label to classes as a unique identifier
 *
 * @package   Khill\Lavacharts\Support\Traits
 * @since     3.1.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2016, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
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
     * @param string $labelStr
     */
    public function createLabel($labelStr)
    {
        $this->label = new Label($labelStr);
    }

    /**
     * Sets the label
     *
     * @param \Khill\Lavacharts\Values\Label $label
     */
    public function setLabel(Label $label)
    {
        $this->label = $label;
    }

    /**
     * Returns the label.
     *
     * @return \Khill\Lavacharts\Values\Label
     */
    public function getLabel()
    {
        return $this->label;
    }
}
