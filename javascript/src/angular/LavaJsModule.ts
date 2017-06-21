import { NgModule } from '@angular/core';
import { LavaJsService } from './LavaJsService';

@NgModule({
    providers: [
        LavaJsService,
        { provide: 'Window',  useValue: window }
    ]
})
export class LavaJsModule {}
