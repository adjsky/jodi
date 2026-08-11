<script lang="ts">
    import { Portal } from "@ark-ui/svelte";
    import { Device } from "@capacitor/device";
    import { SplashScreen } from "@capacitor/splash-screen";
    import { page, router } from "@inertiajs/svelte";
    import { DEVICE_ID_COOKIE } from "$/shared/cfg/constants";
    import { Swiper } from "$/shared/integrations/swiper";
    import { Push } from "$/shared/services/push";
    import { PWA } from "$/shared/services/pwa";
    import ToastProvider from "$/shared/ui/ToastProvider.svelte";
    import { initializeApp } from "firebase/app";
    import Cookies from "js-cookie";
    import { onMount } from "svelte";
    import { get } from "svelte/store";

    import type { Snippet } from "svelte";

    const { children }: { children: Snippet } = $props();

    onMount(() => {
        void SplashScreen.hide();
    });

    onMount(() => {
        async function synchronize() {
            if (Cookies.get(DEVICE_ID_COOKIE) == null) {
                const { identifier } = await Device.getId();

                Cookies.set(DEVICE_ID_COOKIE, identifier, {
                    sameSite: "lax",
                    expires: 365,
                    secure: get(page).props.environment == "production"
                });

                await router.reload({
                    async: true,
                    showProgress: false,
                    replace: true,
                    preserveUrl: true,
                    only: ["auth"]
                });
            }

            await Push.subscription.synchronize();
        }

        initializeApp(get(page).props.config.firebase);

        void synchronize();

        const unlisten = Push.subscription.listen();

        return () => unlisten.then((c) => c());
    });

    PWA.init();
    Swiper.init();
</script>

<Portal>
    <ToastProvider />
</Portal>

{@render children()}
