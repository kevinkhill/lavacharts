/**
 * Errors module
 *
 * @module    lava/Errors
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @license   MIT
 */
class LavaJsError extends Error
{
    constructor (message) {
        super();

        this.name    = 'LavaJsError';
        this.message = (message || '');
    };
}

/**
 * InvalidCallback Error
 *
 * thrown when when anything but a function is given as a callback
 * @type {function}
 */
export class InvalidCallback extends LavaJsError
{
    constructor (callback) {
        super(`[lava.js] "${typeof callback}" is not a valid callback.`);

        this.name = 'InvalidCallback';
    }
}

/**
 * InvalidLabel Error
 *
 * Thrown when when anything but a string is given as a label.
 *
 * @type {function}
 */
export class InvalidLabel extends LavaJsError
{
    constructor (label) {
        super(`[lava.js] "${typeof label}" is not a valid label.`);
        this.name = 'InvalidLabel';
    }
}

/**
 * ElementIdNotFound Error
 *
 * Thrown when when anything but a string is given as a label.
 *
 * @type {function}
 */
export class ElementIdNotFound extends LavaJsError
{
    constructor (elemId) {
        super(`[lava.js] DOM node where id="${elemId}" was not found.`);

        this.name = 'ElementIdNotFound';
    }
}
