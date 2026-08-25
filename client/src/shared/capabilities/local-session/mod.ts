import { db } from "#shared/db/dexie.ts";
import { goto } from "$app/navigation";
import { resolve } from "$app/paths";

import { AuthTokenStorage } from "../auth-token-storage/mod.ts";

let isEndingSession = false;

async function end(): Promise<boolean> {
    if (isEndingSession) return false;

    isEndingSession = true;

    try {
        await Promise.all([
            AuthTokenStorage.remove(),
            db.transaction("rw", db.tables, async () => {
                await Promise.all(db.tables.map((table) => table.clear()));
            })
        ]);
        await goto(resolve("login"));
    } catch (e) {
        console.error(e);
        return false;
    } finally {
        isEndingSession = false;
    }

    return true;
}

export const LocalSession = {
    end
};
