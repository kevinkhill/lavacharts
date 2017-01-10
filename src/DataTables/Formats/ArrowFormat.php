<?php

namespace Khill\Lavacharts\DataTables\Formats;

/**
 * Class ArrowFormat
 *
 * Adds an up or down arrow to a numeric cell, depending on whether the value
 * is above or below a specified base value. If equal to the base value, no arrow is shown.
 *
 *
 * @package    Khill\Lavacharts\DataTables\Formats
 * @since      3.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2017, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT      MIT
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
}
