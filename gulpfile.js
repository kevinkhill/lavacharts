var gulp = require('gulp'),
      sh = require('sh'),
    argv = require('yargs').argv,
    bump = require('gulp-bump'),
 replace = require('gulp-replace');

gulp.task('test', function (cb) {
  sh('./vendor/bin/phpunit -c configs/phpunit.xml');
});

gulp.task('serve', function (cb) {
  sh('php ../../../artisan serve');
});

gulp.task('check', function (cb) {
  sh('./vendor/bin/phpcs --standard=PSR2 ./src');
});

gulp.task('bump', function (cb) {
  var version = argv.v;
  var minorVersion = version.slice(0, -2);

  gulp.src('./package.json')
      .pipe(bump({version:argv.v}))
      .pipe(gulp.dest('./'));

  gulp.src('./README.md')
      .pipe(replace(/("|=|\/|-)[0-9]+\.[0-9]+/g, '$1'+minorVersion))
      .pipe(gulp.dest('./'));
});
