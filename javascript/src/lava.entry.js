/* jshint undef: true, unused: true */
/* globals window, require */

/**
 * Lava.js entry point for Browserify
 */
(function(){
    "use strict";

    function addEvent (object, type, callback) {
        if (object == null || typeof(object) == 'undefined') return;
        if (object.addEventListener) {
            object.addEventListener(type, callback, false);
        } else if (object.attachEvent) {
            object.attachEvent("on" + type, callback);
        } else {
            object["on"+type] = callback;
        }
    }

    var ready = require('document-ready');

    this.lava = require('./lava/Lava.js');

    ready(function() {
        /**
         * Adding the resize event listener for redrawing charts.
         */
        addEvent(window, 'resize', window.lava.redrawHandler);

        /**
         * Let's go!
         */
        window.lava.init();
        window.lava.run();
    });
}.apply(window));
