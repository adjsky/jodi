import { HistoryView } from "$/shared/inertia/history-view.svelte";

export const view = new HistoryView<{
    __selectcategory: { isOpen: boolean };
    __categorytodelete?: string;
}>();
