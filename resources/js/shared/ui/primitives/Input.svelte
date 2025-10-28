<script lang="ts">
    import { Field } from "@ark-ui/svelte/field";
    import { tw } from "$/shared/utils/tw.ts";

    import type { WithClassName } from "$/shared/utils/tw.ts";
    import type { Snippet } from "svelte";
    import type { SvelteHTMLElements } from "svelte/elements";
    import type { Except } from "type-fest";

    type Props = WithClassName<
        Except<SvelteHTMLElements["input"], "children">,
        {
            error?: string;
            indicator?: Snippet;
        }
    >;

    const { error, indicator, ...props }: Props = $props();
</script>

<Field.Root>
    <div
        class="relative flex h-15 items-center gap-2 rounded-xl border-1 border-cream-950 bg-white"
    >
        {#if indicator}
            <div
                class="pointer-events-none absolute flex h-full items-center ps-4 pe-2 text-2xl text-cream-600"
            >
                {@render indicator()}
            </div>
        {/if}
        <Field.Input
            aria-invalid={error ? "true" : undefined}
            {...props}
            class={tw(
                "form-input size-full border-none bg-transparent px-4 font-medium text-cream-950 placeholder:text-cream-600 focus:ring-0",
                indicator && "ps-12",
                props.class
            )}
        />
    </div>
    {#if error}
        <Field.ErrorText>{error}</Field.ErrorText>
    {/if}
</Field.Root>
