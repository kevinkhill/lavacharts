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
 * @since     4.0.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright 2020 Kevin Hill
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
interface Customizable
{
    /**
     * Retrieve the Options instance
     *
     * @return Options
     */
    public function getOptions();
}
