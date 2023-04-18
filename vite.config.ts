import {defineConfig} from "vite";
import laravel from "laravel-vite-plugin";
import vue from '@vitejs/plugin-vue';
import fs from 'fs';

const host = 'solutech.loc'

export default defineConfig({
	plugins: [
		laravel({
			input: ['resources/css/app.css', 'resources/js/app.ts'],
			refresh: true,
		}),
		vue({
			template: {
				transformAssetUrls: {
					base: null,
					includeAbsolute: false,
				},
			},
		}),
	],
	server: {
		host: '0.0.0.0',
		hmr: { host },
		watch: {
			usePolling: true,
		},
		https: {
			key: fs.readFileSync(`/etc/ssl/certs/${host}.key`),
			cert: fs.readFileSync(`/etc/ssl/certs/${host}.crt`),
		},
	},
});
