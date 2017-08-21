import { Injectable } from '@angular/core';

function getWindow() {
    return window;
}

@Injectable()
export class LavaJsService {
    private _window: any;

    constructor() {
        this._window = getWindow();

        console.log('[lava.js] Angular service provider loaded.');
    }

    /**
     * Returns the Lava.js instance attached to the window.
     *
     * @return {LavaJs}
     */
    getInstance() {
        return this._window.lava;
    }
}
