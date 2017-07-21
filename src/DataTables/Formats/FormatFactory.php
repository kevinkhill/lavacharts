<?php

namespace Khill\Lavacharts\DataTables\Formats;

use Khill\Lavacharts\Exceptions\InvalidFormatType;

/**
 * FormatFactory Class
 *
 * Used for creating new format objects.
 *
 *
 * @category  Class
 * @package   Khill\Lavacharts\DataTables\Formats
 * @since     4.0.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
class FormatFactory
{
    /**
     * Static method for creating new format objects.
     *
     * @param  string $type
     * @param  array  $options
     * @return Format
     * @throws \Khill\Lavacharts\Exceptions\InvalidFormatType
     */
    public static function create($type, array $options = [])
    {
        $format = __NAMESPACE__ . '\\' . $type;

        if (! class_exists($format)) {
            throw new InvalidFormatType($type);
        }

        return new $format($options);
    }
}
