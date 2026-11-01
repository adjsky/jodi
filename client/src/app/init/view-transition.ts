import { onNavigate } from "$app/navigation";
import { page } from "$app/state";

export function init(): void {
    onNavigate((navigation) => {
        if (!document.startViewTransition) {
            return;
        }

        const wentBack = navigation.delta !== undefined && navigation.delta < 0;
        const fromSkip = page.state.skipViewTransition;

        return new Promise((resolve) => {
            const transition = document.startViewTransition({
                types: [wentBack ? "back" : "forward"],
                async update() {
                    resolve();
                    await navigation.complete;

                    if (wentBack ? fromSkip : page.state.skipViewTransition) {
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
