<script lang="ts">
    import { Field } from "@ark-ui/svelte/field";
    import { tw } from "$/shared/lib/styles";

    import type { Snippet } from "svelte";
    import type { HTMLInputAttributes } from "svelte/elements";
    import type { Except } from "type-fest";

    type Props = Except<HTMLInputAttributes, "children"> & {
        error?: string;
        indicator?: Snippet;
        required?: boolean;
        disabled?: boolean;
        readonly?: boolean;
        input?: HTMLInputElement | null;
    };

    let {
        error,
        indicator,
        required,
        disabled,
        readonly,
        input = $bindable(null),
        ...props
    }: Props = $props();
</script>

<Field.Root invalid={Boolean(error)} {required} {disabled} readOnly={readonly}>
    <div
        class={tw(
            "relative flex h-15 items-center gap-2 rounded-xl bg-white outline outline-cream-950",
            error && "outline-red"
        )}
    >
        {#if indicator}
            <div
                class="pointer-events-none absolute flex h-full items-center ps-4 pe-2 text-2xl text-cream-600"
            >
                {@render indicator()}
            </div>
        {/if}
        <Field.Input
            bind:ref={input}
            {...props}
            class={tw(
                "form-input size-full border-none bg-transparent px-4 text-md font-semibold text-cream-950 placeholder:text-cream-600 focus:ring-0",
                indicator && "ps-12",
                props.class
            )}
        />
    </div>
    {#if error}
        <Field.ErrorText class="text-sm font-medium text-red">
            {error}
        </Field.ErrorText>
    {/if}
</Field.Root>
