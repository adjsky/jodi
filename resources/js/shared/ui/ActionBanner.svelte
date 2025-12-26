<script module lang="ts">
    type Callbacks = {
        onAccept?: VoidFunction;
        onDecline?: VoidFunction;
    };

    type Banner = Callbacks & {
        id: string;
        title: string;
        action?: string;
        closeable: boolean;
    };

    let banners: Banner[] = $state([]);

    type CreateActionBannerOptions = Callbacks & {
        action?: string;
        closeable?: boolean;
        autoclose?: boolean;
    };

    export function createActionBanner(
        title: string,
        options?: CreateActionBannerOptions
    ) {
        const {
            action,
            closeable = false,
            autoclose = true,
            onAccept,
            onDecline
        } = options ?? {};

        const id = crypto.randomUUID();

        banners.push({
            id,
            title,
            action,
            closeable,
            onAccept() {
                if (autoclose) {
                    destroyActionBanner(id);
                }
                onAccept?.();
            },
            onDecline() {
                if (autoclose) {
                    destroyActionBanner(id);
                }
                onDecline?.();
            }
        });

        return id;
    }

    export function destroyActionBanner(id: string) {
        banners = banners.filter((banner) => id != banner.id);
    }
</script>

<script lang="ts">
    import { X } from "@lucide/svelte";

    import Button from "./Button.svelte";

    const banner = $derived(banners[0]);
</script>

{#if banner}
    <div
        class="relative flex items-center justify-between gap-2 rounded-b-xl border border-cream-950 bg-white px-4 py-2"
    >
        <h3 class={["text-ms font-semibold", banner.closeable && "mr-5 "]}>
            {banner.title}
        </h3>
        {#if banner.closeable}
            <button
                class="absolute top-0.5 right-1 p-2"
                onclick={() => banner.onDecline?.()}
            >
                <X class="text-xl" />
            </button>
        {/if}
        {#if banner.action}
            <Button
                class="h-auto w-max rounded-lg px-2 py-1 text-ms"
                onclick={() => banner.onAccept?.()}
            >
                {banner.action}
            </Button>
        {/if}
    </div>
{/if}
