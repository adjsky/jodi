<script lang="ts">
    import { Switch } from "@ark-ui/svelte";

    type Props = {
        checked?: boolean;
        onchecked?: (checked: boolean) => void;
        label: string;
        name?: string;
    };

    let { checked = $bindable(), onchecked, label, name }: Props = $props();
</script>

<Switch.Root
    bind:checked
    onCheckedChange={(d) => onchecked?.(d.checked)}
    class="flex w-fit items-center gap-2"
>
    <Switch.Control
        class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full bg-cream-300 p-0.5 transition-colors duration-150 data-[state=checked]:bg-brand"
    >
        <Switch.Thumb
            class="h-5 w-5 rounded-full bg-white transition-transform duration-150 data-[state=checked]:translate-x-5"
        />
    </Switch.Control>
    <Switch.Label class="font-bold">{label}</Switch.Label>
    {#if name}
        <Switch.HiddenInput {name} />
    {/if}
</Switch.Root>
