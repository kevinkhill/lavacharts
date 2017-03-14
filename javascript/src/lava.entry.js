/* jshint undef: true, unused: true */
/* globals window, require */

/**
 * Lava.js entry point for Browserify
 */
(function(){
    "use strict";

    var ready = require('document-ready');

    this.lava = require('./lava/Lava.js');

    ready(function() {
        /**
         * Adding the resize event listener for redrawing charts.
         */
        window.addEventListener('resize', window.lava.redrawCharts);

        /**
         * Let's go!
         */
        window.lava.init();
        window.lava.run();
    });
}.apply(window));
