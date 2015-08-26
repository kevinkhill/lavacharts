var gulp = require('gulp'),
   spawn = require('child_process').spawn,
      sh = require('sh'),
    bump = require('gulp-bump'),
  jshint = require('gulp-jshint'),
 replace = require('gulp-replace'),
    argv = require('yargs').array('browsers').argv;

gulp.task('karma', function (done) {
    var karma = require('karma');

    var server = new karma.Server({
        configFile: __dirname + '/configs/karma.conf.js',
        singleRun: argv.dev ? false : true
    }, function(exitStatus) {
        done(exitStatus ? "There are failing unit tests" : undefined);
    });

    server.start();
});


gulp.task('render', function (done) {gulp.src(['file.txt'])
    var webshot = require('webshot'),
          async = require('async');

    var charts = [
        'LineChart',
        'TableChart',
        'Dashboard'
    ];

    var phpserver = spawn('php', ['-S', '127.0.0.1:8946', '-t', 'tests/Examples'], {cwd: __dirname});

    phpserver.stdout.on('data', function (data) {
        console.log('[PHP]: ' + data);
    });

    phpserver.stderr.on('data', function (data) {
        console.error('[PHP]: ' + data);
    });

    phpserver.on('close', function (err) {
        console.log('[PHP]: Stopping Server...');
    });

    async.series([
        function (callback) {
            charts.forEach(function (chart) {
                gulp.src([__dirname + '/screenshot.js'])
                    .pipe(replace('{TYPE}', chart))
                    .pipe(gulp.dest(__dirname + '/build'));

                webshot('http://127.0.0.1:8946/index.php?chart=' + chart, 'build/renders/' + chart + '.png', function (err) {
                    //
                });
            });
        },
        function (callback) {
            phpserver.kill('SIGINT');
        }
    ]);
});

gulp.task('php:test', function (done) {
    sh('./vendor/bin/phpunit -c configs/phpunit.xml');
});

gulp.task('php:doc', function (done) {
    sh('./vendor/bin/sami.php update configs/sami.cfg.php');
});

gulp.task('php:cs', function (done) {
    sh('./vendor/bin/phpcs -n --standard=PSR2 ./src ./tests');
});

gulp.task('php:fix', function (done) {
    sh('./vendor/bin/phpcbf -n --standard=PSR2 ./src ./tests');
});

gulp.task('js:lint', function (done) {
    gulp.src('./javascript/lava.js')
        .pipe(jshint());
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
