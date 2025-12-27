import { HistoryView } from "$/shared/inertia/history-view.svelte";

export const view = new HistoryView<{
    entity: "todo" | "event";
    overlay?: "calendar";
}>("add");
