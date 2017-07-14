/* jshint undef: true, unused: true */
/* globals window, require */

/**
 * Lava.js entry point for Browserify
 */
(function(){
    "use strict";

    const _ = require('lodash');
    const LavaJs = require('./lava/Lava').LavaJs;

    let window = this;
    let debounced = null;
    let debounceTimeout = 250;
    let ready = require('document-ready');
    let addResizeEvent = require('./lava/Utils').addResizeEvent;

    this.lava = new LavaJs;

    /**
     * Adding the resize event listener for redrawing charts.
     */
    addResizeEvent(function (event) {
        const redraw = _.bind(event.target.lava.redrawCharts, window.lava);

        console.log('[lava.js] Window resize detected.');

        clearTimeout(debounced);

        debounced = setTimeout(redraw, debounceTimeout);
    });

    /**
     * Once the window is ready...
     */
    ready(function() {
        /**
         * Let's go!
         */
        if (window.lava.options.auto_run === true) {
            window.lava.run();
        }
    });
}.apply(window));
