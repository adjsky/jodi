import { fromAbsolute } from "@internationalized/date";
import { UTC } from "#shared/cfg/constants.ts";
import { fromDbBoolean } from "#shared/db/boolean.ts";
import { db } from "#shared/db/dexie.ts";
import { evolve } from "remeda";
import { match } from "ts-pattern";

import type { RecurrenceException } from "./types.ts";

type RecurrenceType = RecurrenceException["recurrenceableType"];

type ExceptionFor<T extends RecurrenceType> = Extract<
    RecurrenceException,
    { recurrenceableType: T }
>;

function list<T extends RecurrenceType>(
    type: T,
    ids: string[]
): Promise<ExceptionFor<T>[]> {
    return db.recurrenceExceptions
        .where("[recurrenceableType+recurrenceableId]")
        .anyOf(ids.map((id) => [type, id]))
        .toArray(
            (exceptions) =>
                exceptions.map((e) =>
                    match(e)
                        .returnType<RecurrenceException>()
                        .with({ recurrenceableType: "todo" }, (e) => ({
                            ...e,
                            isCancelled: fromDbBoolean(e.isCancelled),
                            overrides: evolve(e.overrides, {
                                scheduledAt: (value) =>
                                    fromAbsolute(value, UTC),
                                hasTime: fromDbBoolean,
                                completedAt: (value) =>
                                    value === null
                                        ? null
                                        : fromAbsolute(value, UTC)
                            })
                        }))
                        .with({ recurrenceableType: "event" }, (e) => ({
                            ...e,
                            isCancelled: fromDbBoolean(e.isCancelled),
                            overrides: evolve(e.overrides, {
                                startsAt: (value) => fromAbsolute(value, UTC),
                                endsAt: (value) => fromAbsolute(value, UTC)
                            })
                        }))
                        .exhaustive()
                ) as ExceptionFor<T>[]
        );
}

export const RecurrenceExceptionRepository = {
    list
};
