<?php

namespace Khill\Lavacharts\Dashboards\Filters;

use Khill\Lavacharts\Exceptions\InvalidFilterParam;
use Khill\Lavacharts\Support\Customizable;
use Khill\Lavacharts\Support\Contracts\WrappableInterface as Wrappable;

/**
 * Filter Parent Class
 *
 * The base class for the individual filter objects, providing common
 * functions to the child objects.
 *
 *
 * @package   Khill\Lavacharts\Dashboards\Filters
 * @since     3.0.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2016, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
class Filter extends Customizable implements Wrappable, \JsonSerializable
{
    /**
     * Type of wrapped class
     */
    const WRAP_TYPE = 'controlType';

    /**
     * Builds a new Filter Object.
     * Takes either a column label or a column index to filter. The options object will be
     * created internally, so no need to set defaults. The child filter objects will set them.
     *
     * @param  string|int $cLabelOrIndex
     * @param  array      $options Array of options to set.
     * @throws \Khill\Lavacharts\Exceptions\InvalidFilterParam
     */
    public function __construct($cLabelOrIndex, array $options = [])
    {
        switch (gettype($cLabelOrIndex)) {
            case 'string':
                $options = array_merge($options, ['filterColumnLabel' => $cLabelOrIndex]);
                break;
            case 'integer':
                $options = array_merge($options, ['filterColumnIndex' => $cLabelOrIndex]);
                break;
            default:
                throw new InvalidFilterParam($cLabelOrIndex);
                break;
        }

        parent::__construct($options);
    }

    /**
     * Filter Factory for creating new filters
     *
     * @param string     $type
     * @param string|int $cLabelOrIndex
     * @param array      $options
     * @return \Khill\Lavacharts\Dashboards\Filters\Filter
     * @throws \Khill\Lavacharts\Exceptions\InvalidFilterParam
     */
    public static function Factory($type, $cLabelOrIndex, array $options = [])
    {
        if (is_string($type) === false) {
            throw new InvalidFilterParam($type);
        }

        if (is_string($cLabelOrIndex) === false && is_int($cLabelOrIndex) === false) {
            throw new InvalidFilterParam($cLabelOrIndex);
        }

        if (strpos($type, 'range') !== false) {
            $filter = ucfirst(str_replace('range', 'Range', $type));
        } else {
            $filter = $type;
        }

        $filter = __NAMESPACE__ . '\\' . $filter . 'Filter';

        return new $filter($cLabelOrIndex, $options);
    }

    /**
     * Returns the Filter type.
     *
     * @return string
     */
    public function getType()
    {
        return static::TYPE;
    }

    /**
     * Returns the Filter wrap type.
     *
     * @since 3.1.0
     * @return string
     */
    public function getWrapType()
    {
        return static::WRAP_TYPE;
    }
}
