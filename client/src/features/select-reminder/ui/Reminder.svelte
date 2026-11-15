<script lang="ts">
    import BasicMenu from "./BasicMenu.svelte";
    import CustomConfigurator from "./CustomConfigurator.svelte";

    type Props = {
        tooltip: string;
        notifyBefore: string;
        disabled?: boolean;
        onDisabledAttempt?: () => void | boolean;
    };

    let {
        tooltip,
        notifyBefore: current = $bindable(),
        disabled = false,
        onDisabledAttempt
    }: Props = $props();

    let isBasicOpen = $state(false);
    let isCustomOpen = $state(false);
</script>

<BasicMenu
    bind:open={
        () => isBasicOpen,
        (v) => {
            if (disabled) return onDisabledAttempt?.();
            isBasicOpen = v;
        }
    }
    {current}
    {tooltip}
    {disabled}
    onSelect={(duration) => (current = duration)}
    onCustomTrigger={() => {
        isBasicOpen = false;
        isCustomOpen = true;
    }}
/>

<CustomConfigurator
    bind:open={isCustomOpen}
    {current}
    onSelect={(duration) => (current = duration)}
/>
