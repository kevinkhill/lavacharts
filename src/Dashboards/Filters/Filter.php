<?php

namespace Khill\Lavacharts\Dashboards\Filters;

use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Exceptions\InvalidConfigValue;

/**
 * Filter Parent Class
 *
 * The base class for the individual filter objects, providing common
 * functions to the child objects.
 *
 *
 * @package    Lavacharts
 * @subpackage Dashbaords\Filters
 * @since      3.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class Filter
{
    /**
     * Label for the filter
     *
     * @var string
     */
    public $columnLabel;

    /**
     * Options for the Filter
     *
     * @var array
     */
    private $options;

    /**
     * Builds a new Filter Object
     *
     * @param  string $columnLabel
     * @return self
     */
    public function __construct($columnLabel, $options=[])
    {
        if (Utils::nonEmptyString($columnLabel) === false) {
            throw new InvalidConfigValue(
                get_class(),
                __FUNCTION__,
                'string'
            );
        }

        $this->columnLabel = $columnLabel;
    }

    public function setOptions()
    {
        $this->options = array_map(function ($prop) {
            return $prop->name;
        }, $class->getProperties(\ReflectionProperty::IS_PUBLIC));

        if (is_array($config) === false) {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'array',
                'with valid keys as '.Utils::arrayToPipedString($this->options)
            );
        }

        foreach ($config as $option => $value) {
            if (in_array($option, $this->options)) {
                $this->{$option}($value);
            } else {
                throw new InvalidConfigProperty(
                    $this->className,
                    __FUNCTION__,
                    $option,
                    $this->options
                );
            }
        }
    }
}
