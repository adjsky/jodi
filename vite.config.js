import { paraglideVitePlugin } from "@inlang/paraglide-js";
import { wayfinder } from "@laravel/vite-plugin-wayfinder";
import { svelte } from "@sveltejs/vite-plugin-svelte";
import tailwindcss from "@tailwindcss/vite";
import laravel from "laravel-vite-plugin";
import { defineConfig } from "vite";
import { VitePWA } from "vite-plugin-pwa";
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
            strategy: ["custom-preference", "custom-cookie"]
        }),
        tsconfigPaths(),
        laravel({
            input: ["resources/js/app/entrypoint.ts"],
            refresh: true
        }),
        svelte(),
        tailwindcss(),
        VitePWA({
            registerType: "prompt",
            injectRegister: false,

            buildBase: "/build/",
            scope: "/",
            base: "/",

            manifest: {
                name: "Jodi",
                short_name: "Jodi",
                description: "Journal & Diary",
                start_url: "/",
                theme_color: "#f67a3c",
                background_color: "#fdf3e2",
                display: "standalone",
                orientation: "portrait",
                icons: [
                    {
                        src: "/pwa-64x64.png",
                        sizes: "64x64",
                        type: "image/png"
                    },
                    {
                        src: "/pwa-192x192.png",
                        sizes: "192x192",
                        type: "image/png"
                    },
                    {
                        src: "/pwa-512x512.png",
                        sizes: "512x512",
                        type: "image/png"
                    },
                    {
                        src: "/maskable-icon-512x512.png",
                        sizes: "512x512",
                        type: "image/png",
                        purpose: "maskable"
                    }
                ]
            },

            workbox: {
                importScripts: ["/sw-push.js"],
                globPatterns: [
                    "**/*.{js,css,html,ico,jpg,png,svg,woff,woff2,ttf,eot}"
                ],
                additionalManifestEntries: [
                    ...[
                        "/favicon.ico",
                        "/favicon.svg",
                        "/apple-touch-icon-180x180.png"
                    ]
                ].map((url) => ({ url, revision: "v1" })),
                navigateFallback: null,
                maximumFileSizeToCacheInBytes: 3_000_000,
                cleanupOutdatedCaches: true
            }
        })
    ]
});
