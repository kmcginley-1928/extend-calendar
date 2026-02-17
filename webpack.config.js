const config = require('flarum-webpack-config');
module.exports = config({
  useExtensions: ['forum'],
  modules: {
    forum: './js/src/forum/index.js',
  },
});