import bind from 'lodash/fp/bind';
import ready from 'document-ready';
import { LavaJs } from './lava/Lava.es6';
import { addResizeEvent } from './lava/Utils.es6';

/**
 * Assign the Lava.js module to the window and
 * let $lava be an alias to the module.
 */
let $lava = window.lava = new LavaJs;

/**
 * Once the window is ready...
 */
ready(function() {
    /**
     * Adding the resize event listener for redrawing charts if
     * the option responsive is set to true.
     */
    if ($lava.options.responsive === true) {
        let debounced = null;

        addResizeEvent(function () {
            const redraw = bind($lava.redrawAll, $lava);

            console.log('[lava.js] Window resize detected.');

            clearTimeout(debounced);

            debounced = setTimeout(redraw, $lava.options.debounce_timeout);
        });
    }

    if ($lava.options.auto_run === true) {
        $lava.run();
    }
});
