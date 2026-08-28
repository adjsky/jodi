<script lang="ts">
    import { router } from "@inertiajs/core";
    import { tw } from "$/shared/lib/styles/tw";
    import { useLoadingDebounce } from "$/shared/lib/svelte/use-loading-debounce.svelte";
    import Loader from "$/shared/ui/Loader.svelte";

    import type { UrlMethodPair, VisitOptions } from "@inertiajs/core";
    import type { ClassName } from "$/shared/lib/styles/tw";
    import type { Snippet } from "svelte";

    type Props = VisitOptions & {
        href: string | UrlMethodPair;
        class: ClassName;
        loadingIndicator?: boolean;
        startIcon?: Snippet;
        endIcon?: Snippet;
        children?: Snippet;
    };

    const {
        href,
        class: classname,
        loadingIndicator,
        startIcon,
        endIcon,
        children,
        ...options
    }: Props = $props();

    let isLoading = $state(false);
    const isLoaderVisible = useLoadingDebounce(() => isLoading);

    function onClick() {
        router.visit(href, {
            ...options,
            ...(loadingIndicator && {
                showProgress: false,
                onStart(visit) {
                    isLoading = true;
                    options?.onStart?.(visit);
                },
                onFinish(visit) {
                    isLoading = false;
                    options?.onFinish?.(visit);
                }
            })
        });
    }
</script>

<button
    type="button"
    class={tw(
        "flex h-14 w-full items-center justify-between border-cream-300 text-lg font-medium not-last:border-b disabled:cursor-not-allowed",
        classname
    )}
    onclick={onClick}
    aria-busy={isLoading}
>
    {#if isLoaderVisible.current}
        <Loader />
    {:else}
        <span class="flex items-center gap-2">
            {@render startIcon?.()}
            {@render children?.()}
        </span>
        {@render endIcon?.()}
    {/if}
</button>
