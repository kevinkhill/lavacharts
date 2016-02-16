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

gulp.task('php:test', function (done) {
    var test = spawn('./vendor/bin/phpunit', ['-c', 'configs/phpunit.xml']);

    test.on('data', function (data) {
        log(data);
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

gulp.task('js:build', function (done) {
    return browserify('javascript/lavacharts.js')
        .bundle()
        .pipe(source('lavacharts.js'))
        .pipe(buffer())
        .pipe(sourcemaps.init())
        //.pipe(uglify())
        .pipe(sourcemaps.write())
        .pipe(rename({
            extname: ".min.js"
        }))
        .pipe(gulp.dest('javascript'))
});

gulp.task('js:lint', function (done) {
    return gulp.src('./javascript/lava.js')
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

gulp.task('js', ['js:lint', 'js:build'])

gulp.task('watch', ['js'], function() {
    gulp.watch('./javascript/lava.js', [
        'js'
    ]);
});
