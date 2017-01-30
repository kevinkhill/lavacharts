<?php

namespace Khill\Lavacharts\DataTables\Cells;

use Khill\Lavacharts\Exceptions\InvalidParamType;
use Khill\Lavacharts\Support\Customizable;

/**
 * DataCell Object
 *
 * Holds the information for a data point
 *
 * @package   Khill\Lavacharts\DataTables\Cells
 * @since     3.0.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
class NullCell extends Cell
{
    /**
     * Create a new NullCell
     */
    public function __construct()
    {
        parent::__construct(null);
    }
}

