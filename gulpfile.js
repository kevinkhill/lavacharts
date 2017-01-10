  var gulp = require('gulp'),
     gutil = require('gulp-util'),
      bump = require('gulp-bump'),
    jshint = require('gulp-jshint'),
    uglify = require('gulp-uglify'),
 streamify = require('gulp-streamify'),
    gulpif = require('gulp-if'),
   stylish = require('jshint-stylish'),
        fs = require('fs'),
         Q = require('q'),
   replace = require('gulp-replace'),
      argv = require('yargs').array('browsers').argv,
    source = require('vinyl-source-stream'),
browserify = require('browserify'),
  stripify = require('stripify'),
      exec = require('child_process').exec,
  execSync = require('child_process').execSync,
   phantom = require('gulp-phantom'),
   connect = require('gulp-connect-php'),
  watchify = require('watchify');

var pkg = require('./package.json');

function compile(prod, watch) {
    var bundler = watchify(browserify({
        debug: true,
        entries: [pkg.config.entry],
        cache: {},
        packageCache: {}
    }));

    if (prod) {
        bundler.transform('stripify');
    }

    function rebundle() {
        return bundler.bundle()
            .on('error', function(err){
                if (err instanceof SyntaxError) {
                    gutil.log(gutil.colors.red('Syntax Error'));
                    console.log(err.message);
                    // console.log(err.filename+":"+err.loc.line);
                    console.log(err.codeFrame);
                } else {
                    gutil.log(gutil.colors.red('Error'), err.message);
                }
                this.emit('end');
            })
            .pipe(source('lava.js'))
            .pipe(gulpif(prod, streamify(uglify())))
            .pipe(gulp.dest('javascript/dist'));
    }

    if (watch) {
        bundler.on('update', function() {
            gutil.log(gutil.colors.green('-> bundling...'));

            rebundle();
        });
    }

    return rebundle();
}

function getChartTypes(callback) {
    exec('php ./tests/Examples/chartTypes.php', function (error, stdout, stderr) {
        console.log(stderr);

        var charts = eval(stdout);

        callback(charts);
    });
}

function renderChart(chartType, callback) {
    const phantom = './node_modules/.bin/phantomjs';
    const renderScript = './javascript/phantomjs/render.js';

    console.log('Starting render of ' + chartType);

    exec([phantom, renderScript, chartType].join(' '), callback);
}

function phpServer(router, callback) {
    const base = './tests/Examples/';

    connect.server({
        base: base,
        port: 8080,
        ini: base + 'php.ini',
        router: base + router
    }, callback || function(){});
}

gulp.task('watch',   function() { return compile(false, true)  });
gulp.task('build',   function() { return compile(false, false) });
gulp.task('release', function() { return compile(true,  false) });

gulp.task('default', ['watch']);

gulp.task('charts', function() {
    getChartTypes(function (charts) {
        console.log(charts);
    });
});

gulp.task('demos', function() {
    phpServer('demo.php');
});

gulp.task('render', function() {
    phpServer('renderer.php', function() {
        getChartTypes(function (charts) {
            renders = [];

            charts.forEach(function (chart) {
                renderChart(chart, function (error, stdout, stderr) {
                    var deferred = Q.defer();

                    if (error) {
                        deferred.reject(new Error(error));
                    } else {
                        deferred.resolve(''+stdout);
                        console.log(''+stdout)
                    }

                    renders.push(deferred);
                });
            });

            Q.allSettled(renders)
            .then(function (results) {
                results.forEach(function (result) {
                    if (result.state === "fulfilled") {
                        console.log(result.value);
                    } else {
                        console.log(result.reason);
                    }
                });
            }).then(function () {
                connect.closeServer(function() {
                    console.log('Finished renders.')
                });
            });
        });
    }, function() {
        console.log('server');
    });
});

gulp.task('phantom', function() {
    gulp.src("./javascript/phantomjs/render.js")
        .pipe(phantom({
            ext: json
        }))
        .pipe(gulp.dest("./data/"));
});

gulp.task('jshint', function (done) {
    gulp.src('./javascript/src/**/*.js')
        .pipe(jshint())
        .pipe(jshint.reporter(stylish));
});

gulp.task('bump', function (done) { //-v=1.2.3
    var version = argv.v;
    var minorVersion = version.slice(0, -2);

    gulp.src('./package.json')
        .pipe(bump({version:argv.v}))
        .pipe(gulp.dest('./'));

    gulp.src(['./README.md', './.travis.yml'])
        .pipe(replace(/("|=|\/|-)[0-9]+\.[0-9]+/g, '$1'+minorVersion))
        .pipe(gulp.dest('./'));
});
