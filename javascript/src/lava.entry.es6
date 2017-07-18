import { LavaJs } from './lava/Lava.es6';
import { addEvent, domLoaded } from './lava/Utils.es6';

/**
 * Assign the Lava.js module to the window and
 * let $lava be an alias to the module.
 */
let $lava = window.lava = new LavaJs();

/**
 * Once the DOM has loaded...
 */
domLoaded().then(() => {
    /**
     * Adding the resize event listener for redrawing charts if
     * the option responsive is set to true.
     */
    if ($lava.options.responsive === true) {
        let debounced = null;

        addEvent(window, 'resize', () => {
            let redraw = $lava.redrawAll.bind($lava);

            clearTimeout(debounced);

            debounced = setTimeout(() => {
                console.log('[lava.js] Window re-sized, redrawing...');

                redraw();
            }, $lava.options.debounce_timeout);
        });
    }

    if ($lava.options.auto_run === true) {
        $lava.run();
    }
});
