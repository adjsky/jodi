import { defineConfig } from "@vite-pwa/assets-generator/config";

export default defineConfig({
    preset: {
        transparent: {
            sizes: [64, 72, 96, 192, 512],
            favicons: [[48, "favicon.ico"]]
        },
        maskable: {
            sizes: [512],
            resizeOptions: { background: "#fdf3e2" }
        },
        apple: {
            sizes: [180],
            resizeOptions: { background: "#fdf3e2" }
        }
    },
    images: ["public/logo.svg"]
});
