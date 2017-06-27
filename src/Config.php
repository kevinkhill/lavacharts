<?php

namespace Khill\Lavacharts;

class Config
{
    /**
     * Returns the default configuration options for Lavacharts
     *
     * @return array
     */
    public static function getDefault()
    {
        return require(__DIR__.'/Laravel/config/lavacharts.php');
    }

    /**
     * Returns a list of the options that can be set.
     *
     * @return array
     */
    public static function getOptions()
    {
        $options = self::getDefault();

        return array_keys($options);
    }
}
