<?php

namespace Khill\Lavacharts\Support\Traits;

use Khill\Lavacharts\Configs\Options;

/**
 * Class OptionsTrait
 *
 * Trait for allowing a class to carry an Options object, for configuring said class.
 *
 * @package    Khill\Lavacharts\Support\Traits
 * @since      3.1.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2016, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
trait OptionsTrait
{
     /**
     * Holds all the customization options
     *
     * @var \Khill\Lavacharts\Configs\Options
     */
    protected $options;

    /**
     * Retrieves the Options object from the chart.
     *
     * @param \Khill\Lavacharts\Configs\Options $options
     */
    public function setOptions(Options $options)
    {
        $this->options = $options;
    }

    /**
     * Retrieves the Options object from the chart.
     *
     * @return \Khill\Lavacharts\Configs\Options
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * JSON string representing the Options object.
     *
     * @return string
     */
    public function getOptionsJson()
    {
        return json_encode($this->options);
    }
}
