<?php

namespace Khill\Lavacharts\DataTables\Rows;

use Khill\Lavacharts\Exceptions\InvalidConfigValue;

/**
 * NullRow Object
 *
 * The null row object is used to add an empty row to the datatable.
 *
 *
 * @package    Khill\Lavacharts
 * @subpackage DataTables\Rows
 * @since      3.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class NullRow extends Row
{
    /**
     * Creates a new NullRow object
     *
     * @param  int $numOfCols Number of null columns to create.
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function __construct($numOfCols)
    {
        if (is_int($numOfCols) === false) {
            throw new InvalidConfigValue(
                'NullRow->__construct()',
                'int'
            );
        }

        parent::__construct(array_fill(0, $numOfCols, null));
    }
}
