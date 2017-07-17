import bind from 'lodash/fp/bind';
import ready from 'document-ready';
import { LavaJs } from './lava/Lava';
import { addResizeEvent } from './lava/Utils';

let debounced = null;

/**
 * Assign the Lava.js module to the window and let $lava be an alias.
 */
let $lava = window.lava = new LavaJs;

/**
 * Adding the resize event listener for redrawing charts.
 */
addResizeEvent(function() {
    const redraw = bind($lava.redrawAll, $lava);

    console.log('[lava.js] Window resize detected.');

    clearTimeout(debounced);

    debounced = setTimeout(redraw, $lava.options.debounce_timeout);
});

/**
 * Once the window is ready...
 */
ready(function() {
    if ($lava.options.auto_run === true) {
        $lava.run();
    }
});
