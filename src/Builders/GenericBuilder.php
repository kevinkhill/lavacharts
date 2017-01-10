<?php

namespace Khill\Lavacharts\Builders;

use Khill\Lavacharts\Values\Label;
use Khill\Lavacharts\Values\ElementId;

/**
 * Class GenericBuilder
 *
 * This class will provide some common methods to the other builders.
 *
 * @package    Khill\Lavacharts\Builders
 * @since      3.1.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2017, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT      MIT
 */
class GenericBuilder
{
    /**
     * The chart's unique label.
     *
     * @var \Khill\Lavacharts\Values\Label
     */
    protected $label = null;

    /**
     * The chart's unique elementId.
     *
     * @var \Khill\Lavacharts\Values\ElementId
     */
    protected $elementId = null;

    /**
     * Creates and sets the label for the chart.
     *
     * @param  string|\Khill\Lavacharts\Values\Label $label
     * @return self
     * @throws \Khill\Lavacharts\Exceptions\InvalidLabel
     */
    public function setLabel($label)
    {
        $this->label = new Label($label);

        return $this;
    }

    /**
     * Creates and sets the elementId for the chart.
     *
     * @param  string|\Khill\Lavacharts\Values\ElementId $elementId
     * @return self
     * @throws \Khill\Lavacharts\Exceptions\InvalidElementId
     */
    public function setElementId($elementId)
    {
        $this->elementId = new ElementId($elementId);

        return $this;
    }
}
