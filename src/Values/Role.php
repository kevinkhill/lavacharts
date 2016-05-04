<?php

namespace Khill\Lavacharts\Values;

use Khill\Lavacharts\Exceptions\InvalidColumnRole;

/**
 * Role Value Object
 *
 * Creates a new label for a chart or dashboard while checking if it is a non empty string.
 *
 * @package   Khill\Lavacharts\Values
 * @since     3.0.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2016, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
class Role extends StringValue
{
    /**
     * Valid column roles
     *
     * @var array
     */
    public static $roles = [
        'annotation',
        'annotationText',
        'certainty',
        'emphasis',
        'interval',
        'scope',
        'style',
        'tooltip'
    ];

    public function __construct($role)
    {
        try {
            var_dump($role);
            parent::__construct($role);

            if (in_array($this->value, self::$roles) === false) {
                throw new InvalidColumnRole($this->value, self::$roles);
            }
        } catch (\Exception $e) {
            throw new InvalidColumnRole($role, self::$roles);
        }
    }
}
