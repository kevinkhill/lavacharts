/* jshint undef: true, unused: true */
/* globals window, require */

/**
 * Lava.js entry point for Browserify
 */
(function(){
    "use strict";

    var window = this;
    var debounced = null;
    var debounceTimeout = 250;
    var bind = require('lodash').bind;
    var ready = require('document-ready');
    var addResizeEvent = require('./lava/Utils').addResizeEvent;

    window.lava = require('./lava/Lava.js');

    /**
     * Once the window is ready...
     */
    ready(function() {
        /**
         * Adding the resize event listener for redrawing charts.
         */
        addResizeEvent(function (event) {
            var redraw = bind(event.target.lava.redrawCharts, window.lava);

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
