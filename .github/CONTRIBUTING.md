## Contributing

First of all, **thank you** for contributing!

Here are a few guidelines to follow in order to ease code reviews and merging:

### For the PHP code base

- follow [PSR-1](http://www.php-fig.org/psr/1/) and [PSR-2](http://www.php-fig.org/psr/2/)
- run the test suite
- write (or update) unit tests when applicable
- write documentation for new features
- use [commit messages that make sense](http://tbaggery.com/2008/04/19/a-note-about-git-commit-messages.html)

One may ask you to [squash your commits](http://gitready.com/advanced/2009/02/10/squashing-commits-with-rebase.html) too. This is used to "clean" your pull request before merging it (we don't want commits such as `fix tests`, `fix 2`, `fix 3`, etc.).

When creating your pull request on GitHub, please write a description which gives the context and/or explains why you are creating it.


### For the Javascript code base

To get started, you will need to have nodejs installed with npm.

- From the root of the project, navigate to the javascript directory: `cd javascript`
- Then run `npm install` to fetch all the tooling needed for compiling lava.js

Gulp is used to build the module so you will need to use the tasks:
- Use `gulp watch` to monitor the files for changes and rebuild when detected.
- Use `gulp build` to initiate a manual dev build.
- Use `gulp release` to initiate a manual production build. (Strips comments, removes logging, and minifys the code.)
