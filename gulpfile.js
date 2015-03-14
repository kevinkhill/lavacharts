var gulp = require('gulp'),
       _ = require('lodash'),
  notify = require('gulp-notify'),
    exec = require('child_process').exec,
     map = require('map-stream'),
 phpunit = require('/Users/kevin/projects/gulp-phpunit');


gulp.task('test', function (cb) {
    gulp.src('./configs/phpunit.xml').pipe(phpunit());
});

gulp.task('serve', function (cb) {
    runCmd('php ../../../artisan serve', cb);
});

gulp.task('check', function (cb) {
    runCmd('./vendor/bin/phpcs --standard=PSR2 ./src', cb);
});



function log (file, cb) {
  console.log(file.path);
  cb(null, file);
}

function runCmd (cmd, cb) {
    exec(cmd, function (err, stdout, stderr) {
        console.log(stdout);
        console.log(stderr);
        //cb(err);
    });
}

function testNotification(status, pluginName, override) {
    var options = {
        //hostname : 'localhost',
        title:   ( status == 'pass' ) ? 'Tests Passed' : 'Tests Failed',
        message: ( status == 'pass' ) ? '\n\nAll tests have passed!\n\n' : '\n\nOne or more tests failed...\n\n',
        icon:    __dirname + '/node_modules/gulp-' + pluginName +'/assets/test-' + status + '.png'
    };

    options = _.merge(options, override);

    return options;
}