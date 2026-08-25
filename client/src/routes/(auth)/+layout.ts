import { redirect } from "@sveltejs/kit";
import { SessionCache } from "#entities/user/mod.ts";
import { resolve } from "$app/paths";

import type { LayoutLoad } from "./$types";

export const load: LayoutLoad = () => {
    if (SessionCache.current) {
        redirect(307, resolve("/(app)"));
    }
};
