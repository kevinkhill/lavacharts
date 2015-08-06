<?php

namespace Khill\Lavacharts\Configs;

use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Configs\Options;
use \Khill\Lavacharts\Exceptions\InvalidConfigValue;
use \Khill\Lavacharts\Exceptions\InvalidConfigProperty;

class JsonConfig implements \JsonSerializable
{
    /**
     * Allowed options to set for the UI.
     *
     * @var \Khill\Lavacharts\Configs\Options
     */
    protected $options;

    /**
     * Creates a new JsonConfig object
     *
     * @param  \Khill\Lavacharts\Configs\Options $options
     * @param  array                             $config
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     */
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

    /**
     * function for easy creation of exceptions
     * TODO: deprecate this
     */
    protected function invalidConfigValue($func, $type, $extra = '')
    {
        if (! empty($extra)) {
            throw new InvalidConfigValue(
                static::TYPE . '->' . $func,
                $type,
                $extra
            );
        } else {
            throw new InvalidConfigValue(
                static::TYPE . '->' . $func,
                $type
            );
        }
    }

    private function parseConfig($config)
    {
        foreach ($config as $option => $value) {
            if ($this->options->hasOption($option) === false) {
                throw new InvalidConfigProperty(
                    static::TYPE,
                    __FUNCTION__,
                    $option,
                    $this->options->getDefaults()
                );
            }

            call_user_func([$this, $option], $value);
        }
    }

    /**
     * Sets the value of a string option.
     *
     * @param  string $option Option to set.
     * @param  string $value Value of the option.
     * @param  array  $validValues Array of valid values
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @throws \Khill\Lavacharts\Exceptions\InvalidOption
     * @return self
     */
    protected function setStringOption($option, $value)
    {
        if (Utils::nonEmptyString($value) === false) {
            throw new InvalidConfigValue(
                static::TYPE . '->' . $option,
                'string'
            );
        }

        $this->options->set($option, $value);

        return $this;
    }

    /**
     * Sets the value of a string option from an array of choices.
     *
     * @param  string $option Option to set.
     * @param  string $value Value of the option.
     * @param  array  $validValues Array of valid values
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @throws \Khill\Lavacharts\Exceptions\InvalidOption
     * @return self
     */
    protected function setStringInArrayOption($option, $value, $validValues = [])
    {
        if (Utils::nonEmptyStringInArray($value, $validValues) === false) {
            throw new InvalidConfigValue(
                static::TYPE . '->' . $option,
                'string. Whose value is one of '.Utils::arrayToPipedString($validValues)
            );
        }

        $this->options->set($option, $value);

        return $this;
    }

    /**
     * Sets the value of an integer option.
     *
     * @param  string $option Option to set.
     * @param  int    $value Value of the option.
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @throws \Khill\Lavacharts\Exceptions\InvalidOption
     * @return self
     */
    protected function setIntOption($option, $value)
    {
        if (is_int($value) === false) {
            throw new InvalidConfigValue(
                static::TYPE . '->' . $option,
                'int'
            );
        }

        return $this->setOptions->set($option, $value);

        return $this;
    }


    /**
     * Sets the value of an boolean option.
     *
     * @param  string $option Option to set.
     * @param  bool   $value Value of the option.
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @throws \Khill\Lavacharts\Exceptions\InvalidOption
     * @return self
     */
    protected function setBoolOption($option, $value)
    {
        if (is_int($value) === false) {
            throw new InvalidConfigValue(
                static::TYPE . '->' . $option,
                'bool'
            );
        }

        return $this->setOptions->set($option, $value);

        return $this;
    }

    /**
     * Sets the value of an option.
     *
     * Used internally to check the values for their respective types and validity.
     * TODO: deprecate this
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
