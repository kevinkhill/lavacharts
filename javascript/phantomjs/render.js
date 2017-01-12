/* jshint undef: true */
/* globals module, require, phantom, window */
"use strict";

var page = require('webpage').create();
var args = require('system').args;

var renderOutputDir = './javascript/phantomjs/renders';
var url = 'http://127.0.0.1:5000/';
var chart = args[1];

page.onConsoleMessage = function(msg) {
    console.log(msg);
};

page.open(url + chart, function (status) {
    if (status !== "success") {
        console.log('Error loading page.');
    } else {
        console.log('Page loaded, waiting on chart to render.');

        page.onCallback = function (data) {
            page.render(renderOutputDir + '/' + chart + '.png');

            console.log('Saved to disk.');

            phantom.exit();

            //console.log('CALLBACK: ' + JSON.stringify(data));
            // Prints 'CALLBACK: { "hello": "world" }'
        };
    }
});
