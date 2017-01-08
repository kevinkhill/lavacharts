/* jshint undef: true */
/* globals module, require, phantom, window */
"use strict";

var resemble = require('node-resemble-js');

var rendersDir = './javascript/phantomjs/renders/';
var args = process.argv;
var chart = args[2];

resemble(rendersDir + chart + '.png')
    .compareTo(rendersDir + chart + '2.png')
    .onComplete(function (data) {
        console.log(data);
    /*
     {
     misMatchPercentage : 100, // %
     isSameDimensions: true, // or false
     dimensionDifference: { width: 0, height: -1 }, // defined if dimensions are not the same
     getImageDataUrl: function(){}
     }
     */
});
