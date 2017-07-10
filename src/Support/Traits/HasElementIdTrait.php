<?php

namespace Khill\Lavacharts\Support\Traits;

use Khill\Lavacharts\Support\StringValue as Str;

/**
 * Trait ElementIdTrait
 *
 * Trait for adding the methods for getting/setting the html id of a div tag,
 * that a Renderable can be output into.
 *
 * @package   Khill\Lavacharts\Support\Traits
 * @since     3.1.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
trait HasElementIdTrait
{
    /**
     * The renderable's unique elementId.
     *
     * @var string
     *
     */
    protected $elementId;

    /**
     * Returns the ElementId.
     *
     * @return string
     */
    public function getElementId()
    {
        return $this->elementId;
    }

    /**
     * Check to see if the class has it's elementId set.
     *
     * @since  3.1.0
     * @return bool
     */
    public function hasElementId()
    {
        return isset($this->elementId);
    }

    /**
     * Creates and/or sets the e lementId.
     *
     * @param string $elementId
     * @return self
     */
    public function setElementId($elementId)
    {
        $this->elementId = Str::verify($elementId);

        return $this;
    }
}
