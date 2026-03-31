<script lang="ts">
    import { Check, X } from "@lucide/svelte";

    import { HistoryView } from "../inertia/history-view.svelte";
    import { tw } from "../lib/styles";
    import RecurrenceActionDialog from "./RecurrenceActionDialog.svelte";

    import type { Scope } from "../lib/types";

    type Props = {
        title?: string;
        variant: "save" | "close";
        disabled?: boolean;
        confirm?: boolean;
        scopeLabels?: { this: string; all: string };
        onConfirm?: (scope: Scope) => void;
        onClose?: VoidFunction;
    };

    const {
        title,
        variant,
        disabled,
        confirm,
        scopeLabels,
        onConfirm,
        onClose
    }: Props = $props();

    const view = new HistoryView<{ __saveitem: { isOpen: boolean } }>();
</script>

<RecurrenceActionDialog
    {title}
    {scopeLabels}
    bind:open={
        () => view.meta?.__saveitem?.isOpen ?? false,
        (v) => {
            if (!v) {
                void view.back();
            }
        }
    }
    skip={!confirm}
    onConfirm={(scope) => {
        onConfirm?.(scope);
        return true;
    }}
>
    {#snippet trigger(props)}
        <button
            {...props()}
            {disabled}
            type={variant == "save" ? "submit" : "button"}
            class={tw(
                "flex size-7 shrink-0 items-center justify-center rounded-full",
                variant == "close" && "bg-cream-100",
                variant == "save" && "bg-brand"
            )}
            onclick={(e) => {
                if (variant == "close") {
                    onClose?.();
                    return;
                }

                if (confirm) {
                    e.preventDefault();
                    void view.push(view.name, {
                        meta: { ...view.meta, __saveitem: { isOpen: true } }
                    });
                }
            }}
        >
            {#if variant == "close"}
                <X class="text-xl text-cream-700" />
            {:else}
                <Check class="text-xl text-white" />
            {/if}
        </button>
    {/snippet}
</RecurrenceActionDialog>
