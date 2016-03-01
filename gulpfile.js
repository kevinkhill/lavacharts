  var gulp = require('gulp'),
       log = require('gulp-util').log,
     spawn = require('child_process').spawn,
      exec = require('child_process').exec,
        sh = require('sh'),
      bump = require('gulp-bump'),
    jshint = require('gulp-jshint'),
    uglify = require('gulp-uglify'),
    rename = require('gulp-rename'),
   replace = require('gulp-replace'),
sourcemaps = require('gulp-sourcemaps'),
    source = require('vinyl-source-stream'),
    buffer = require('vinyl-buffer'),
      argv = require('yargs').array('browsers').argv;

var pkg = require('./package.json');


gulp.task('default', [
    'jshint',
    'browserify'
]);

gulp.task('browserify', function (done) {
    function bundle() {
        b.bundle()
         .pipe(source('lava.js'))
         .pipe(gulp.dest('./javascript/dist'));
    }

    var b = require('browserify')({
        entries: [pkg.config.entry],
        cache: {},
        packageCache: {},
        plugin: [require('watchify')]
    });

    b.on('log', function (msg) {
        log(msg);
    });

    b.on('update', bundle);

    bundle();
});

gulp.task('jshint', function (done) {
    return gulp.src('./javascript/src/**/*.js')
               .pipe(jshint());
});

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

gulp.task('php:cs', function (done) {
    sh('./vendor/bin/phpcs -n --standard=PSR2 ./src ./tests');
});

gulp.task('php:fix', function (done) {
    sh('./vendor/bin/phpcbf -n --standard=PSR2 ./src ./tests');
});
