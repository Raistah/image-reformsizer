// @ts-check
import { defineConfig } from "@rspack/cli";
import { rspack } from "@rspack/core";
import { fileURLToPath } from 'node:url';
import { dirname } from 'node:path';
import path from "path";
import fs from "fs";

const __filename = fileURLToPath(import.meta.url);
const __dirname = dirname(__filename);
// Target browsers, see: https://github.com/browserslist/browserslist
const targets = ["last 2 versions", "> 0.2%", "not dead", "Firefox ESR"];

class PostBuildTaskPlugin {
    apply(compiler) {
        compiler.hooks.afterEmit.tapAsync('PostBuildTaskPlugin', (compilation, callback) => {
            let styles = [
                'style',
            ];
            styles.forEach(style => {
                if (fs.existsSync('./dist/' + style + '.js'))
	                fs.unlinkSync('./dist/' + style + '.js');
                if (fs.existsSync('./dist/' + style + '.js.map'))
	                fs.unlinkSync('./dist/' + style + '.js.map');
            });

            callback();
        });
    }
}

export default defineConfig({
	entry: {
		main: "./assets/main.js",
		style: './assets/style.scss',
	},
	output: {
        path: path.resolve(__dirname, 'dist'),
        publicPath: '/wp-content/plugins/image-reformsizer/dist/',
    },
	module: {
		rules: [
			{
				test: /\.svg$/,
				type: "asset"
			},
			{
				test: /\.js$/,
				use: [
					{
						loader: "builtin:swc-loader",
						/** @type {import('@rspack/core').SwcLoaderOptions} */
						options: {
							jsc: {
								parser: {
									syntax: "ecmascript"
								}
							},
							env: { targets }
						}
					}
				]
			},
			{
                test: /\.(sass|scss)$/,
                use: [
                    {
                        loader: 'sass-loader',
                        options: {
                            api: 'modern-lightningcss',
                        },
                    },
                    "postcss-loader"
                ],
                type: 'css/auto',
            },
		]
	},
	optimization: {
		minimizer: [
			new rspack.SwcJsMinimizerRspackPlugin(),
			new rspack.LightningCssMinimizerRspackPlugin({
				minimizerOptions: { targets }
			})
		]
	},
	plugins: [
		new PostBuildTaskPlugin(),
	],
	experiments: {
		css: true
	},
	devServer: {
		devMiddleware: {
			writeToDisk: true,
		},
	},
});
