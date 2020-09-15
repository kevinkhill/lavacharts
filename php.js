const phpServer = require("php-server");

(async () => {
  const server = await phpServer({});

  console.log(`PHP server running at ${server.url}`);
})();
