import { HistoryView } from "$/shared/integrations/inertia";

import type { CategoryData } from "$/entities/todo";

export const view = new HistoryView<{
    __selectcategory: { isOpen: boolean };
    __categorytodelete?: CategoryData;
}>();
