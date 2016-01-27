<?php

namespace Khill\Lavacharts\Configs\UIs;

use \Khill\Lavacharts\Options;
use \Khill\Lavacharts\DataTables\Formats\NumberFormat;

/**
 * NumberRangeUI Object
 *
 * Customization for NumberRange Filters in Dashboards.
 *
 * @package    Khill\Lavacharts
 * @subpackage Configs\UIs
 * @since      3.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class NumberRangeUI extends DataRange
{
    /**
     * Type of UI config object
     *
     * @var string
     */
    const TYPE = 'NumberRangeUI';

    /**
     * Sets the format for numbers in the control.
     *
     * @access public
     * @param  array $formatConfig
     * @return \Khill\Lavacharts\Configs\UIs\NumberRangeUI
     */
    public function format($formatConfig)
    {
        return $this->setOption(__FUNCTION__, new NumberFormat($formatConfig));
    }
}
