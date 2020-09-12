# Lavacharts 4.0

The biggest change in v4 is how the PHP Library interacts with the Lava.js module, and how the javascript rendering flow works.

## [4.0.0] - 2020

### Removed

## [4.0.0] - 2017-09-01

### Added

- Added a Lava.js Service for Angular.
- Most classes implement Arrayable and Jsonable interfaces to convert the classes into JSON to pass to Lava.js
-

### Changed

- Lava.js rewritten in ES6 to be leaner, faster and do more of the heavy lifting
  - Json versions of the Renderables and Promises are used to simplify the render flow instead of a complex chain of init, run, render, ready, passing control around.
- Completely rewrote how Charts and Dashboards get passed to Lava.js
  - They no longer write out large amounts of javascript. They boil down to a JSON representation of themselves and get passed into Lava.js.
- ScriptManager now handles all javascript generation
- Charts now use methods over constants
- Creating Dashboards has been simplified.

### Removed

- PHP 5.4 & 5.5 support
- Laravel 4 support
- render() from Lavacharts
- customize() from Charts
- Named Filter Classes
- Named Format Classes
- DashboardBuilder, GenericBuilder
- ChartJsFactory, DashboardJsFactory
- Javascript templates for charts and dashboards

# More Notes

## Lava.js Renderflow

...and how v4 has simplified this process.

#### Version 3.1

- Individual `render()` calls in the view php script would trigger the output of the lava.js module to the page, but only once, checking if it was already output.
- Each Renderable would output a `<script>` block to the page, using a template to string replace many values, then drop it right where the `render()` calls were (This was a problem for some users.)
- Once the page was loaded, `lava.run()` is called and the render flow begins.
- `run()` called `init()` which called `renderable.init()` for each one.
  - The renderable `init()` method would set a few properties, setup the `config()` method, then emit a `ready` event.
  - Once all the Renderables were ready, then `lava.init()` chain would continue by loading `google` (which was another few chained methods)
  - Then all the Renderables' `configure()` methods were called to setup their draw methods, etc.
  - Then finally, each Renderables' `render()` methods are called, and the charts are actually displayed on the page.
- Then the `lava.ready()` callback is executed
- I am still amazed I got it working, and it always felt like a house of cards.

#### Version 4.0 Goodness

- Instead of individual `render()` calls, only one call to the library is needed, `flow()`
- This will output the Lava.js module into a `<script>` tag (this can be overriden, or loaded manually, or externally) and one additional `<script>` tag with all the needed javascript for rendering.
  - Charts will output `lava.addNewChart({CHART_JSON})` and Dashboards will output `lava.addNewDashboard({DASHBOARD_JSON})`
  - This further simplifies the library, by not tying the user to expecting large chunks of javascript dumped into the page. The user can setup API endpoints to serve the Renderables' JSON and use lava.js manually to store them, and call `lava.run()` and be done with it.
- The `lava.run()` method now simply loads `google` (if it is not already available in page) and then calls each Renderables' `render()` method.

---

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).
