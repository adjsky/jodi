import "../../css/app.css";
import "./config/i18n";
import "./config/date";
import "./config/capacitor";

import { createInertiaApp } from "@inertiajs/svelte";
import { attachPortalRoot } from "$/shared/lib/dom/attach-portal-root";
import { mount } from "svelte";

import PersistentLayout from "./ui/layouts/PersistentLayout.svelte";

void createInertiaApp({
    pages: { path: "../pages", lazy: true },
    layout: () => PersistentLayout,
    setup({ el, App, props }) {
        if (!el) return;
        const portalRoot = attachPortalRoot(el);
        mount(App, { target: el, anchor: portalRoot, props });
    },
    progress: {
        color: "var(--color-brand)",
        delay: 100
    }
});
