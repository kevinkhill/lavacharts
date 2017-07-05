<?php

namespace Khill\Lavacharts\DataTables;

use Khill\Lavacharts\Javascript\JavascriptSource;
use Khill\Lavacharts\Support\Contracts\DataInterface;

class JoinedDataTable extends JavascriptSource implements DataInterface
{
    /**
     * Array of joined tables
     *
     * @var DataTable[]
     */
    private $tables;

    /**
     * JoinedDataTable constructor.
     *
     * @param \Khill\Lavacharts\Support\Contracts\DataInterface $data1
     * @param \Khill\Lavacharts\Support\Contracts\DataInterface $data2
     */
    public function __construct(DataInterface $data1, DataInterface $data2)
    {
        $this->tables[] = $data1->getDataTable();
        $this->tables[] = $data2->getDataTable();
    }

    /**
     * Define how the class will be cast to javascript source when
     * the extending class is treated like a string.
     *
     * @return string
     */
    public function toJavascript()
    {
        return sprintf($this->getFormatString(), $this->tables[0], $this->tables[1]);
    }

    /**
     * Return a format string that will be used by sprintf to convert the
     * extending class to javascript.
     *
     * @return string
     */
    public function getFormatString()
    {
        return 'google.visualization.data.join(%s, %s, "full", [[0, 0]], [1], [1])';
    }
}
