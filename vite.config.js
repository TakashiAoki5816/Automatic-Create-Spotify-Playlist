import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";

export default defineConfig({
    resolve: {
        preserveSymlinks: true, // シンボリックリンクを辿っていないパスでファイルの同一性を判別 ※ファイル名をリネームした時に役立つ
    },
    plugins: [
        vue(),
        laravel({
            input: [
                "resources/js/app.ts"
            ],
            refresh: true,
        }),
    ],
});
