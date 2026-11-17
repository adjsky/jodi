<script lang="ts">
    import Checkbox from "#shared/ui/Checkbox.svelte";
    import Confirmable from "#shared/ui/Confirmable.svelte";

    import type { RecurrenceScope } from "../model/types";
    import type { Snippet } from "svelte";
    import type { HTMLAttributes } from "svelte/elements";

    type Props = {
        title?: string;
        scope_labels?: { this: string; following?: string; all: string };
        open?: boolean;
        portal?: boolean;
        fallback?: boolean;
        skip?: boolean;
        trigger?: Snippet<[() => HTMLAttributes<HTMLElement>]>;
        on_confirm?: (scope: RecurrenceScope) => boolean | void;
        on_exit_complete?: VoidFunction;
        on_abort?: VoidFunction;
    };

    let {
        title = "",
        scope_labels,
        open = $bindable(),
        portal,
        fallback,
        skip,
        trigger,
        on_confirm,
        on_exit_complete,
        on_abort
    }: Props = $props();

    let selected_scope: RecurrenceScope = $state("this");
</script>

{#if skip}
    {@render trigger?.(() => ({}))}
{:else}
    <Confirmable
        bind:open
        {title}
        {portal}
        {on_abort}
        on_confirm={() => on_confirm?.(selected_scope)}
        on_exit_complete={() => {
            selected_scope = "this";
            on_exit_complete?.();
        }}
    >
        {#snippet trigger(props)}
            {@render trigger?.(props)}
        {/snippet}
        {#snippet content()}
            {#if !fallback}
                <div class="mt-2">
                    {#each ["this", "following", "all"] as const as scope (scope)}
                        {@const label = scope_labels?.[scope]}
                        {#if label}
                            <Checkbox
                                {label}
                                checked={selected_scope === scope}
                                onclick={() => (selected_scope = scope)}
                            />
                        {/if}
                    {/each}
                </div>
            {/if}
        {/snippet}
    </Confirmable>
{/if}
