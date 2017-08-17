/* jshint undef: true */
/* globals module, require, phantom, window */
"use strict";

var resemble = require('node-resemble-js');

var rendersDir = './javascript/phantomjs/renders/';
var args = process.argv;
var chart = args[2];

resemble(rendersDir + chart + '.png').compareTo(rendersDir + chart + '.png').onComplete(function (data) {
    //console.log(data);

    if (Number(data.misMatchPercentage) <= 0.01) {
        console.log('Pass!');
    } else {
        console.log('Fail!');
    }
});
