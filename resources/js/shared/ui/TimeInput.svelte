<script lang="ts">
    import "imask/esm/masked/range";

    import { m } from "$/paraglide/messages";
    import IMask from "imask/esm/imask";

    import { tw } from "../lib/styles";

    import type { HTMLInputAttributes } from "svelte/elements";

    type Props = HTMLInputAttributes & {
        // preferNativeWidget?: boolean
    };

    let { name, ...props }: Props = $props();

    let node = $state<HTMLInputElement | null>(null);
    let formValue = $state<string>("");

    $effect(() => {
        if (!node) {
            return;
        }

        const mask = IMask(node, {
            mask: "HH:MM",
            blocks: {
                HH: {
                    mask: IMask.MaskedRange,
                    from: 0,
                    to: 23,
                    maxLength: 2,
                    placeholderChar: "-"
                },
                MM: {
                    mask: IMask.MaskedRange,
                    from: 0,
                    to: 59,
                    maxLength: 2,
                    placeholderChar: "-"
                }
            },

            lazy: false,
            autofix: true
        });

        mask.on("accept", () => {
            formValue = mask.masked.isComplete ? mask.value : "";
            updateValidity();
        });

        function updateValidity() {
            if (props.required && !mask.masked.isComplete) {
                node?.setCustomValidity(m["common.required-field"]());
            } else {
                node?.setCustomValidity("");
            }
        }

        updateValidity();

        return () => mask.destroy();
    });
</script>

<input
    bind:this={node}
    {...props}
    type="text"
    inputmode="numeric"
    class={tw(
        "form-input w-20 rounded-lg border border-cream-300! bg-cream-100 px-3 py-1.25 text-center text-[length:inherit] font-bold outline-none focus:ring-0",
        props.class
    )}
/>

{#if name}
    <input hidden bind:value={formValue} {name} />
{/if}
