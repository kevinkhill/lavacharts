<?php namespace Khill\Lavacharts\Traits;

use \Khill\Lavacharts\Configs\Annotation;

trait AnnotationsTrait
{
    /**
     * Defines how chart annotations will be displayed.
     *
     * @param  Annotation $annotation
     * @return Chart
     */
    public function annotations(Annotation $annotation)
    {
        return $this->addOption($annotation->toArray(__FUNCTION__));
    }
}
