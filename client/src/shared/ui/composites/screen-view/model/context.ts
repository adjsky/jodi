import { Context } from "runed";

export const context = new Context<{ close: VoidFunction }>(
    "shared:screen-view"
);
