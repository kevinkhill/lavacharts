<?php

namespace Khill\Lavacharts\DataTables\Columns;

use Khill\Lavacharts\DataTables\Formats\Format;
use Khill\Lavacharts\Exceptions\InvalidColumnRole;
use Khill\Lavacharts\Exceptions\InvalidColumnType;
use Khill\Lavacharts\Support\AbstractBuilder;
use Khill\Lavacharts\Support\Customizable;
use Khill\Lavacharts\Values\Role;
use Khill\Lavacharts\Support\StringValue;

/**
 * ColumnBuilder Class
 *
 * The ColumnBuilder is used to create new columns.
 *
 *
 * @package       Khill\Lavacharts\DataTables\Columns
 * @since         3.2.0
 * @author        Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link          http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link          http://lavacharts.com                   Official Docs Site
 * @license       http://opensource.org/licenses/MIT      MIT
 *
 * @method setType($id)
 * @method setLabel($label)
 * @method setFormat($format)
 * @method setRole($role)
 * @method setOptions($options)
 * @method Column build()
 */
class ColumnBuilder extends AbstractBuilder
{
    /**
     * Get built Column.
     *
     * @return Column
     */
    public function getColumn()
    {
        return $this->build();
    }

    /**
     * {@inheritdoc}
     */
    protected function configureParameters()
    {
        // Order matters since these correspond to the order of construction parameters
        return [
            'type'    => '',
            'label'   => '',
            'format'  => null,
            'role'    => null,
            'options' => [],
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getObjectFqcn()
    {
        return Column::class;
    }
}
