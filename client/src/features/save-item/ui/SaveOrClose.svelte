<script lang="ts">
    import { Check, X } from "@lucide/svelte";
    import { Recurrence } from "#entities/recurrence/mod.ts";
    import { tw } from "#shared/lib/styles/tw.ts";

    import type { RecurrenceScope } from "#entities/recurrence/mod.ts";

    type Props = {
        title?: string;
        variant: "save" | "close";
        confirm?: boolean;
        scopeLabels?: { this: string; all: string };
        onConfirm?: (scope: RecurrenceScope) => void;
        onClose?: VoidFunction;
    };

    const { title, variant, confirm, scopeLabels, onConfirm, onClose }: Props =
        $props();

    let open = $state(false);
</script>

<Recurrence.ScopeDialog
    bind:open
    {title}
    {scopeLabels}
    {onConfirm}
    skip={!confirm}
>
    {#snippet trigger(props)}
        <button
            {...props()}
            type={variant === "save" ? "submit" : "button"}
            class={tw(
                "flex size-7 shrink-0 items-center justify-center rounded-full",
                variant === "close" && "bg-cream-100",
                variant === "save" && "bg-brand"
            )}
            onclick={(e) => {
                if (variant === "close") {
                    onClose?.();
                    return;
                }

                if (confirm) {
                    e.preventDefault();
                    open = true;
                }
            }}
        >
            {#if variant === "close"}
                <X class="text-xl text-cream-700" />
            {:else}
                <Check class="text-xl text-white" />
            {/if}
        </button>
    {/snippet}
</Recurrence.ScopeDialog>
