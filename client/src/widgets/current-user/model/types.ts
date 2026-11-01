import type { Session } from "#shared/db/tables/session.ts";

export type ViewProps = {
    open: boolean;
    user: Session["user"];
};
