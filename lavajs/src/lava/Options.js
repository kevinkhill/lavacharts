/**
 * Options module
 *
 * Default configuration options for using Lava.js as a standalone library.
 *
 * @module    lava/Options
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @license   MIT
 */

/**
 * @type {{auto_run: boolean, locale: string, timezone: string, datetime_format: string, maps_api_key: string, responsive: boolean, debounce_timeout: number}}
 */
const defaultOptions = {
    "auto_run"        : false,
    "locale"          : "en",
    "timezone"        : "America/Los_Angeles",
    "datetime_format" : "",
    "maps_api_key"    : "",
    "responsive"      : true,
    "debounce_timeout": 250
};

export default defaultOptions;
