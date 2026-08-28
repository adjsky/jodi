import { HistoryView } from "$/shared/integrations/inertia";

export const editView = new HistoryView<{ __todo: { id: string } }>(
    "edit-todo"
);
