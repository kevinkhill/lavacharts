<?php namespace Khill\Lavacharts\Configs;
/**
 * configOptions Parent Object
 *
 * The base class for the individual configuration objects, providing common
 * functions to the child objects.
 *
 *
 * @author Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2013, KHill Designs
 * @link https://github.com/kevinkhill/Codeigniter-gCharts GitHub Repository Page
 * @link http://kevinkhill.github.io/Codeigniter-gCharts/ GitHub Project Page
 * @license http://opensource.org/licenses/MIT MIT
 */

use Khill\Lavacharts\Helpers\Helpers;

class configOptions
{
    /**
     * Output of the configOptions object.
     *
     * @var string
     */
    public $output = NULL;

    /**
     * Array of allowed key values for the configOptions child objects.
     *
     * @var array
     */
    public $options = NULL;

    /**
     * Class name without namespace
     *
     * @var string
     */
    private $className;

    /**
     * Builds the configOptions object.
     *
     * Passing an array of key value pairs will set the configuration for each
     * child object created from this master object.
     *
     * @param array Array of options.
     * @return \configOptions
     */
    public function __construct($config)
    {
        $this->className = Helpers::get_real_class($this);

        if(is_array($config))
        {
            foreach($config as $option => $value)
            {
                if(in_array($option, $this->options))
                {
                    $this->$option($value);
                } else {
                    $this->error('Ignoring "'.$option.'", not a valid configuration option.');
                }
            }
        } else {
            $this->type_error($this->className.'()', 'array', 'with valid keys as '.Helpers::array_string($this->options));
        }

        return $this;
    }

    /**
     * Adds an error message to the error log in the lavacharts object.
     *
     * @param string $msg
     */
    public function error($msg)
    {
        Lavacharts::_set_error($this->className, $msg);
    }

    /**
     * Adds an function/type error message to the error log in the lavacharts object.
     *
     * @param string Property in error
     * @param string Variable type
     * @param string Extra message to append to error
     */
    public function type_error($val, $type, $extra = FALSE)
    {
        $msg = sprintf(
            'Invalid value for %s, must be type (%s)',
            $val,
            $type
        );

        $msg .= $extra ? ' '.$extra.'.' : '.';

        $this->error($msg);
    }

    /**
     * Returns an array representation of the object.
     *
     * If passed a label, then the array will be returned with the label as the
     * key.
     *
     * Called with no label returns an array with the classname as the key.
     *
     * @return array
     */
    public function toArray($keyName = NULL)
    {
        $this->output = array();

        foreach($this->options as $option)
        {
            if(isset($this->$option))
            {
                $this->output[$option] = $this->$option;
            }
        }

        if(is_null($keyName))
        {
            return array($this->className => $this->output);
        } else {
            return array($keyName => $this->output);
        }
    }

    /**
     * Same as toArray, but without the class name as a key to being multi-dimension.
     *
     * @return array Array of the options of the object.
     */
    public function values()
    {
        $this->output = array();

        foreach($this->options as $option)
        {
            if(isset($this->$option))
            {
                $this->output[$option] = $this->$option;
            }
        }

        return $this->output;
    }

}

/* End of file configOptions.php */
/* Location: ./lavacharts/configs/configOptions.php */
