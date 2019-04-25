const path = require('path')

// include the js minification plugin
const TerserPlugin = require('terser-webpack-plugin')

// include the css extraction and minification plugins
const MiniCssExtractPlugin = require('mini-css-extract-plugin')
const OptimizeCSSAssetsPlugin = require('optimize-css-assets-webpack-plugin')

module.exports = {
  entry: ['./js/src/app.js', './css/src/app.scss'],
  output: {
    filename: './js/build/app.min.js',
    path: path.resolve(__dirname)
  },
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
        test: /\.(sass|scss)$/,
        use: [MiniCssExtractPlugin.loader, 'css-loader', 'sass-loader']
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
      filename: './css/build/main.min.css'
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
      new OptimizeCSSAssetsPlugin({})
    ]
  }
}
