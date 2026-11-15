import { db } from "#shared/db/dexie.ts";
import { liveQuery } from "#shared/db/live-query.ts";

import type { Category } from "./types.ts";
import type { LiveQuery } from "#shared/db/live-query.ts";

function list(): LiveQuery<Category[]> {
    return liveQuery(() =>
        db.categories.orderBy("createdAt").toArray((categories) =>
            categories.map((c) => ({
                id: c.id,
                name: c.name
            }))
        )
    );
}

function create(name: string): Promise<string> {
    return db.categories.add({
        id: crypto.randomUUID(),
        name,
        createdAt: Date.now()
    });
}

function update(id: string, name: string): Promise<number> {
    return db.categories.update(id, { name });
}

function destroy(id: string): Promise<void> {
    return db.categories.delete(id);
}

export const CategoryRepository = {
    list,
    create,
    update,
    destroy
};
