var gulp = require('gulp'),
       _ = require('lodash'),
  notify = require('gulp-notify'),
    exec = require('child_process').exec,
     map = require('map-stream'),
 phpunit = require('/Users/kevin/projects/gulp-phpunit');


gulp.task('test', function (cb) {
    gulp.src('./configs/phpunit.xml')
        .pipe(phpunit('', {colors:true}));
});

gulp.task('serve', function (cb) {
    runCmd('php ../../../artisan serve', cb);
});

gulp.task('check', function (cb) {
    runCmd('./vendor/bin/phpcs --standard=PSR2 ./src', cb);
});


function runCmd (cmd, cb) {
    exec(cmd, function (err, stdout, stderr) {
        console.log(stdout);
        console.log(stderr);
        //cb(err);
    });
}