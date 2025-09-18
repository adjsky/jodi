import { wayfinder } from "@laravel/vite-plugin-wayfinder";
import { svelte } from "@sveltejs/vite-plugin-svelte";
import tailwindcss from "@tailwindcss/vite";
import laravel from "laravel-vite-plugin";
import { defineConfig } from "vite";

export default defineConfig({
    plugins: [
        wayfinder(),
        laravel({
            input: ["resources/js/app.ts"],
            refresh: true
        }),
        svelte(),
        tailwindcss()
    ],
    resolve: {
        alias: {
            $: "/resources/js"
        }
    }
});
