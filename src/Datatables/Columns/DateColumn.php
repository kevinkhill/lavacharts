<?php

namespace Khill\Lavacharts\Datatables\Columns;

use \Khill\Lavacharts\Values\Label;
use \Khill\Lavacharts\Formats\Format;

/**
 * DateColumn Object
 *
 * Date columns are for Carbon objects or strings representing dates in the Datatable.
 *
 *
 * @package    Lavacharts
 * @subpackage Datatables\Columns
 * @since      3.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class DateColumn extends Column
{
    /**
     * Type of column.
     *
     * @var string
     */
    const TYPE = 'date';

    /**
     * Creates a new column object.
     *
     * @access public
     * @param  string $label Label for the column.
     * @return self
     */
    public function __construct(Label $label=null)
    {
        parent::__construct($label);
    }

}
