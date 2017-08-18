/* globals document */

/**
 * Function that does nothing.
 *
 * @return {undefined}
 */
export function noop() {
    return undefined;
}

/**
 * Return the type of object.
 *
 * @param {object} object
 * @return {mixed}
 */
export function getType(object) {
    let type = Object.prototype.toString.call(object);

    return type.replace('[object ','').replace(']','');
}

/**
 * Simple Promise for the DOM to be ready.
 *
 * @return {Promise}
 */
export function domLoaded() {
    return new Promise(resolve => {
        if (document.readyState === 'interactive' || document.readyState === 'complete') {
            resolve();
        } else {
            document.addEventListener('DOMContentLoaded', resolve);
        }
    });
}

/**
 * Method for attaching events to objects.
 *
 * Credit to Alex V.
 *
 * @link https://stackoverflow.com/users/327934/alex-v
 * @link http://stackoverflow.com/a/3150139
 * @param {object} target
 * @param {string} type
 * @param {Function} callback
 * @param {bool} eventReturn
 */
export function addEvent(target, type, callback, eventReturn)
{
    if (target === null || typeof target === 'undefined') {
        return;
    }

    if (target.addEventListener) {
        target.addEventListener(type, callback, !!eventReturn);
    }
    else if(target.attachEvent) {
        target.attachEvent("on" + type, callback);
    }
    else {
        target["on" + type] = callback;
    }
}
