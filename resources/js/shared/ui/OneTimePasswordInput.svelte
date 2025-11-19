<script lang="ts">
    import { PinInput } from "@ark-ui/svelte/pin-input";
    import { tw } from "$/shared/lib/styles";

    import type { HTMLInputAttributes } from "svelte/elements";
    import type { Except } from "type-fest";

    type Props = Except<HTMLInputAttributes, "children"> & {
        error?: boolean;
        disabled?: boolean;
        name?: string;
    };

    const { error, disabled, name, ...props }: Props = $props();
</script>

<PinInput.Root
    {disabled}
    {name}
    invalid={error}
    type="numeric"
    placeholder="*"
    autoFocus
    required
    otp
>
    <PinInput.Control class="grid grid-cols-6 gap-2">
        {#each Array.from({ length: 6 }) as _, index (index)}
            <PinInput.Input
                {index}
                class={tw(
                    "form-input h-15 rounded-xl border border-cream-950 bg-white text-center text-xl placeholder:text-cream-600 focus:border-brand focus:ring-brand",
                    error && "border-red"
                )}
            />
        {/each}
    </PinInput.Control>
    <PinInput.HiddenInput {...props} />
</PinInput.Root>
