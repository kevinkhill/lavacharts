<?php

namespace Khill\Lavacharts\Dashboards\Filters;

use \Khill\Lavacharts\Options;
use \Khill\Lavacharts\Exceptions\InvalidConfigValue;

/**
 * String Filter Class
 *
 * @package    Khill\Lavacharts
 * @subpackage Dashboards\Filters
 * @since      3.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 * @see        https://developers.google.com/chart/interactive/docs/gallery/controls#googlevisualizationstringfilter
 */
class StringFilter extends Filter
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
    private $extDefaults = [
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
     */
    public function __construct($columnLabelOrIndex, $config = [])
    {
        $options = new Options($this->defaults);
        $options->extend($this->extDefaults);

        parent::__construct($options, $columnLabelOrIndex, $config);
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
     * @return \Khill\Lavacharts\Dashboards\Filters\StringFilter
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function matchType($matchType)
    {
        $values = [
            'exact',
            'prefix',
            'any'
        ];

        return $this->setStringInArrayOption(__FUNCTION__, $matchType, $values);
    }

    /**
     * Whether matching should be case sensitive or not.
     *
     * @param  boolean $caseSensitive
     * @return \Khill\Lavacharts\Dashboards\Filters\StringFilter
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function caseSensitive($caseSensitive)
    {
        return $this->setBoolOption(__FUNCTION__, $caseSensitive);
    }

    /**
     * Whether the control should match against cell formatted values or against actual values.
     *
     * @param  boolean $useFormattedValue
     * @return \Khill\Lavacharts\Dashboards\Filters\StringFilter
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function useFormattedValue($useFormattedValue)
    {
        return $this->setBoolOption(__FUNCTION__, $useFormattedValue);
    }
}
