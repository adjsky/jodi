<script lang="ts">
    import "../app.css";

    import { Portal } from "@ark-ui/svelte";
    import { SplashScreen } from "@capacitor/splash-screen";
    import { QueryClient, QueryClientProvider } from "@tanstack/svelte-query";
    import { SvelteQueryDevtools } from "@tanstack/svelte-query-devtools";
    import { m } from "#paraglide/messages.js";
    import { Swiper } from "#shared/capabilities/swiper/mod.ts";
    import { VirtualKeyboard } from "#shared/capabilities/virtual-keyboard/mod.ts";
    import { onMount } from "svelte";
    import { Toaster } from "svelte-sonner";

    const { children } = $props();

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

    // $effect.pre(() => {
    //     const locale = getLocale();

    //     document.documentElement.lang = locale;
    //     document.documentElement.dir = getTextDirection(locale);
    // });

    Swiper.init();
    VirtualKeyboard.init();
</script>

<svelte:head>
    <title>{m["common.app-title"]()}</title>
</svelte:head>

<QueryClientProvider client={queryClient}>
    <Portal>
        <Toaster />
        <SvelteQueryDevtools buttonPosition="bottom-left" />
    </Portal>

    {@render children()}

    <div id="portal-root"></div>
</QueryClientProvider>
