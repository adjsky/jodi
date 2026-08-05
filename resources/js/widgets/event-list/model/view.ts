import { HistoryView } from "$/shared/integrations/inertia";

import type { EventData } from "$/entities/event";

export const editView = new HistoryView<EventData>("edit-event");
