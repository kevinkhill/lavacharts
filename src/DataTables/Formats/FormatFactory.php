<?php

namespace Khill\Lavacharts\DataTables\Formats;

use Khill\Lavacharts\Configs\Options;
use Khill\Lavacharts\Exceptions\InvalidFormatType;

/**
 * FormatFactory creates new format objects
 *
 * @package    Khill\Lavacharts\DataTables\Formats
 * @since      3.1.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2016, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class FormatFactory
{
    /**
     * Creates Format Objects
     *
     * @param  string $type Type of format to create.
     * @param  array  $config
     * @return \Khill\Lavacharts\DataTables\Formats\Format
     * @throws \Khill\Lavacharts\Exceptions\InvalidFormatType
     */
    public static function create($type, array $config)
    {
        $format = __NAMESPACE__ . '\\' . $type;

        if (class_exists($format) === false) {
            throw new InvalidFormatType($type);
        }

        $options = new Options($config);

        return new $format($options);
    }
}
