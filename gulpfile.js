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
browserify = require('browserify'),
//  babelify = require('babelify')
      argv = require('yargs').array('browsers').argv;

var pkg = require('./package.json');

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

gulp.task('browserify', function (done) {
    var bundleStream = browserify(pkg.config.entry).bundle();

    bundleStream
        .pipe(source('lava.min.js'))
        .pipe(buffer())
        //.pipe(sourcemaps.init({loadMaps: true}))
        .pipe(uglify())
        //.pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('./javascript/dist'))
});

gulp.task('jshint', function (done) {
    return gulp.src('./javascript/**/*.js')
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

gulp.task('php:cs', function (done) {
    sh('./vendor/bin/phpcs -n --standard=PSR2 ./src ./tests');
});

gulp.task('php:fix', function (done) {
    sh('./vendor/bin/phpcbf -n --standard=PSR2 ./src ./tests');
});

gulp.task('js', ['jshint', 'browserify'])

gulp.task('watch', ['js'], function() {
    gulp.watch('./javascript/**/*.js', [
        'js'
    ]);
});
