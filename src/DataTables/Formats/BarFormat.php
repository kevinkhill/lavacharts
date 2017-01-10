<?php

namespace Khill\Lavacharts\DataTables\Formats;

/**
 * Class BarFormat
 *
 * Adds a colored bar to a numeric cell indicating whether the cell value
 * is above or below a specified base value.
 *
 *
 * @package    Khill\Lavacharts\DataTables\Formats
 * @since      3.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2017, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT      MIT
 * @see        https://developers.google.com/chart/interactive/docs/reference#barformatter
 */
class BarFormat extends Format
{
    /**
     * Type of format object
     *
     * @var string
     */
    const TYPE = 'BarFormat';
}
