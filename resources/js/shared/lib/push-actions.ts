import { router } from "@inertiajs/svelte";

export type PushActionData = {
    purpose: "reminder";
    target: string;
    id: string;
    d: string;
};

export function handlePushAction(data: PushActionData) {
    switch (data.purpose) {
        case "reminder": {
            void router.visit("/", {
                data: {
                    d: data.d,
                    target: data.target,
                    id: data.id
                },
                only: ["todos", "events"],
                showProgress: true,
                replace: true
            });
        }
    }
}
