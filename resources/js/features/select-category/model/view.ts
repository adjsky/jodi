import { HistoryView } from "$/shared/inertia/history-view.svelte";

import type { CategoryData } from "$/entities/todo";
import type { DISABLE_SHEET_DRAGGING } from "$/shared/ui/Sheet.svelte";

export const view = new HistoryView<{
    [DISABLE_SHEET_DRAGGING]: boolean;
    __selectcategory: { isOpen: boolean };
    __categorytodelete?: CategoryData;
}>();
