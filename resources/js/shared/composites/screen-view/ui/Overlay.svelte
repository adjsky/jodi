<script lang="ts">
    import { tw } from "$/shared/lib/styles/tw";
    import AppPortal from "$/shared/ui/AppPortal.svelte";

    import { stack } from "../model/stack.svelte";

    import type { SvelteHTMLElements } from "svelte/elements";

    type Props = SvelteHTMLElements["div"] & {
        portal?: boolean;
    };

    const id = $props.id();
    const { portal = true, children, ...props }: Props = $props();

    stack.register(id);
</script>

<AppPortal disabled={!portal}>
    <div
        id="floating-view-{id}"
        inert={!stack.isTop(id)}
        style:--layer-index={stack.indexOf(id)}
        {...props}
        class={tw(
            "fixed inset-0 z-[calc(100+var(--layer-index,0))] flex flex-col overflow-hidden bg-cream-50 pt-safe-offset-3 pb-safe-offset-5 safe-area-keyboard",
            props.class
        )}
    >
        {@render children?.()}
    </div>
</AppPortal>
