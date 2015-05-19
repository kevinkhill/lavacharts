<?php

namespace Khill\Lavacharts\Formats;

use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Exceptions\InvalidConfigValue;

/**
 * ArrowFormat Object
 *
 * Adds an up or down arrow to a numeric cell, depending on whether the value
 * is above or below a specified base value. If equal to the base value, no arrow is shown. 
 *
 *
 * @package    Lavacharts
 * @subpackage Formats
 * @since      3.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 * @see        https://developers.google.com/chart/interactive/docs/reference#dateformatter
 */
class ArrowFormat extends Format
{
    const TYPE = 'ArrowFormat';

    /**
     * A number indicating the base value.
     *
     * @var int|float
     */
    public $formatType;

    /**
     * Builds the ArrowFormat object with specified options
     *
     * @param  array $config
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     * @return self
     */
    public function __construct($config = [])
    {
        parent::__construct($this, $config);
    }

    /**
     * A number indicating the base value, used to compare against the cell value.
     * 
     * If the cell value is higher, the cell will include a green up arrow.
     * If the cell value is lower, it will include a red down arrow.
     * If the same, no arrow. 
     *
     * @param  int|float $base
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return self
     */
    public function base($base)
    {
        if (is_numeric($base) === false) {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'numeric'
            );
        }

        return $this;
    }
}
