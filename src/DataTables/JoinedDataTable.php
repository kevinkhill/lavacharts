<?php

namespace Khill\Lavacharts\DataTables;

use Khill\Lavacharts\Support\Buffer;
use Khill\Lavacharts\Support\Contracts\DataInterface;
use Khill\Lavacharts\Support\Contracts\Javascriptable;
use Khill\Lavacharts\Support\Traits\HasOptionsTrait as HasOptions;
use Khill\Lavacharts\Support\Traits\ToJavascriptTrait as ToJavascript;

class JoinedDataTable implements DataInterface, Javascriptable
{
    use HasOptions, ToJavascript;

    /**
     * Default options for joining two DataTables
     */
    const DEFAULT_JOIN_OPTIONS = [
        'joinMethod' => 'full',
        'keys'       => [[0,0]],
        'dt1Columns' => [1],
        'dt2Columns' => [1],
    ];

    /**
     * Array of DataTables to join.
     *
     * @var DataTable[]
     */
    private $tables;

    /**
     * JoinedDataTable constructor.
     *
     * @param DataTable $data1
     * @param DataTable $data2
     */
    public function __construct(DataTable $data1, DataTable $data2, array $options = [])
    {
        $this->initOptions($options);

        $this->options->merge(self::DEFAULT_JOIN_OPTIONS);

        $this->options->set('interpolateNulls', true);

        $this->tables = [$data1, $data2];
    }

    /**
     * Define how the class will be cast to javascript source when
     * the extending class is treated like a string.
     *
     * @return string
     */
    public function toJsDataTable()
    {
        return $this->toJavascript();
    }

    /**
     * Get the format string that will be used by sprintf and toJson()
     * to convert the extending class to javascript.
     *
     * @return string
     */
    public function getJavascriptFormat()
    {
        return 'google.visualization.data.join(%s, %s, "%s", %s, %s, %s)';
    }

    /**
     * Return an array of arguments to pass to the format string provided
     * by getJavascriptFormat().
     *
     * These variables will be used with vsprintf, and the format string
     * to convert the extending class to javascript.
     *
     * @return array
     */
    public function getJavascriptSource()
    {
        return array_merge(
            $this->tables,
            [
                $this->options->joinMethod,
                json_encode($this->options->keys),
                json_encode($this->options->dt1Columns),
                json_encode($this->options->dt2Columns),
            ]
        );
    }
}
