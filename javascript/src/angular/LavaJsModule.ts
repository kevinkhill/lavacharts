import { NgModule } from '@angular/core';
import { LavaJsService } from '../../../../../projects/lavacharts/lavacharts/javascript/src/angular/LavaJsService';

@NgModule({
    providers: [
        LavaJsService,
        { provide: 'Window',  useValue: window }
    ]
})
export class LavaJsModule {}
