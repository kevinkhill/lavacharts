module.exports = function(config) {
    config.set({
        basePath: '..',

        // available frameworks: https://npmjs.org/browse/keyword/karma-adapter
        frameworks: [
            'jasmine',
            'detectBrowsers'
        ],

        // list of files / patterns to load in the browser
        files: [
            'lava.js',
            'lava.spec.js'
        ],

        // list of files to exclude
        exclude: [
        ],

        // preprocess matching files before serving them to the browser
        // available preprocessors: https://npmjs.org/browse/keyword/karma-preprocessor
        preprocessors: {
        },

        //CI Mode
        singleRun: true,

        plugins: [
            'karma-jasmine',
            'karma-detect-browsers',
            'karma-ie-launcher',
            'karma-chrome-launcher',
            'karma-firefox-launcher',
            'karma-phantomjs-launcher'
        ],

        // test results reporter to use
        // possible values: 'dots', 'progress'
        // available reporters: https://npmjs.org/browse/keyword/karma-reporter
        reporters: [
            'dots'
        ],

        // web server port
        port: 9876,

        // enable / disable colors in the output (reporters and logs)
        colors: true,

        // level of logging
        // possible values: config.LOG_DISABLE || config.LOG_ERROR || config.LOG_WARN || config.LOG_INFO || config.LOG_DEBUG
        logLevel: config.LOG_ERROR,

        // enable / disable watching file and executing tests whenever any file changes
        autoWatch: true,

        // start these browsers
        // available browser launchers: https://npmjs.org/browse/keyword/karma-launcher
        browsers: ['Chrome'],

        customLaunchers: {
            IE10: {
                base: 'IE',
                'x-ua-compatible': 'IE=EmulateIE10',
                flags: ['-extoff']
            },
            IE9: {
                base: 'IE',
                'x-ua-compatible': 'IE=EmulateIE9',
                flags: ['-extoff']
            },
            IE8: {
                base: 'IE',
                'x-ua-compatible': 'IE=EmulateIE8',
                flags: ['-extoff']
            }
        },

        // detectBrowsers Configuration
        detectBrowsers: {
            // post processing of browsers list
            // here you can edit the list of browsers used by karma
            postDetection: function(availableBrowser) {
                //Add IE Emulation
                var result = availableBrowser;

                if (availableBrowser.indexOf('IE')>-1) {
                    result.push('IE8');
                    result.push('IE9');
                    result.push('IE10');
                }

                //Remove PhantomJS if another browser has been detected
                if (availableBrowser.length > 1 && availableBrowser.indexOf('PhantomJS')>-1) {
                    var i = result.indexOf('PhantomJS');

                    if (i !== -1) {
                        result.splice(i, 1);
                    }
                }

                return result;
            }
        }
    });
};
