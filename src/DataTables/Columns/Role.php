<?php

namespace Khill\Lavacharts\DataTables\Columns;

use Khill\Lavacharts\Exceptions\InvalidColumnRole;
use Khill\Lavacharts\Support\StringValue;

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
    const TYPES = [
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

    /**
     * Role constructor.
     *
     * @param string $role
     * @throws InvalidColumnRole
     */
    public function __construct($role)
    {
        static::check($role);

        parent::__construct($role);
    }

    /**
     * Type of role to check
     *
     * @param string $role
     * @throws \Khill\Lavacharts\Exceptions\InvalidColumnRole
     */
    public static function check($role)
    {
        if (! self::isValid($role)) {
            throw new InvalidColumnRole($role);
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
        return in_array($role, self::TYPES, true);
    }
}
