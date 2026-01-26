import { HistoryView } from "$/shared/inertia/history-view.svelte";

export const editView = new HistoryView<{
    todo: App.Data.TodoDto;
    isCalendarOpen: boolean;
}>("edit-todo");
