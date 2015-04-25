var gulp = require('gulp'),
      sh = require('sh'),
    argv = require('yargs').argv,
    bump = require('gulp-bump'),
  uglify = require('gulp-uglify'),
  rename = require('gulp-rename'),
   watch = require('gulp-watch'),
  jshint = require('gulp-jshint'),
 replace = require('gulp-replace'),
 stylish = require('jshint-stylish');


gulp.task('test', function (cb) {
  sh('./vendor/bin/phpunit -c configs/phpunit.xml');
});

gulp.task('serve', function (cb) {
  sh('php ../../../artisan serve');
});

gulp.task('check', function (cb) {
  sh('./vendor/bin/phpcs -n --standard=PSR2 --ignore=./src/Javascript/lava.js ./src ./tests');
});

gulp.task('fix', function (cb) {
  sh('./vendor/bin/phpcbf -n --standard=PSR2 --ignore=./src/Javascript/lava.js ./src ./tests');
});

gulp.task('lint', function (cb) {
  var lavaSrc = './src/Javascript/lava.js';

  gulp.src(lavaSrc)
      .pipe(jshint())
      .pipe(jshint.reporter(stylish))
      .pipe(jshint.reporter('fail'));
});

gulp.task('bump', function (cb) {
  var version = argv.v;
  var minorVersion = version.slice(0, -2);

  gulp.src('./package.json')
      .pipe(bump({version:argv.v}))
      .pipe(gulp.dest('./'));

  gulp.src(['./README.md', './.travis.yml'])
      .pipe(replace(/("|=|\/|-)[0-9]+\.[0-9]+/g, '$1'+minorVersion))
      .pipe(gulp.dest('./'));
});
