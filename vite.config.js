import { paraglideVitePlugin } from "@inlang/paraglide-js";
import { wayfinder } from "@laravel/vite-plugin-wayfinder";
import { svelte } from "@sveltejs/vite-plugin-svelte";
import tailwindcss from "@tailwindcss/vite";
import laravel from "laravel-vite-plugin";
import { defineConfig } from "vite";
import tsconfigPaths from "vite-tsconfig-paths";

export default defineConfig({
    plugins: [
        paraglideVitePlugin({
            project: "./project.inlang",
            outdir: "./resources/js/paraglide",
            strategy: ["custom-cookie"]
        }),
        tsconfigPaths(),
        wayfinder({
            path: "resources/js/generated"
        }),
        laravel({
            input: ["resources/js/app/entrypoint.ts"],
            refresh: true
        }),
        svelte(),
        tailwindcss()
    ]
});
