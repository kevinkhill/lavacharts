import args from 'yargs';
import gulpif from 'gulp-if';
import source from 'vinyl-source-stream';
import notifier from 'node-notifier';
import browserify from 'browserify';
import uglify from 'gulp-uglify';
import babelify from 'babelify';
// import stripify from 'stripify';
import watchify from 'watchify';
import streamify from 'gulp-streamify';
import versionify from 'browserify-versionify';
import { dest } from 'gulp';
import { log } from 'gulp-util';
import { red, green } from 'chalk';
import { create } from 'browser-sync';

const browserSync = create();

export default function compile(prod, watch, sync) {
    let bundler = browserify({
        debug: true,
        entries: ['./src/lava.entry.es6'],
        cache: {},
        packageCache: {}
    });

    bundler.transform(babelify, { presets: ['es2015'] });
    bundler.transform(versionify);

    if (watch) {
        bundler = watchify(bundler);

        if (sync) {
            browserSync.init({
                proxy: "localhost:" + args.port || 8000
            });
        }
    }

    if (prod) {
        bundler.transform('stripify');
    }

    function rebundle() {
        return bundler.bundle()
            .on('error', err => {
                if (err instanceof SyntaxError) {
                    log(red('Syntax Error'));
                    log(err.message);
                    // log(err.filename+":"+err.loc.line);
                    log(err.codeFrame);
                } else {
                    log(red('Error'), err.message);
                }

                this.emit('end');
            })
            .pipe(source('lava.js'))
            .pipe(gulpif(prod, streamify(uglify())))
            .pipe(dest('dist'));
    }

    if (watch) {
        bundler.on('update', () => {
            const msg = 'lava.js re-bundling...';

            log(green(msg));

            notifier.notify({
                title: 'Browserify',
                message:msg
            });

            rebundle();
        });

        bundler.on('log', msg => {
            log(green(msg));

            if (sync) {
                browserSync.reload();
            }
        });
    }

    return rebundle();
}
