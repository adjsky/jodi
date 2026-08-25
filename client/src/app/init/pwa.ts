export function init(): void {
    const { needRefresh: _needRefresh, updateServiceWorker } = useRegisterSW();
    const needRefresh = fromStore(_needRefresh);

    watch(
        () => [needRefresh.current],
        () => {
            if (!needRefresh.current) {
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

        function handleMessage(event: MessageEvent) {
            if (
                typeof event.data !== "object" ||
                event.data === null ||
                event.data.messageType !== "notification-clicked"
            ) {
                return;
            }

            this.action.handle(event.data.data);
        }

        navigator.serviceWorker.addEventListener("message", handleMessage);

        return () =>
            navigator.serviceWorker.removeEventListener(
                "message",
                handleMessage
            );
    });
}
