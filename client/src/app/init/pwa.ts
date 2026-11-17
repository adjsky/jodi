export function init(): void {
    const { needRefresh, updateServiceWorker } = useRegisterSW();
    const need_refresh = fromStore(needRefresh);

    watch(
        () => [need_refresh.current],
        () => {
            if (!need_refresh.current) {
                return;
            }

            createActionBanner(m["pwa-update.title"](), {
                action: m["pwa-update.action"](),
                onAccept() {
                    progress.reveal(true);
                    progress.start();
                    void updateServiceWorker();
                },
                autoclose: false
            });
        }
    );

    onMount(() => {
        if (!("serviceWorker" in navigator)) {
            return;
        }

        function handle_message(event: MessageEvent) {
            if (
                typeof event.data !== "object" ||
                event.data === null ||
                event.data.messageType !== "notification-clicked"
            ) {
                return;
            }

            this.action.handle(event.data.data);
        }

        navigator.serviceWorker.addEventListener("message", handle_message);

        return () => navigator.serviceWorker.removeEventListener("message", handle_message);
    });
}
