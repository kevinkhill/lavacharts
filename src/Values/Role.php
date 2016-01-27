<?php

namespace Khill\Lavacharts\Values;

use \Khill\Lavacharts\Exceptions\InvalidColumnRole;

/**
 * Role Value Object
 *
 *
 * Creates a new Role object for a column roles while checking if it is a non empty string.
 *
 * @category  Class
 * @package   Khill\Lavacharts
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2015, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT MIT
 */
class Role extends StringValue
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

    /**
     * Creates a new Role object.
     *
     * @param  string $type
     * @throws \Khill\Lavacharts\Exceptions\InvalidColumnRole
     */
    public function __construct($type)
    {
        if (in_array($type, $this->validRoles) === false) {
            throw new InvalidColumnRole($type);
        }

        parent::__construct($type);
    }
}
