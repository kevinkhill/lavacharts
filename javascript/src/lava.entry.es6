/* jshint browser:true */
/* globals __OPTIONS__:true */

import LavaJs from './lava/Lava.es6';
import { domLoaded } from './lava/Utils.es6';

/**
 * Assign the Lava.js module to the window and
 * let $lava be an alias to the module.
 */
window.lava = new LavaJs();

/**
 * If Lava.js was loaded from Lavacharts, the __OPTIONS__
 * placeholder will be a JSON object of options that
 * were set server-side.
 */
if (typeof __OPTIONS__ !== 'undefined') {
    window.lava.options = __OPTIONS__;
}

/**
 * If Lava.js was set to auto_run then once the DOM
 * is ready, rendering will begin.
 */
if (window.lava.options.auto_run === true) {
    domLoaded().then(() => {
        window.lava.run();
    });
}
