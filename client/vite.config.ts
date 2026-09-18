import { paraglideVitePlugin } from "@inlang/paraglide-js";
import { wayfinder } from "@laravel/vite-plugin-wayfinder";
import adapter from "@sveltejs/adapter-static";
import { sveltekit } from "@sveltejs/kit/vite";
import tailwindcss from "@tailwindcss/vite";
import { defineConfig } from "vite";

export default defineConfig({
    plugins: [
        wayfinder({
            command: "php ../api/artisan wayfinder:generate",
            path: "./generated/wayfinder",
            patterns: ["../api/routes/**/*.php"]
        }),
        sveltekit({
            adapter: adapter({ fallback: "index.html" })
        }),
        tailwindcss(),
        paraglideVitePlugin({
            project: "./project.inlang",
            outdir: "./generated/paraglide",
            emitTsDeclarations: true,
            strategy: [
                "custom-preference",
                "localStorage",
                "preferredLanguage",
                "baseLocale"
            ],
            localStorageKey: "jodi-locale"
        })
    ],
    envDir: "..",
    server: {
        proxy: {
            "/api": {
                target: "http://jodi.localhost:8000"
            },
            "/firebase-messaging-sw.js": {
                target: "http://jodi.localhost:8000"
            }
        }
    }
});
