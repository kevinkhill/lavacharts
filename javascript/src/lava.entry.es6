/* jshint browser:true */
/* globals __OPTIONS__:true */

import LavaJs from './lava/Lava.es6';
import { domLoaded } from './lava/Utils.es6';

/**
 * Assign the Lava.js module to the window and
 * let $lava be an alias to the module.
 */
let $lava = window.lava = new LavaJs();

/**
 * Once the DOM has loaded...
 */
domLoaded().then(() => {
    if (typeof __OPTIONS__ !== 'undefined') {
        $lava.options = __OPTIONS__;
    }

    if ($lava.options.auto_run === true) {
        $lava.run(window);
    }
});
