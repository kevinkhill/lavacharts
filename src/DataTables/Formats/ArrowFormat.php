<?php

namespace Khill\Lavacharts\DataTables\Formats;

use \Khill\Lavacharts\Options;

/**
 * ArrowFormat Object
 *
 * Adds an up or down arrow to a numeric cell, depending on whether the value
 * is above or below a specified base value. If equal to the base value, no arrow is shown.
 *
 *
 * @package    Khill\Lavacharts
 * @subpackage Formats
 * @since      3.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 * @see        https://developers.google.com/chart/interactive/docs/reference#arrowformatter
 */
class ArrowFormat extends Format
{
    /**
     * Type of format object
     *
     * @var string
     */
    const TYPE = 'ArrowFormat';

    /**
     * Default options for ArrowFormat
     *
     * @var array
     */
    private $defaults = [
        'base'
    ];

    /**
     * Builds the ArrowFormat object with specified options
     *
     * @param  array $config
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     */
    public function __construct($config = [])
    {
        $options = new Options($this->defaults);

        parent::__construct($options, $config);
    }

    /**
     * A number indicating the base value, used to compare against the cell value.
     *
     * If the cell value is higher, the cell will include a green up arrow.
     * If the cell value is lower, it will include a red down arrow.
     * If the same, no arrow.
     *
     * @param  int|float $base
     * @return \Khill\Lavacharts\DataTables\Formats\ArrowFormat
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function base($base)
    {
        return $this->setNumericOption(__FUNCTION__, $base);
    }
}
