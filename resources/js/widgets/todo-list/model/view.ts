import { HistoryView } from "$/shared/integrations/inertia";

import type { TodoData } from "$/entities/todo";

export const editView = new HistoryView<TodoData>("edit-todo");
