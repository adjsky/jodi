/// <reference lib="webworker" />

/**
 * @type {ServiceWorkerGlobalScope}
 */
const sw = self;

sw.addEventListener("push", (e) => {
    if (!e.data) return;

    const payload = e.data.json();
    if (!payload?.notification) return;

    const {
        title,
        icon = "/pwa-192x192.png",
        vibrate = [200, 100, 200],
        ...options
    } = payload.notification;

    e.waitUntil(
        sw.registration.showNotification(title, {
            icon,
            badge,
            vibrate,
            ...options
        })
    );
});

sw.addEventListener("notificationclick", (e) => {
    const { navigate } = e.notification.data ?? {};

    e.notification.close();

    async function handle() {
        const clients = await sw.clients.matchAll({
            type: "window",
            includeUncontrolled: true
        });

        let client = navigate ? clients.find((c) => c.url === navigate) : null;

        if (client) {
            return client.focus();
        }

        if (clients.length > 0) {
            client = await clients[0].focus();
            if (navigate) client.navigate?.(navigate);
            return client;
        }

        return sw.clients?.openWindow?.(navigate ?? "/");
    }

    e.waitUntil(handle());
});
