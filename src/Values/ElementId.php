<?php

namespace Khill\Lavacharts;

use Khill\Lavacharts\Exceptions\InvalidElementId;

/**
 * ElementId Value Object
 *
 *
 * Creates a new elementId for a html entity while checking if it is a non empty string.
 *
 * @category  Class
 * @package   Lavacharts
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2015, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT MIT
 */
class ElementId
{
    private $elementId;

    public function __construct($elementId)
    {
        if (is_string($elementId) === false || empty($elementId) === true) {
            throw new InvalidElementId($elementId);
        }

        $this->elementId = $elementId;
    }

    public function __toString()
    {
        return $this->elementId;
    }
}
