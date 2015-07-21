<?php

namespace Khill\Lavacharts\Values;

use Khill\Lavacharts\Exceptions\InvalidElementId;

/**
 * Role Value Object
 *
 *
 * Creates a new Role object for a column roles while checking if it is a non empty string.
 *
 * @category  Class
 * @package   Lavacharts
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2015, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT MIT
 */
class Role extends String
{
    private $validRoles = [
        'annotation',
        'annotationText',
        'certainty',
        'emphasis',
        'interval',
        'scope',
        'style',
        'tooltip'
    ];

    public function __construct($value)
    {
        parent::__construct($value);

        if (in_array($value, $this->valideRoles) === false) {
            throw new InvalidRole($value);
        }
    }
}
