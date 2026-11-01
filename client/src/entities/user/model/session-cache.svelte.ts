import { db } from "#shared/db/dexie.ts";
import { LastMatching } from "#shared/lib/svelte/last-matching.svelte.ts";
import { liveQuery } from "dexie";
import { isHTTPError } from "ky";

import { getCurrentSession } from "../api/session.ts";

import type { Session } from "#shared/db/tables/session.ts";
import type { UpdateSpec } from "dexie";
import type { Except } from "type-fest";

type SessionCache = Except<Session, "id">;

type RetainedCache = {
    current: SessionCache;
};

let cache: SessionCache | null | undefined = $state(undefined);

function init(): Promise<void> {
    const query = liveQuery(() => db.sessions.get("current"));

    return new Promise((resolve, reject) => {
        query.subscribe((session) => {
            if (!session) {
                cache = null;
            } else {
                cache = {
                    user: session.user,
                    pushSubscription: session.pushSubscription
                };
            }

            resolve();
        }, reject);
    });
}

async function hydrate(): Promise<void> {
    if (!cache) {
        return;
    }

    try {
        const session = await getCurrentSession();
        await set(session);
    } catch (e) {
        if (isHTTPError(e) && e.response.status === 401) {
            // Swallow error, we don't want to pollute the console with errors
            // that are handled.
            return;
        }

        throw e;
    }
}

async function set(session: SessionCache): Promise<void> {
    await db.sessions.put({
        id: "current",
        user: session.user,
        pushSubscription: session.pushSubscription
    });
}

async function update(changes: UpdateSpec<Session>): Promise<void> {
    await db.sessions.update("current", changes);
}

/**
 * Similar to `.required` but retains the last non-nullable session cache.
 *
 * Useful when using in components to prevent crashes when logging out.
 */
function retain(): RetainedCache {
    const retained = new LastMatching(
        () => SessionCache.current,
        (session) => session !== null
    );

    return {
        get current() {
            const cache = retained.current;

            if (cache === null) {
                throw new Error("Non-nullable session cache is required.");
            }

            return cache;
        }
    };
}

export const SessionCache = {
    init,
    hydrate,
    set,
    update,
    retain,
    get current(): SessionCache | null {
        if (cache === undefined) {
            throw new Error("Session cache must be initialized before usage.");
        }

        return cache;
    },
    get required(): SessionCache {
        const cache = this.current;

        if (cache === null) {
            throw new Error("Non-nullable session cache is required.");
        }

        return cache;
    }
};
