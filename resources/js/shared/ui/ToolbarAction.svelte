<script lang="ts">
    import { tw } from "$/shared/lib/styles";

    import type { HTMLButtonAttributes } from "svelte/elements";

    type Props = HTMLButtonAttributes & {
        tooltip: string;
    };

    const { tooltip: _tooltip, children, ...props }: Props = $props();
</script>

<button
    type="button"
    {...props}
    class={tw(
        "flex size-12 items-center justify-center text-xl disabled:not-data-loading:text-cream-400",
        props.class
    )}
>
    {@render children?.()}
</button>

<!-- <Tooltip.Root {id} openDelay={0} closeDelay={0}>
    <Tooltip.Trigger
        {...props}
        class={tw(
            "p-3.5 text-xl disabled:not-data-loading:text-cream-400",
            props.class
        )}
    >
        {@render children?.()}
    </Tooltip.Trigger>
    <Tooltip.Positioner>
        <Tooltip.Content
            class={[
                "rounded-sm bg-cream-950 px-1.5 leading-normal font-medium text-cream-50",
                "data-[state=closed]:animate-out data-[state=closed]:fade-out",
                "data-[state=open]:animate-in data-[state=open]:fade-in"
            ]}
        >
            {tooltip}
        </Tooltip.Content>
    </Tooltip.Positioner>
</Tooltip.Root> -->
