const path = require('path');

/**
 * Plain webpack to verify the path/FS side works.
 * We mark Flarum modules as externals so resolution does not fail at build time.
 */
module.exports = {
  mode: 'production',
  entry: {
    forum: path.resolve(__dirname, 'js/src/forum/index.js'),
  },
  output: {
    path: path.resolve(__dirname, 'js/dist'),
    filename: '[name].js',
    clean: true
  },
  externalsType: 'commonjs',
  externals: [
    // Treat every "flarum/..." import as external so webpack won't try to bundle it
    ({ request }, callback) => {
      if (request && /^flarum\//.test(request)) return callback(null, `commonjs ${request}`);
      return callback();
    }
  ]
};