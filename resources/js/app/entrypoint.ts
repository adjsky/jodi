import "../../css/app.css";
import "./config/i18n";
import "./config/date";
import "./config/capacitor";

import { createInertiaApp } from "@inertiajs/svelte";
import { hydrate, mount } from "svelte";

import PersistentLayout from "./ui/layouts/PersistentLayout.svelte";

import type { ResolvedComponent } from "@inertiajs/svelte";
import type { LegacyComponentType } from "svelte/legacy";

void createInertiaApp({
    resolve(name) {
        const pages = import.meta.glob<ResolvedComponent>(
            "../pages/**/*.svelte",
            { eager: true }
        );
        const page = pages[`../pages/${name}.svelte`];
        return {
            default: page.default,
            layout: PersistentLayout as LegacyComponentType
        };
    },
    setup({ el, App, props }) {
        if (el && el.dataset.serverRendered === "true") {
            hydrate(App, { target: el, props });
        } else if (el) {
            mount(App, { target: el, props });
        }
    },
    progress: {
        color: "var(--color-brand)",
        delay: 200
    }
});
