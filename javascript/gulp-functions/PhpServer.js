import PortFinder from 'portfinder';
import PhpServer from 'gulp-connect-php';
import { cwd } from 'process';
import { resolve } from 'path';

function _createServer(port) {
    const base = resolve(cwd(), '../tests/Examples');
    const server = new PhpServer();

    return new Promise(resolve => {
        server.server({
            base: base,
            port: port,
            ini: base + '/php.ini',
            router: base + '/renderer.php'
        });

        resolve(server);
    });
}

export default function getPhpServer() {
    return PortFinder
        .getPortPromise()
        .then(_createServer)
        .catch(err => {
            console.log(err);
        });
}
