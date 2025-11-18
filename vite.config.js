import { paraglideVitePlugin } from "@inlang/paraglide-js";
import { wayfinder } from "@laravel/vite-plugin-wayfinder";
import { svelte } from "@sveltejs/vite-plugin-svelte";
import tailwindcss from "@tailwindcss/vite";
import laravel from "laravel-vite-plugin";
import { defineConfig } from "vite";
import tsconfigPaths from "vite-tsconfig-paths";

import { dto } from "./resources/vite/dto/plugin";

export default defineConfig({
    plugins: [
        dto(),
        wayfinder({
            path: "resources/js/generated"
        }),
        paraglideVitePlugin({
            project: "./project.inlang",
            outdir: "./resources/js/paraglide",
            strategy: ["custom-cookie"]
        }),
        tsconfigPaths(),
        laravel({
            input: ["resources/js/app/entrypoint.ts"],
            refresh: true
        }),
        svelte(),
        tailwindcss()
    ]
});
