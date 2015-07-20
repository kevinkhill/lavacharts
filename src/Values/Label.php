<?php

namespace Khill\Lavacharts\Values;

use Khill\Lavacharts\Exceptions\InvalidLabel;

/**
 * Label Value Object
 *
 *
 * Creates a new label for a chart or dashboard while checking if it is a non empty string.
 *
 * @category  Class
 * @package   Lavacharts
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2015, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT MIT
 */
class Label
{
    private $label;

    public function __construct($label)
    {
        if (is_string($label) === false || empty($label) === true) {
            throw new InvalidLabel($label);
        }

        $this->label = $label;
    }

    public function __toString()
    {
        return $this->label;
    }
}
