import { fromAbsolute } from "@internationalized/date";
import { UTC } from "#shared/cfg/constants.ts";
import { from_db_boolean, to_db_boolean } from "#shared/db/boolean.ts";
import { db } from "#shared/db/dexie.ts";
import Dexie from "dexie";
import { evolve, omit } from "remeda";
import { match } from "ts-pattern";

import type { RecurrenceException } from "./types.ts";
import type { CalendarDate } from "@internationalized/date";
import type { RecurrenceException as DatabaseException } from "#shared/db/tables/recurrence-exceptions.ts";
import type { KeysOfUnion } from "type-fest";

type RecurrenceType = RecurrenceException["recurrenceable_type"];

type ExceptionFor<T extends RecurrenceType> = Extract<
    RecurrenceException,
    { recurrenceable_type: T }
>;

function map(exception: DatabaseException): RecurrenceException {
    return match(exception)
        .returnType<RecurrenceException>()
        .with({ recurrenceable_type: "todo" }, (e) => ({
            ...e,
            is_cancelled: from_db_boolean(e.is_cancelled),
            overrides: evolve(e.overrides, {
                scheduled_at: (value) => fromAbsolute(value, UTC),
                has_time: from_db_boolean,
                completed_at: (value) => (value === null ? null : fromAbsolute(value, UTC))
            })
        }))
        .with({ recurrenceable_type: "event" }, (e) => ({
            ...e,
            is_cancelled: from_db_boolean(e.is_cancelled),
            overrides: evolve(e.overrides, {
                starts_at: (value) => fromAbsolute(value, UTC),
                ends_at: (value) => fromAbsolute(value, UTC)
            })
        }))
        .exhaustive();
}

async function find<T extends RecurrenceType>(
    type: T,
    id: string,
    occurs_at: CalendarDate
): Promise<ExceptionFor<T> | null> {
    const exception = await db.recurrence_exceptions
        .where("[recurrenceable_type+recurrenceable_id+occurs_at]")
        .equals([type, id, occurs_at.toString()])
        .first();

    if (!exception) {
        return null;
    }

    return map(exception) as ExceptionFor<T>;
}

function list<T extends RecurrenceType>(type: T, ids: string[]): Promise<ExceptionFor<T>[]> {
    return db.recurrence_exceptions
        .where("[recurrenceable_type+recurrenceable_id]")
        .anyOf(ids.map((id) => [type, id]))
        .toArray((exceptions) => exceptions.map(map) as ExceptionFor<T>[]);
}

function upsert(exception: RecurrenceException): Promise<string> {
    return match(exception)
        .with({ recurrenceable_type: "todo" }, (e) =>
            db.recurrence_exceptions.put({
                ...e,
                is_cancelled: to_db_boolean(e.is_cancelled),
                overrides: evolve(e.overrides, {
                    scheduled_at: (value) => value.toDate().getTime(),
                    has_time: to_db_boolean,
                    completed_at: (value) => value?.toDate().getTime() ?? null
                })
            })
        )
        .with({ recurrenceable_type: "event" }, (e) =>
            db.recurrence_exceptions.put({
                ...e,
                is_cancelled: to_db_boolean(e.is_cancelled),
                overrides: evolve(e.overrides, {
                    starts_at: (value) => value.toDate().getTime(),
                    ends_at: (value) => value.toDate().getTime()
                })
            })
        )
        .exhaustive();
}

async function reset(
    type: RecurrenceType,
    id: string,
    attributes: KeysOfUnion<RecurrenceException["overrides"]>[]
): Promise<number> {
    return db.transaction("rw", db.recurrence_exceptions, async () => {
        const exceptions = await db.recurrence_exceptions
            .where("[recurrenceable_type+recurrenceable_id+occurs_at]")
            .between([type, id, Dexie.minKey], [type, id, Dexie.maxKey])
            .toArray();

        for (const exception of exceptions) {
            const overrides = omit(exception.overrides, attributes);

            if (!exception.is_cancelled && Object.keys(overrides).length === 0) {
                await db.recurrence_exceptions.delete(exception.id);
            } else {
                await db.recurrence_exceptions.put({ ...exception, overrides });
            }
        }

        return exceptions.length;
    });
}

function destroy(type: RecurrenceType, id: string, from?: CalendarDate): Promise<number> {
    return db.recurrence_exceptions
        .where("[recurrenceable_type+recurrenceable_id+occurs_at]")
        .between([type, id, from?.toString() ?? Dexie.minKey], [type, id, Dexie.maxKey])
        .delete();
}

function transfer(
    type: RecurrenceType,
    id: string,
    new_id: string,
    from: CalendarDate
): Promise<number> {
    return db.recurrence_exceptions
        .where("[recurrenceable_type+recurrenceable_id+occurs_at]")
        .between([type, id, from.toString()], [type, id, Dexie.maxKey])
        .modify({ recurrenceable_id: new_id });
}

export const RecurrenceExceptionRepository = {
    list,
    find,
    upsert,
    reset,
    destroy,
    transfer
};
