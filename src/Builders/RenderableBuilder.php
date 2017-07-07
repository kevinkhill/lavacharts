<?php

namespace Khill\Lavacharts\Builders;

use Khill\Lavacharts\Support\StringValue as Str;

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
class RenderableBuilder
{
    /**
     * The chart's unique label.
     *
     * @var string
     */
    protected $label = null;

    /**
     * The chart's unique elementId.
     *
     * @var string
     */
    protected $elementId = null;

    /**
     * Creates and sets the label for the chart.
     *
     * @param  string $label
     * @return self
     */
    public function setLabel($label)
    {
        $this->label = Str::verify($label);

        return $this;
    }

    /**
     * Creates and sets the elementId for the chart.
     *
     * @param  string $elementId
     * @return self
     */
    public function setElementId($elementId)
    {
        $this->elementId = Str::verify($elementId);

        return $this;
    }
}
