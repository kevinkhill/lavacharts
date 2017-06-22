/* jshint undef: true, unused: true */
/* globals window, require */

/**
 * Lava.js entry point for Browserify
 */
(function(){
    "use strict";

    const LavaJs = require('./lava/Lava').LavaJs;

    let window = this;

    let debounced = null;
    let debounceTimeout = 250;
    let bind = require('lodash').bind;
    let ready = require('document-ready');
    let addResizeEvent = require('./lava/Utils').addResizeEvent;

    this.lava = new LavaJs;

    /**
     * Once the window is ready...
     */
    ready(function() {
        /**
         * Adding the resize event listener for redrawing charts.
         */
        addResizeEvent(function (event) {
            const redraw = bind(event.target.lava.redrawCharts, window.lava);

            console.log('Window resized, redrawing charts');

            clearTimeout(debounced);

            debounced = setTimeout(redraw, debounceTimeout);
        });

        /**
         * Let's go!
         */
        if (window.lava.options.auto_run === true) {
            window.lava.run();
        }
    });
}.apply(window));
