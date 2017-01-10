<?php

namespace Khill\Lavacharts\DataTables\Formats;

/**
 * NumberFormat Class
 *
 * Formats number values in the datatable for display.
 * Added to columns during column definition.
 *
 *
 * @package    Khill\Lavacharts\DataTables\Formats
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2017, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT      MIT
 * @see        https://developers.google.com/chart/interactive/docs/reference#numberformatter
 */
class NumberFormat extends Format
{
    /**
     * Type of format object
     *
     * @var string
     */
    const TYPE = 'NumberFormat';
}
