import "../../css/app.css";
import "./paraglide";

import { createInertiaApp } from "@inertiajs/svelte";
import { hydrate, mount } from "svelte";

import type { ResolvedComponent } from "@inertiajs/svelte";

void createInertiaApp({
    resolve(name) {
        const pages = import.meta.glob<ResolvedComponent>(
            "../pages/**/*.svelte",
            { eager: true }
        );
        return pages[`../pages/${name}.svelte`];
    },
    setup({ el, App, props }) {
        if (el && el.dataset.serverRendered === "true") {
            hydrate(App, { target: el, props });
        } else if (el) {
            mount(App, { target: el, props });
        }
    },
    progress: false
});
