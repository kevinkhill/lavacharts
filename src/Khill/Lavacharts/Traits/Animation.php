<?php namespace Khill\Lavacharts\Traits;

use Khill\Lavacharts\Utils;

trait Animation
{
    /**
     * Animation Easing
     *
     * The easing function applied to the animation. The following options are available:
     * 'linear' - Constant speed.
     * 'in' - Ease in - Start slow and speed up.
     * 'out' - Ease out - Start fast and slow down.
     * 'inAndOut' - Ease in and out - Start slow, speed up, then slow down.
     *
     * @param string $easing
     *
     * @return Chart
     */
    public function animationEasing($easing = 'linear')
    {
        $values = array(
            'linear',
            'in',
            'out',
            'inAndOut'
        );

        if (in_array($easing, $values)) {
            $this->easing = $easing;
        } else {
            $this->error('Invalid animationEasing value, must be (string) '.Utils::arrayToPipedString($values));
        }

        return $this;
    }

    /**
     * Animation Duration
     *
     * The duration of the animation, in milliseconds.
     *
     * @param mixed $duration
     *
     * @return Chart
     */
    public function animationDuration($duration)
    {
        if (is_int($duration) || is_string($duration)) {
            $this->duration = $this->_valid_int($duration);
        } else {
            $this->duration = 0;
        }

        return $this;
    }
}
