const config = require('flarum-webpack-config');
module.exports = config({
  modules: {
    forum: './js/src/forum/index.js',
  },
});
