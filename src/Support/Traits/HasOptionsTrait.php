<?php

namespace Khill\Lavacharts\Support\Traits;

use Khill\Lavacharts\Support\Options;

/**
 * Trait HasOptionsTrait
 *
 * When applied to a class, then the class is able to have configured options tracked by
 * an Options object.
 *
 *
 * @package   Khill\Lavacharts\Support\Traits
 * @since     3.2.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
trait HasOptionsTrait
{
    /**
     * Options for the class.
     *
     * @var Options
     */
    private $options;

    /**
     * Retrieves the Options object from the class.
     *
     * @return Options
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Sets the Options object for the class.
     *
     * @param array|Options $options
     */
    public function setOptions($options)
    {
        if ($options instanceof Options) {
            $this->options = $options;
        } else {
            $this->options = new Options($options);
        }
    }
}
