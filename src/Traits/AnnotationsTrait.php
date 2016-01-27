<?php

namespace Khill\Lavacharts\Traits;

use \Khill\Lavacharts\Configs\Annotation;

trait AnnotationsTrait
{
    /**
     * Defines how chart annotations will be displayed.
     *
     * @param  array $annotationConfig
     * @return \Khill\Lavacharts\Charts\Chart
     */
    public function annotations($annotationConfig)
    {
        return $this->setOption(__FUNCTION__, new Annotation($annotationConfig));
    }
}
