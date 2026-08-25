import * as z from "zod";

const ActionSchema = z.discriminatedUnion("purpose", [
    z.object({
        purpose: z.literal("reminder"),
        target: z.enum(["event", "todo"]),
        id: z.string(),
        d: z.string()
    })
]);

export class Action {
    handle(data: unknown): void {
        const result = z.safeParse(ActionSchema, data);
        if (!result.success) return;

        switch (result.data.purpose) {
            case "reminder": {
                // TODO: implement for sveltekit
                // const { target, id, d } = result.output;
                // void router.visit("/", {
                //     data: {
                //         target,
                //         id,
                //         d
                //     },
                //     only: ["todos", "events"],
                //     showProgress: true,
                //     replace: true
                // });
            }
        }
    }
}
