const path = require('path');
const CleanWebpackPlugin = require('clean-webpack-plugin');
const ExtractTextPlugin = require('extract-text-webpack-plugin');
const TwigWatchPlugin = require('./TwigWatchPlugin');

const assetsSubFolder = 'assets';
const publicDir = path.resolve(__dirname, '..', '..', 'public');
const outputDir = path.resolve(publicDir, assetsSubFolder);
const tsRootFolder = path.resolve(__dirname, '..', 'ts');

module.exports = {
    entry: {
        index: path.resolve(tsRootFolder, 'Main.ts')
    },
    module: {
        rules: [
            {
                test: /\.tsx?$/,
                exclude: /node_modules/,
                use: [
                    {
                        loader: 'babel-loader',
                        options: {
                            presets: []
                        }
                    },
                    {
                        loader: 'ts-loader',
                        options: {
                            configFile: path.resolve(tsRootFolder, 'tsconfig.json')
                        }
                    }
                ],
            },
            {
                test: /\.scss$/,
                use: ExtractTextPlugin.extract({
                    fallback: 'style-loader',
                    use: ['css-loader', 'sass-loader']
                })
            }, {
                test: /\.(jpe|jpg|woff|woff2|eot|ttf|svg)/,
                loader: 'file-loader'
            }
        ]
    },
    plugins: [
        // hack with `root` dir. Otherwise the files are not removed because they are 'outside of the project root'.
        new CleanWebpackPlugin('*.*', {root: outputDir}),

        new ExtractTextPlugin('styles.css'),

        new TwigWatchPlugin(),
    ],
    resolve: {
        extensions: ['.tsx', '.ts', '.js']
    },
    output: {
        // https://webpack.js.org/configuration/output/#output-librarytarget
        libraryTarget: 'var',
        publicPath: `/${assetsSubFolder}/`,
        path: outputDir,
        filename: '[name].js',
    },
    devServer: {
        contentBase: publicDir,
        compress: true,
        port: 8080,
        stats: 'minimal',
        proxy: [{
            context: ['**', '!/assets/**', '!/img/**'],
            target: 'http://localhost:8090',
        }],
    },
};
