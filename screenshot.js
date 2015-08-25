var WebPage = require('webpage');

page = WebPage.create();

page.open('http://127.0.0.1:8946/{TYPE}.php');
/*
page.onResourceRequested = function (request) {
    console.log(request.method + ' => ' + request.url);
};
*/
page.onError = function (msg, trace) {
    console.log(msg);

    trace.forEach(function(item) {
        console.log('  ', item.file, ':', item.line);
    });
};

page.onLoadFinished = function() {
    console.log('Saving Chart...');
    page.render('build/renders/{TYPE}.png');
    phantom.exit();
};
