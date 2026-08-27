import { Context } from "runed";

export const context = new Context<{ open: boolean }>("shared:screen-view");
