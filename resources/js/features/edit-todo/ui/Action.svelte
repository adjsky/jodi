<script lang="ts">
    import { Tooltip } from "@ark-ui/svelte/tooltip";
    import { tw } from "$/shared/lib/styles";

    import type { WithClassName } from "$/shared/lib/styles";
    import type { HTMLButtonAttributes } from "svelte/elements";

    type Props = WithClassName<
        HTMLButtonAttributes,
        {
            tooltip: string;
        }
    >;

    const id = $props.id();
    const { tooltip, children, ...props }: Props = $props();
</script>

<Tooltip.Root {id} openDelay={0} closeDelay={0}>
    <Tooltip.Trigger
        {...props}
        class={tw(
            "p-3 text-lg disabled:not-data-loading:text-cream-400",
            props.class
        )}
    >
        {@render children?.()}
    </Tooltip.Trigger>
    <Tooltip.Positioner>
        <Tooltip.Content
            class={[
                "rounded-sm bg-cream-950 px-1.5 text-sm leading-normal font-medium text-cream-50",
                "data-[state=closed]:animate-out data-[state=closed]:fade-out",
                "data-[state=open]:animate-in data-[state=open]:fade-in"
            ]}
        >
            {tooltip}
        </Tooltip.Content>
    </Tooltip.Positioner>
</Tooltip.Root>
