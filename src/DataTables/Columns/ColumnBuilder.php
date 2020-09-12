<?php

namespace Khill\Lavacharts\DataTables\Columns;

use Khill\Lavacharts\Support\AbstractBuilder;

/**
 * ColumnBuilder Class
 *
 * The ColumnBuilder is used to create new columns.
 *
 *
 * @package       Khill\Lavacharts\DataTables\Columns
 * @since         4.0.0
 * @author        Kevin Hill <kevinkhill@gmail.com>
 * @copyright 2020 Kevin Hill
 * @link          http://github.com/kevinkhill/lavacharts GitHub Repository
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
