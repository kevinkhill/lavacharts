<?php

namespace Khill\Lavacharts\Support\Contracts;

use Khill\Lavacharts\Support\Options;

/**
 * Arrayable Interface
 *
 * Provides common methods for working with DataTables.
 *
 *
 * @package   Khill\Lavacharts\Support\Contracts
 * @since     3.2.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
interface Customizable
{
    /**
     * Retrieve the instance Options.
     *
     * @return Options
     */
    public function getOptions();
}
