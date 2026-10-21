import { onNavigate } from "$app/navigation";

export function init(): void {
    onNavigate((navigation) => {
        if (!document.startViewTransition) {
            return;
        }

        return new Promise((resolve) => {
            document.startViewTransition({
                types: [
                    navigation.delta !== undefined && navigation.delta < 0
                        ? "back"
                        : "forward"
                ],
                async update() {
                    resolve();
                    await navigation.complete;
                }
            });
        });
    });
}
