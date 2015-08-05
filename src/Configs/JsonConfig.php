<?php

namespace Khill\Lavacharts\Configs;

use \Khill\Lavacharts\Configs\Options;
use \Khill\Lavacharts\Exceptions\InvalidConfigProperty;

class JsonConfig implements \JsonSerializable
{
    /**
     * Allowed options to set for the UI.
     *
     * @var \Khill\Lavacharts\Configs\Options
     */
    protected $options;

    public function __construct(Options $options, $config = [])
    {
        $this->options = $options;

        if (is_array($config) === true && empty($config) === false) {
            $this->parseConfig($config);
        }
    }

    /**
     * Get the value of a set option via magic method through UI.
     *
     * @param  string $option Name of option.
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     * @return mixed
     */
    public function __get($option)
    {
        return $this->options->get($option);
    }

    private function parseConfig($config)
    {
        foreach ($config as $option => $value) {
            if ($this->options->hasOption($option) === false) {
                throw new InvalidConfigProperty(
                    get_class(), //TODO: static type class name?
                    __FUNCTION__,
                    $option,
                    $this->options->getDefaults()
                );
            }

            call_user_func([$this, $option], $value);
        }
    }

    /**
     * Sets the value of an option.
     *
     * Used internally to check the values for their respective types and validity.
     *
     * @param  string $option Option to set.
     * @param  mixed $value Value of the option.
     * @return self
     */
    protected function setOption($option, $value)
    {
        $this->options->set($option, $value);

        return $this;
    }

    /**
     * Gets the Options object for the JsonConfig
     *
     * @return \Khill\Lavacharts\Configs\Options
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Custom serialization of the JsonConfig object.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->options->getValues();
    }
}
