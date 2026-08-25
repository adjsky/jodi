import Dexie from "dexie";

import type { Session } from "./tables/session.ts";
import type { EntityTable } from "dexie";

export const db = new Dexie("jodi") as Dexie & {
    sessions: EntityTable<Session, "id">;
};

db.version(1).stores({
    sessions: "id"
});
