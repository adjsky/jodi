<script lang="ts">
    import Checkbox from "./Checkbox.svelte";
    import Confirmable from "./Confirmable.svelte";

    import type { MaybePromise, Scope } from "../lib/types";
    import type { Snippet } from "svelte";
    import type { HTMLAttributes } from "svelte/elements";

    type Props = {
        title?: string;
        scopeLabels?: { this: string; following?: string; all: string };
        open?: boolean;
        portal?: boolean;
        fallback?: boolean;
        skip?: boolean;
        trigger?: Snippet<[() => HTMLAttributes<HTMLElement>]>;
        onConfirm?: (scope: Scope) => MaybePromise<boolean | void>;
        onAbort?: VoidFunction;
    };

    let {
        title = "",
        scopeLabels,
        open = $bindable(),
        portal,
        fallback,
        skip,
        trigger,
        onConfirm,
        onAbort
    }: Props = $props();

    let selectedScope: Scope = $state("this");
</script>

{#if skip}
    {@render trigger?.(() => ({}))}
{:else}
    <Confirmable
        {title}
        {portal}
        {onAbort}
        bind:open
        onConfirm={() => onConfirm?.(selectedScope)}
        onExitComplete={() => {
            selectedScope = "this";
        }}
    >
        {#snippet trigger(props)}
            {@render trigger?.(props)}
        {/snippet}
        {#snippet content()}
            {#if !fallback}
                <div class="mt-2">
                    {#each ["this", "following", "all"] as const as scope (scope)}
                        {@const label = scopeLabels?.[scope]}
                        {#if label}
                            <Checkbox
                                {label}
                                checked={selectedScope == scope}
                                onclick={() => (selectedScope = scope)}
                            />
                        {/if}
                    {/each}
                </div>
            {/if}
        {/snippet}
    </Confirmable>
{/if}
