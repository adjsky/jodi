<script lang="ts">
    import BasicMenu from "./BasicMenu.svelte";
    import CustomConfigurator from "./CustomConfigurator.svelte";

    type Props = {
        tooltip: string;
        notify_before: string;
        disabled?: boolean;
        on_disabled_attempt?: () => void | boolean;
    };

    let {
        tooltip,
        notify_before: current = $bindable(),
        disabled = false,
        on_disabled_attempt
    }: Props = $props();

    let is_basic_open = $state(false);
    let is_custom_open = $state(false);
</script>

<BasicMenu
    bind:open={
        () => is_basic_open,
        (v) => {
            if (disabled) return on_disabled_attempt?.();
            is_basic_open = v;
        }
    }
    {current}
    {tooltip}
    {disabled}
    on_select={(duration) => (current = duration)}
    on_custom_trigger={() => {
        is_basic_open = false;
        is_custom_open = true;
    }}
/>

<CustomConfigurator
    bind:open={is_custom_open}
    {current}
    on_select={(duration) => (current = duration)}
/>
