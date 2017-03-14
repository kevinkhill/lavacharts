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
 * @copyright (c) 2017, KHill Designs
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
        'tooltip',
        'data',
        'domain',
    ];

    public function __construct($role)
    {
        try {
            parent::__construct($role);

            if (static::isValid($role) === false) {
                throw new InvalidColumnRole($this->value, self::$roles);
            }
        } catch (\Exception $e) {
            throw new InvalidColumnRole($role, self::$roles);
        }
    }

    /**
     * Checks if a given value is a valid role.
     *
     * @since  3.1.0
     * @param  string $role
     * @return bool
     */
    public static function isValid($role)
    {
        if (is_string($role) && in_array($role, static::$roles)) {
            return true;
        } else {
            return false;
        }
    }
}
