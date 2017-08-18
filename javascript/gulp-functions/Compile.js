import args from 'yargs';
import gulpif from 'gulp-if';
import source from 'vinyl-source-stream';
import notifier from 'node-notifier';
import browserify from 'browserify';
import uglify from 'gulp-uglify';
import babelify from 'babelify';
import watchify from 'watchify';
import streamify from 'gulp-streamify';
import { dest } from 'gulp';
import { log } from 'gulp-util';
import { red, green } from 'chalk';
import { create as createBrowserSync } from 'browser-sync';

const browserSync = createBrowserSync();

let bundler = browserify({
    debug: true,
    entries: ['./src/lava.entry.js'],
    cache: {},
    packageCache: {},
    transform: [
        'browserify-versionify',
        ['babelify', {presets: ['es2015'] }]
    ]
});

function rebundle(prod = false) {
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

export default function compile(prod, watch, sync) {
    if (prod) {
        bundler.transform('stripify');
    }

    if (watch) {
        bundler = watchify(bundler);

        if (sync) {
            browserSync.init({
                proxy: "localhost:" + args.port || 8000
            });
        }

        bundler.on('update', () => {
            const msg = 'Lava.js re-bundling...';

            log(green(msg));

            notifier.notify({
                title: 'Browserify',
                message: msg
            });

            rebundle(prod);
        });

        bundler.on('log', msg => {
            log(green(msg));

            if (sync) {
                browserSync.reload();
            }
        });
    }

    return rebundle(prod);
}
