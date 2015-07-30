<?php

namespace Khill\Lavacharts\Dashboards\Filters;

use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Configs\Options;
use \Khill\Lavacharts\Exceptions\InvalidConfigValue;

/**
 * String Filter Class
 *
 * @package    Lavacharts
 * @subpackage Dashboards\Filters
 * @since      3.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 * @see        https://developers.google.com/chart/interactive/docs/gallery/controls#googlevisualizationstringfilter
 */
class String extends Filter
{
    /**
     * Type of Filter.
     *
     * @var string
     */
    const TYPE = 'StringFilter';

    /**
     * NumberRange specific default options.
     *
     * @var array
     */
    private $defaults = [
        'matchType',
        'caseSensitive',
        'useFormattedValue'
    ];

    /**
     * Creates the new Filter object to filter the given column label or index.
     *
     * @param  string|int $columnLabelOrIndex The column label or index to filter.
     * @param  array $config Array of options to set.
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return self
     */
    public function __construct($columnLabelOrIndex, $config = [])
    {
        parent::__construct($columnLabelOrIndex, $this->defaults, $config);
    }

    /**
     * What type of string the control should match.
     *
     * Allowed types:
     * - exact  : Match exact values only
     * - prefix : Prefixes starting from the beginning of the value ('prefix')
     * - any    : Any substring
     *
     * @param  string $matchType
     * @throws InvalidConfigValue
     * @return self
     */
    public function matchType($matchType)
    {
        $matchTypes = [
            'exact',
            'prefix',
            'any'
        ];

        if (Utils::nonEmptyStringInArray($matchType, $matchTypes) === false) {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'string',
                'whose value is one of '.Utils::arrayToPipedString($matchTypes)
            );
        }

        return $this->setOption(__FUNCTION__, $matchType);
    }

    /**
     * Whether matching should be case sensitive or not.
     *
     * @param  boolean $caseSensitive
     * @throws InvalidConfigValue
     * @return self
     */
    public function caseSensitive($caseSensitive)
    {
        if (is_bool($caseSensitive) === false) {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'boolean'
            );
        }

        return $this->setOption(__FUNCTION__, $caseSensitive);
    }

    /**
     * Whether the control should match against cell formatted values or against actual values.
     *
     * @param  boolean $useFormattedValue
     * @throws InvalidConfigValue
     * @return self
     */
    public function useFormattedValue($useFormattedValue)
    {
        if (is_bool($useFormattedValue) === false) {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'boolean'
            );
        }

        return $this->setOption(__FUNCTION__, $useFormattedValue);
    }
}
