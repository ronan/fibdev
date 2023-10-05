const CopyPlugin = require('copy-webpack-plugin');
const FixStyleOnlyEntriesPlugin = require('webpack-fix-style-only-entries');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const path = require('path');

module.exports = {
  devtool: 'source-map',
  entry: {
    style: ['./scss/style.scss'],
    editor: ['./scss/editor.scss'],
    savethebw: [
      './js/savethebw.js',
    ],
    'savethebw.wave': [
      './js/savethebw.wave.js',
    ],
    'savethebw.flyouts': [
      './js/savethebw.flyouts.js',
    ],
    'savethebw.tabs': [
      './js/savethebw.tabs.js',
    ],
    'savethebw.nav': [
      './js/savethebw.nav.js',
    ],
    'savethebw.slides': [
      './js/savethebw.slides.js',
    ],
    polyfills: [
      'mdn-polyfills/Element.prototype.closest',
      'mdn-polyfills/Element.prototype.matches',
      'mdn-polyfills/Node.prototype.append',
      'mdn-polyfills/Node.prototype.before',
      'mdn-polyfills/Node.prototype.prepend',
      'mdn-polyfills/Node.prototype.remove',
    ],
  },
  output: {
    path: path.resolve(__dirname, 'dist'),
    filename: '[name].js',
  },
  mode: 'development',
  module: {
    rules: [
      {
        parser: {
          amd: false,
        },
      },
      {
        test: /\.js$/,
        exclude: /(node_modules)/,
        use: {
          loader: 'babel-loader',
        },
      },
      {
        test: /\.(sa|sc|c)ss$/,
        use: [
          MiniCssExtractPlugin.loader,
          {
            loader: 'css-loader',
            options: {
              sourceMap: true,
            },
          },
          {
            loader: 'postcss-loader',
            options: {
              sourceMap: true,
            },
          },
          {
            loader: 'sass-loader',
            options: {
              sourceMap: true,
              implementation: require('sass'),
            },
          },
        ],
      },
      {
        test: /\.(png|jpe?g|gif|svg)$/,
        use: [
          {
            loader: 'file-loader',
            options: {
              outputPath: 'images',
            },
          },
        ],
      },
      {
        test: /\.(woff|woff2|ttf|otf|eot)$/,
        use: [
          {
            loader: 'file-loader',
            options: {
              outputPath: 'fonts',
            },
          },
        ],
      },
    ],
  },
  plugins: [
    new CopyPlugin([
      { from: './node_modules/bootstrap/dist/js/bootstrap.min.js', to: './' },
      { from: './node_modules/bootstrap/dist/js/bootstrap.min.js.map', to: './' },
    ]),
    new FixStyleOnlyEntriesPlugin(),
    new MiniCssExtractPlugin({
      filename: '[name].css',
    }),
  ],
};
