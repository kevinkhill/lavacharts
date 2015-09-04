<?php

namespace Khill\Lavacharts\Configs\UIs;

use \Khill\Lavacharts\Options;
use \Khill\Lavacharts\Exceptions\InvalidConfigValue;

class StringUI extends UI
{
    /**
     * Default options available.
     *
     * @var array
     */
    private $extDefaults = [
        'realtimeTrigger'
    ];

    public function __construct($config = [])
    {
        $options = new Options($this->defaults);
        $options->extend($this->extDefaults);

        parent::__construct($options, $config);
    }


    /**
     * Whether the control should match any time a key is pressed or only when
     * the input field 'changes' (loss of focus or pressing the Enter key).
     *
     * @access public
     * @param  string $realtimeTrigger
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return self
     */
    public function realtimeTrigger($realtimeTrigger)
    {
        if (is_bool($realtimeTrigger) === false) {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'boolean'
            );
        }

        return $this->setOption(__FUNCTION__, $realtimeTrigger);
    }
}
