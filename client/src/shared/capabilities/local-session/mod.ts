import { db } from "#shared/db/dexie.ts";
import { goto } from "$app/navigation";
import { resolve } from "$app/paths";

import { AuthTokenStorage } from "../auth-token-storage/mod.ts";

let is_ending_session = false;

async function end(): Promise<boolean> {
    if (is_ending_session) return false;

    is_ending_session = true;

    try {
        await Promise.all([
            AuthTokenStorage.remove(),
            db.transaction("rw", db.tables, async () => {
                await Promise.all(db.tables.map((table) => table.clear()));
            })
        ]);
        await goto(resolve("login"));
    } catch (e) {
        console.error("[LocalSession] Failed to end the session: ", e);
        return false;
    } finally {
        is_ending_session = false;
    }

    return true;
}

export const LocalSession = {
    end
};
