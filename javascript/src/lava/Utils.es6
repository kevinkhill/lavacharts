/* jshint undef: true, unused: true */
/* globals exports, window */

/**
 * Function that does nothing.
 *
 * @return {undefined}
 */
export function noop() {
    return undefined;
}

/**
 * Slightly modified event attachment handler.
 *
 * Credit to Alex V.
 *
 * @link https://stackoverflow.com/users/327934/alex-v
 * @link http://stackoverflow.com/a/3150139
 * @param {Function} callback
 */
export function addResizeEvent(callback) {
    if (window === null || typeof(window) === 'undefined') return;
    if (window.addEventListener) {
        window.addEventListener('resize', callback, false);
    } else if (window.attachEvent) {
        window.attachEvent('onresize', callback);
    } else {
        window['onresize'] = callback;
    }
}

/**
 * Get a function a by its' namespaced string name with context.
 *
 * Credit to Jason Bunting
 *
 * @link https://stackoverflow.com/users/1790/jason-bunting
 * @link https://stackoverflow.com/a/359910
 * @param {string} functionName
 * @param {object} context
 * @private
 */
export function stringToFunction(functionName, context) {
    let namespaces = functionName.split('.');
    let func = namespaces.pop();

    for (let i = 0; i < namespaces.length; i++) {
        context = context[namespaces[i]];
    }

    return context[func];
}
