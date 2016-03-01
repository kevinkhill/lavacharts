<?php

namespace Khill\Lavacharts\Traits;

trait PngOutputTrait
{
    /**
     * Chart output override.
     *
     * @var bool
     */
    private $pngOutput = false;

    /**
     * Sets the chart to be output as a PNG instead of SVG.
     *
     * @param  bool Sets the chart output override status.
     */
    public function setPngOutput($png)
    {
        $this->pngOutput = $png;
    }

    /**
     * Gets the chart output ovveride status.
     *
     * @return bool Returns the output override status.
     */
    public function getPngOutput()
    {
        return $this->pngOutput;
    }
}
