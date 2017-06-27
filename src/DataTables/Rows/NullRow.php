<?php

namespace Khill\Lavacharts\DataTables\Rows;

use Khill\Lavacharts\Exceptions\InvalidParamType;

/**
 * NullRow Object
 *
 * The null row object is used to add an empty row to the datatable.
 *
 *
 * @package   Khill\Lavacharts\DataTables\Rows
 * @since     3.0.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
class NullRow extends Row
{
    /**
     * Creates a new NullRow object
     *
     * @param  int $columnCount Number of null columns to create.
     * @throws \Khill\Lavacharts\Exceptions\InvalidParamType
     */
    public function __construct($columnCount)
    {
        if (is_int($columnCount) === false) {
            throw new InvalidParamType($columnCount, 'integer');
        }

        parent::__construct(array_fill(0, $columnCount, null));
    }
}
