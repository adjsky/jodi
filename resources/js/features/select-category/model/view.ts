import { HistoryView } from "$/shared/inertia/history-view.svelte";

import type { CategoryData } from "$/entities/todo";

export const view = new HistoryView<{
    __selectcategory: { isOpen: boolean };
    __categorytodelete?: CategoryData;
}>();
