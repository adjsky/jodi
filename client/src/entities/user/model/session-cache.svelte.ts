import { db } from "#shared/db/dexie.ts";
import { liveQuery } from "dexie";

import { getCurrentSession } from "../api/session.ts";

import type { Session } from "#shared/db/tables/session.ts";
import type { Except } from "type-fest";

type SessionCache = Except<Session, "id">;

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

    const session = await getCurrentSession();
    await set(session);
}

async function set(session: SessionCache): Promise<void> {
    await db.sessions.put({
        id: "current",
        user: session.user,
        pushSubscription: session.pushSubscription
    });
}

export const SessionCache = {
    init,
    hydrate,
    set,
    get current(): SessionCache | null {
        if (cache === undefined) {
            throw new Error("Session cache must be initialized before usage.");
        }

        return cache;
    }
};
