<script lang="ts">
    import Checkbox from "./Checkbox.svelte";
    import Confirmable from "./Confirmable.svelte";

    import type { MaybePromise, Scope } from "../lib/types";
    import type { Snippet } from "svelte";
    import type { HTMLAttributes } from "svelte/elements";

    type Props = {
        title: string;
        scopeLabels: { this: string; all: string };
        open?: boolean;
        portal?: boolean;
        fallback?: boolean;
        skip?: boolean;
        trigger?: Snippet<[() => HTMLAttributes<HTMLElement>]>;
        onConfirm?: (scope: Scope) => MaybePromise<boolean | void>;
        onAbort?: VoidFunction;
    };

    let {
        title,
        scopeLabels,
        open = $bindable(),
        portal,
        fallback,
        skip,
        trigger,
        onConfirm,
        onAbort
    }: Props = $props();

    const scopes = $derived([
        {
            label: scopeLabels["this"],
            value: "this"
        },
        { label: scopeLabels["all"], value: "all" }
    ] as const);

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
    >
        {#snippet trigger(props)}
            {@render trigger?.(props)}
        {/snippet}
        {#snippet content()}
            {#if !fallback}
                <div class="mt-2">
                    {#each scopes as { label, value } (value)}
                        <Checkbox
                            {label}
                            checked={selectedScope == value}
                            onclick={() => (selectedScope = value)}
                        />
                    {/each}
                </div>
            {/if}
        {/snippet}
    </Confirmable>
{/if}
