import { HistoryView } from "$/shared/integrations/inertia";

export const editView = new HistoryView<{ __event: { id: string } }>(
    "edit-event"
);
