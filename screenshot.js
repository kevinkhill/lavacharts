var WebPage = require('webpage');

page = WebPage.create();

page.open('http://127.0.0.1:8946/{TYPE}.php');

page.onLoadFinished = function() {
  page.render('build/renders/{TYPE}.png');
  phantom.exit();
};
