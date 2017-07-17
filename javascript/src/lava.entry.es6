/* jshint undef: true, unused: true */
/* globals window, require */

/**
 * Lava.js entry point for Browserify
 */
;(function(){
    "use strict";

    /**
     * The Lava.js module for all the heavy lifting.
     *
     * @type {LavaJs}
     */
    const LavaJs = require('./lava/Lava').LavaJs;

    /**
     * Assign needed variables for the browser.
     */
    let debounced = null;

    /**
     * Get needed modules and methods.
     */
    const bind = require('lodash/fp/bind');
    const ready = require('document-ready');
    const addResizeEvent = require('./lava/Utils').addResizeEvent;

    /**
     * Assign the Lava.js module to the window and let $lava be an alias.
     */
    let $lava = this.lava = new LavaJs;

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

}.apply(window)); // Set the closure scope "this" to the window
