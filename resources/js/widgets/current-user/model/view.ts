import { HistoryView } from "$/shared/inertia/history-view.svelte";

export const view = new HistoryView<{ isDelete?: boolean }>(null, {
    viewTransition: true
});

export function buildViewName(...name: string[]) {
    return "me/" + name.join("/");
}
