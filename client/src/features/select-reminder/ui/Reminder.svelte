<script lang="ts">
    import { m } from "#paraglide/messages.js";
    import { NotificationCoordinator } from "#shared/capabilities/notification-coordinator/mod.svelte.ts";
    import { PLATFORM } from "#shared/cfg/capacitor.ts";
    import { toaster } from "#shared/ui/toaster.ts";

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
            if (PLATFORM === "web") {
                toaster.info(m["reminders.not-supported-in-web"]());
                return;
            }
            if (NotificationCoordinator.permission === "denied") {
                toaster.info(m["notification.permission-denied.native"]());
                return;
            }
            if (NotificationCoordinator.permission !== "granted") {
                toaster.info(m["notification.no-permission.title"](), {
                    action: {
                        label: m["notification.no-permission.action"](),
                        onclick() {
                            void NotificationCoordinator.enable();
                        }
                    }
                });
                return;
            }
            if (disabled) {
                on_disabled_attempt?.();
                return;
            }
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
