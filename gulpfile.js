var gulp = require('gulp'),
   spawn = require('child_process').spawn,
    exec = require('child_process').exec,
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
        'AreaChart',
        'LineChart',
        'TableChart',
        'Dashboard'
    ];

    var phpserver = spawn('php', ['-S', '127.0.0.1:8946', '-c', 'php.ini', 'router.php'], {cwd: 'tests/Examples'});

    var render = function (chart, callback) {
        var url = 'http://127.0.0.1:8946/' + chart,
         output = 'build/renders/' + chart + '.png';

        webshot(url, output, {
          renderDelay: 5000,
          errorIfJSException: true
        }, function (err) {
            if (err) { callback(err); }

            console.log('Rendered ' + chart);
            return callback();
        });
    };

    phpserver.on('data', function (data) {
        console.log(data);
    });

    phpserver.on('close', function (err) {
        console.log('Done');
    });

    async.forEach(charts, render, function (error) {
        if (error) { console.log(error); }

        console.log('Stopping PHP Server');
        phpserver.kill('SIGINT');
    });
});

gulp.task('php:test', function (done) {
    var test = spawn('./vendor/bin/phpunit', ['-c', 'configs/phpunit.xml']);

    test.on('data', function (data) {
        console.log(data);
    });
});

gulp.task('php:coverage', function (done) {
    sh('./vendor/bin/phpunit -c configs/phpunit.xml.coverage');
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
