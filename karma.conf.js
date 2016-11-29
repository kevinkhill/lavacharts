module.exports = function (config) {
    config.set({
        frameworks: ['jasmine','sinon'],
        files: [
            'node_modules/jasmine-sinon/lib/jasmine-sinon.js',
            'javascript/dist/lava.js',
            'javascript/tests/lava.spec.js'
        ],
        singleRun: false,
        reporters: ['dots'],
        port: 9876,
        colors: true,
        logLevel: config.LOG_ERROR,
        autoWatch: true,
        browsers: [(process.env.TRAVIS ? 'PhantomJS' : 'Chrome')]
    });
};
