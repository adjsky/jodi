import { HistoryView } from "$/shared/inertia/history-view.svelte";

import type { TodoData } from "$/entities/todo";

export const editView = new HistoryView<TodoData>("edit-todo");
