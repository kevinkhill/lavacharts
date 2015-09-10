<?php

namespace Khill\Lavacharts\Configs\UIs;

use \Khill\Lavacharts\Options;
use \Khill\Lavacharts\Exceptions\InvalidConfigValue;

/**
 * StringUI Object
 *
 * Customization for String filters in dashboards
 *
 * @package    Khill\Lavacharts
 * @subpackage Configs\UIs
 * @since      3.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class StringUI extends UI
{
    /**
     * Type of UI object
     *
     * @var string
     */
    const TYPE = 'StringUI';

    /**
     * Default options available.
     *
     * @var array
     */
    private $stringDefaults = [
        'realtimeTrigger'
    ];

    /**
     * Builds a new StringUI Object
     *
     * @param  array $config
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function __construct($config = [])
    {
        $options = new Options($this->defaults);
        $options->extend($this->stringDefaults);

        parent::__construct($options, $config);
    }


    /**
     * Whether the control should match any time a key is pressed or only when
     * the input field 'changes' (loss of focus or pressing the Enter key).
     *
     * @param  string $realtimeTrigger
     * @return \Khill\Lavacharts\Configs\UIs\StringUI
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function realtimeTrigger($realtimeTrigger)
    {
        return $this->setBoolOption(__FUNCTION__, $realtimeTrigger);
    }
}
