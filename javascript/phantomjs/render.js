/* jshint undef: true */
/* globals module, require, phantom, window */
"use strict";

var page = require('webpage').create();
var system = require('system');

var renderOutputDir = './javascript/phantomjs/renders';
var url = 'http://127.0.0.1:8080/';
var args = system.args;
var chart = args[1];

function waitFor(testFx, onReady, timeOutMillis) {
    var maxtimeOutMillis = timeOutMillis ? timeOutMillis : 3000, //< Default Max Timout is 3s
        start = new Date().getTime(),
        condition = false,
        interval = setInterval(function() {
            if ( (new Date().getTime() - start < maxtimeOutMillis) && !condition ) {
                // If not time-out yet and condition not yet fulfilled
                condition = (typeof(testFx) === "string" ? eval(testFx) : testFx()); //< defensive code
            } else {
                if(!condition) {
                    // If condition still not fulfilled (timeout but condition is 'false')
                    console.log("'waitFor()' timeout");
                    phantom.exit(1);
                } else {
                    // Condition fulfilled (timeout and/or condition is 'true')
                    console.log("'waitFor()' finished in " + (new Date().getTime() - start) + "ms.");
                    typeof(onReady) === "string" ? eval(onReady) : onReady(); //< Do what it's supposed to do once the condition is fulfilled
                    clearInterval(interval); //< Stop this interval
                }
            }
        }, 500); //< repeat check every 250ms
};

page.onConsoleMessage = function(msg) {
    console.log(msg);
}

page.open(url + chart, function (status) {
    if (status !== "success") {
        console.log('Error loading page.');
    } else {
        console.log('Page loaded, waiting on Lavacharts');

        waitFor(function() {
            // Check in the page if a specific element is now visible
            var renderedChart = page.evaluate(function (e) {
                return document.querySelector(e).innerHTML;
            }, '.render');

            return renderedChart == '' ? false : true;
        }, function () {
            page.render(renderOutputDir + '/' + chart + '.png');

            console.log(chart + ' rendered and saved to disk.');

            phantom.exit();
        });
    }
});
