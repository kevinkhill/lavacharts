<?php namespace Khill\Lavacharts\Traits;

use \Khill\Lavacharts\Configs\Annotation;

trait AnnotationsTrait
{
    /**
     * Defines how chart annotations will be displayed.
     *
     * @param  \Khill\Lavacharts\Configs\Annotation $annotation
     * @return \Khill\Lavacharts\Charts\Chart
     */
    public function annotations(Annotation $annotation)
    {
        return $this->addOption($annotation->toArray(__FUNCTION__));
    }
}
