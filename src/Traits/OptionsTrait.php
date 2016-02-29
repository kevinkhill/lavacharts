<?php

namespace Khill\Lavacharts\Traits;

use Khill\Lavacharts\Configs\Options;

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
     * @access public
     * @since  3.1.0
     * @param \Khill\Lavacharts\Configs\Options $options
     */
    public function setOptions(Options $options)
    {
        return $this->options;
    }

    /**
     * Retrieves the Options object from the chart.
     *
     * @access public
     * @since  3.1.0
     * @return \Khill\Lavacharts\Configs\Options
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * JSON string representing the Options object.
     *
     * @access public
     * @since  3.1.0
     * @return \Khill\Lavacharts\Configs\Options
     */
    public function getOptionsJson()
    {
        return json_encode($this->options);
    }
}
