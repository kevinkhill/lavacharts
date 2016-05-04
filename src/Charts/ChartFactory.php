<?php

namespace Khill\Lavacharts\Charts;

use Khill\Lavacharts\Builders\ChartBuilder;
use Khill\Lavacharts\Exceptions\InvalidDataTable;

/**
 * ChartFactory Class
 *
 * Used for creating new charts and removing the need for the main Lavacharts
 * class to handle the creation.
 *
 *
 * @category  Class
 * @package   Khill\Lavacharts\Charts
 * @since     3.0.5
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2016, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
class ChartFactory
{
    /**
     * Create new chart from type with DataTable and config passed
     * from the main Lavacharts class.
     *
     * @param  string $type Type of chart to create.
     * @param  array  $args Passed arguments from __call in Lavacharts.
     * @return \Khill\Lavacharts\Charts\Chart
     * @throws \Khill\Lavacharts\Exceptions\InvalidChartType
     * @throws \Khill\Lavacharts\Exceptions\InvalidDataTable
     */
    public static function create($type, $args)
    {
        if (isset($args[1]) === false) {
            throw new InvalidDataTable;
        }

        $builder = new ChartBuilder;

        $builder->setType($type)
                ->setLabel($args[0])
                ->setDatatable($args[1]);

        if (isset($args[2])) {
            if (is_string($args[2])) {
                $builder->setElementId($args[2]);
            }

            if (is_array($args[2])) {
                if (array_key_exists('elementId', $args[2])) {
                    $builder->setElementId($args[2]['elementId']);
                    unset($args[2]['elementId']);
                }

                if (array_key_exists('png', $args[2])) {
                    $builder->setPngOutput($args[2]['png']);
                    unset($args[2]['png']);
                }

                $builder->setOptions($args[2]);
            }
        }

        if (isset($args[3])) {
            $builder->setElementId($args[3]);
        }

        return $builder->getChart();
    }

    /**
     * Returns an array of supported chart types.
     *
     * @access public
     * @since  3.0.5
     * @return array
     */
    public static function getChartTypes()
    {
        $types = [];

        foreach (new \IteratorIterator(new \FilesystemIterator(__DIR__)) as $file) {
            $filename = $file->getFilename();

            if (!in_array($filename, ['Chart.php', 'ChartFactory.php'])) {
                $types[] = rtrim($filename, '.php');
            }
        }

        return $types;
    }

    /**
     * Returns the array of supported chart types.
     *
     * @access public
     * @since  3.0.5
     * @param  string $type Type of chart to isNonEmpty.
     * @return bool
     */
    public static function isValidChart($type)
    {
        return in_array($type, self::getChartTypes(), true);
    }
}
