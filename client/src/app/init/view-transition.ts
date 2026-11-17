import { onNavigate } from "$app/navigation";
import { page } from "$app/state";

export function init(): void {
    onNavigate((navigation) => {
        if (!document.startViewTransition) {
            return;
        }

        const went_back = navigation.delta !== undefined && navigation.delta < 0;
        const from_skip = page.state.skip_view_transition;

        return new Promise((resolve) => {
            const transition = document.startViewTransition({
                types: [went_back ? "back" : "forward"],
                async update() {
                    resolve();
                    await navigation.complete;

                    if (went_back ? from_skip : page.state.skip_view_transition) {
                        transition.skipTransition();
                    }
                }
            });

            transition.ready.catch((e) => {
                if (e instanceof DOMException && e.name === "AbortError") {
                    return;
                }

                console.error("View transition failed: ", e);
            });
        });
    });
}
