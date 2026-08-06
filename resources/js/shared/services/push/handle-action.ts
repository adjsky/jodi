import { router } from "@inertiajs/svelte";
import * as v from "valibot";

const ActionSchema = v.variant("purpose", [
    v.object({
        purpose: v.literal("reminder"),
        target: v.picklist(["event", "todo"]),
        id: v.string(),
        d: v.string()
    })
]);

export function handleAction(data: unknown): void {
    const result = v.safeParse(ActionSchema, data);
    if (!result.success) return;

    switch (result.output.purpose) {
        case "reminder": {
            const { target, id, d } = result.output;
            void router.visit("/", {
                data: {
                    target,
                    id,
                    d
                },
                only: ["todos", "events"],
                showProgress: true,
                replace: true
            });
        }
    }
}
