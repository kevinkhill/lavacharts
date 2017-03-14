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
     spawn = require('child_process').spawn,
  execSync = require('child_process').execSync,
   phantom = require('gulp-phantom'),
   connect = require('gulp-connect-php'),
  watchify = require('watchify');

var renderOutputDir = './phantomjs/renders';

function compile(prod, watch) {
    var bundler = browserify({
        debug: true,
        entries: ['./src/lava.entry.js'],
        cache: {},
        packageCache: {}
    });

    if (watch) {
        bundler = watchify(bundler);
    }

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
            .pipe(gulp.dest('dist'));
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
    exec('php ../tests/Examples/chartTypes.php', function (error, stdout, stderr) {
        console.log(stderr);

        var charts = eval(stdout);

        callback(charts);
    });
}

function renderChart(type, callback) {
    const phantom = './node_modules/.bin/phantomjs';
    const renderScript = './phantomjs/render.js';

    console.log('[' + type + '] Launching phantom.');

    //return exec([phantom, renderScript, type].join(' '), callback);
    return spawn(phantom, [renderScript, type]);
}

function phpServer(router, port, callback) {
    const base = '../tests/Examples/';

    connect.server({
        base: base,
        port: port || 8080,
        ini: base + 'php.ini',
        router: base + router
    }, callback || function(){});
}

function phpServerEnd(done) {
    connect.closeServer(function() {
        done();
    });
}

gulp.task('default', ['build']);

gulp.task('watch',   function() { return compile(false, true)  });
gulp.task('build',   function() { return compile(false, false) });
gulp.task('release', function() { return compile(true,  false) });

gulp.task('charts', function() {
    getChartTypes(function (charts) {
        console.log(charts);
    });
});

gulp.task('demos', function() {
    phpServer('demo.php', process.env.PORT || 6000);
});

gulp.task('render', function (done) {
    phpServer('renderer.php', 5000, function() {
        var chart    = 'PieChart';
        var renderer = renderChart(chart);

        renderer.stdout.on('data', function (data) {
            console.log('['+chart+'] ' + data);
        });

        renderer.on('close', function (code) {
            const chartPath = renderOutputDir+'/'+chart+'.png';

            if (fs.existsSync(chartPath)) {
                execSync('convert ' + chartPath + ' -trim +repage ' + chartPath);

                console.log('[' + chart + '] Successfully Cropped.');
            } else {
                console.log('[' + chart + '] ' + chartPath + ' not found.');
            }

            phpServerEnd(done);
        });
    });
});

gulp.task('phantom', function() {
    gulp.src("./phantomjs/render.js")
        .pipe(phantom({
            ext: json
        }))
        .pipe(gulp.dest("./data/"));
});

gulp.task('jshint', function (done) {
    return gulp.src('./src/**/*.js')
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
