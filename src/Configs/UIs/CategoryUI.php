<?php

namespace Khill\Lavacharts\Configs\UIs;

use \Khill\Lavacharts\Exceptions\InvalidConfigProperty;

class CategoryUI extends UI
{
    /**
     * Allowed options to set for the UI.
     *
     * @var \Khill\Lavacharts\Configs\Options
     */
    private $options;

    /**
     * Default options available.
     *
     * @var array
     */
    private $defaults = [
        'caption',
        'sortValues',
        'selectedValuesLayout',
        'allowNone',
        'allowMultiple',
        'allowTyping'
    ];

    public function __construct($config)
    {
        $this->options = new Options(parent::$defaults);
        $this->options->extend($this->defaults);

        parent::__construct($config);
    }
}
