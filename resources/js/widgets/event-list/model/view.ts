import { HistoryView } from "$/shared/inertia/history-view.svelte";

import type { EventData } from "$/entities/event/model/types";

export const editView = new HistoryView<EventData>("edit-event");
