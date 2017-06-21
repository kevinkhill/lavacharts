import LavaJs from '../lava/Lava.js';
import Injectable from '@angular/core';
import WindowRefService from '../angular/WindowRefService'

@Injectable()
export class LavaJsService {
    private _lava: LavaJs;
    private _window: Window;

    constructor (
        windowRef: WindowRefService
    ) {
        this._lava = new LavaJs;

        this._window = windowRef.nativeWindow;

        console.log(this._window);

        console.log('Lavacharts Lava.js Service Provider Loaded.');

        this._window.lava = this._lava;
    }

    /**
     * Returns the Lava.js instance attached to the window.
     *
     * @return {LavaJs}
     */
    get lava(): LavaJs {
        return this._window.lava;
    }
}
