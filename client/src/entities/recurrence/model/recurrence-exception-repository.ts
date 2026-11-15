import { fromDbBoolean } from "#shared/db/boolean.ts";
import { db } from "#shared/db/dexie.ts";

import type { RecurrenceException } from "./types.ts";

function list(
    type: "todo" | "event",
    ids: string[]
): Promise<RecurrenceException[]> {
    return db.recurrenceExceptions
        .where("[recurrenceableType+recurrenceableId]")
        .anyOf(ids.map((id) => [type, id]))
        .toArray((exceptions) =>
            exceptions.map((e) => ({
                id: e.id,
                recurrenceableId: e.recurrenceableId,
                occursAt: e.occursAt,
                isCancelled: fromDbBoolean(e.isCancelled),
                overrides: e.overrides
            }))
        );
}

export const RecurrenceExceptionRepository = {
    list
};
