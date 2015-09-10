<?php

namespace Khill\Lavacharts\Configs\UIs;

use \Khill\Lavacharts\Options;
use \Khill\Lavacharts\DataTables\Formats\DateFormat;

/**
 * DateRangeUI Object
 *
 * Customization for DateRange Filters in Dashboards.
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
class DateRangeUI extends DataRange
{
    /**
     * Type of UI object
     *
     * @var string
     */
    const TYPE = 'DateRangeUI';

    /**
     * Sets the format for dates in the control.
     *
     * @access public
     * @param  array $formatConfig
     * @return \Khill\Lavacharts\Configs\UIs\DateRangeUI
     */
    public function format($formatConfig)
    {
        return $this->setOption(__FUNCTION__, new DateFormat($formatConfig));
    }
}
