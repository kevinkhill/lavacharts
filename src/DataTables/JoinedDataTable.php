<?php

namespace Khill\Lavacharts\DataTables;

use Khill\Lavacharts\Support\Contracts\DataInterface;
use Khill\Lavacharts\Support\Contracts\JavascriptSource;
use Khill\Lavacharts\Support\Traits\HasOptionsTrait as HasOptions;
use Khill\Lavacharts\Support\Traits\ToJavascriptTrait as ToJavascript;

class JoinedDataTable implements DataInterface, JavascriptSource
{
    use HasOptions, ToJavascript;

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

        $this->options->set('interpolateNulls', true);

        $this->options->setIfNot('joinMethod', 'full');

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
        return 'google.visualization.data.join(%s, %s, "%s", [[0, 0]], [1], [1])';
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
        $sourceVars = $this->tables;

        array_push($sourceVars, $this->options->joinMethod);

        return $sourceVars;
    }
}
