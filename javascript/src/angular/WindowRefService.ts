import { Injectable } from '@angular/core';

function _window() : any {
    // return the global native browser window object
    return window;
}

/**
 * @property {Window} nativeWindow
 */
@Injectable()
export class WindowRefService
{
    static get nativeWindow() : any {
        return _window();
    }
}
