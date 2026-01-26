import { HistoryView } from "$/shared/inertia/history-view.svelte";

export const editView = new HistoryView<{
    event: App.Data.EventDto;
    isCalendarOpen: boolean;
}>("edit-event");
