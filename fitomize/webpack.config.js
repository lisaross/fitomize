const path = require('path')
const webpack = require('webpack'); //to access built-in plugins

const BrowserSyncPlugin = require('browser-sync-webpack-plugin')

// include the js minification plugin
const TerserPlugin = require('terser-webpack-plugin')

// include the css extraction and minification plugins
const MiniCssExtractPlugin = require('mini-css-extract-plugin')
const OptimizeCSSAssetsPlugin = require('optimize-css-assets-webpack-plugin')
const CleanWebpackPlugin = require('clean-webpack-plugin')

module.exports = {
  entry: ['./js/src/app.js', './css/src/app.scss'],
  output: {
    filename: './js/build/app.min.js',
    path: path.resolve(__dirname)
  },
  devtool: 'inline-source-map',
  module: {
    rules: [
      // perform js babelization on all .js files
      {
        test: /\.js$/,
        exclude: /node_modules/,
        use: {
          loader: 'babel-loader',
          options: {
            presets: ['@babel/env']
          }
        }
      },
      // compile all .scss files to plain old css
      {
        test: /\.(sa|sc|c)ss$/,
        use: [MiniCssExtractPlugin.loader, 'css-loader', 'postcss-loader', 'sass-loader']
      },
      {
        test: /\.(png|svg|jpg|gif)$/,
        use: ['file-loader']
      }
    ]
  },
  plugins: [
    // extract css into dedicated file
    new MiniCssExtractPlugin({
      filename: './css/build/app.min.css'
    }),
    new CleanWebpackPlugin({
      verbose: true,
      cleanStaleWebpackAssets: true,
      protectWebpackAssets: true,
      cleanOnceBeforeBuildPatterns: ['**/build/*', '!**/node_modules/**/build/*'],
    }),
    new BrowserSyncPlugin({
      files: '**/*.php',
      proxy: 'https://127.0.0.1:8443'
    })
  ],
  optimization: {
    minimizer: [
      // enable the js minification plugin
      new TerserPlugin({
        terserOptions: {
          compress: {
            warnings: false
          },
          output: {
            comments: false
          }
        },
        sourceMap: true
      }),
      // enable the css minification plugin
      new OptimizeCSSAssetsPlugin({
        cssProcessor: require('cssnano'),
        cssProcessorPluginOptions: {
          preset: ['default', { discardComments: { removeAll: true } }],
        },
        canPrint: true
      })
    ]
  }
}
