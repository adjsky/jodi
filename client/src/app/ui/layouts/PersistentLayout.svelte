<script lang="ts">
    import { Portal } from "@ark-ui/svelte";
    import { Device } from "@capacitor/device";
    import { SplashScreen } from "@capacitor/splash-screen";
    import { page } from "@inertiajs/svelte";
    import { QueryClient, QueryClientProvider } from "@tanstack/svelte-query";
    import { DEVICE_ID_COOKIE } from "$/shared/cfg/constants";
    import { reload } from "$/shared/integrations/inertia/visit";
    import { Swiper } from "$/shared/integrations/swiper";
    import { Push } from "$/shared/services/push";
    import { PWA } from "$/shared/services/pwa";
    import { VirtualKeyboard } from "$/shared/services/virtual-keyboard";
    import ToastProvider from "$/shared/ui/ToastProvider.svelte";
    import { initializeApp } from "firebase/app";
    import Cookies from "js-cookie";
    import { onMount } from "svelte";

    import type { Snippet } from "svelte";

    const { children }: { children: Snippet } = $props();

    const queryClient = new QueryClient({
        defaultOptions: {
            queries: {
                staleTime: 60_000,
                gcTime: 30 * 60_000,
                retry: false,
                refetchOnWindowFocus: false,
                refetchOnReconnect: true
            },
            mutations: {
                retry: false
            }
        }
    });

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
                    secure: page.props.environment == "production"
                });

                await reload({
                    async: true,
                    showProgress: false,
                    replace: true,
                    preserveUrl: true,
                    only: ["auth"]
                });
            }

            await Push.subscription.synchronize();
        }

        initializeApp(page.props.config.firebase);

        void synchronize();

        const unlisten = Push.subscription.listen();

        return () => unlisten.then((c) => c());
    });

    PWA.init();
    Swiper.init();
    VirtualKeyboard.init();
</script>

<QueryClientProvider client={queryClient}>
    <Portal>
        <ToastProvider />
    </Portal>

    {@render children()}
</QueryClientProvider>
