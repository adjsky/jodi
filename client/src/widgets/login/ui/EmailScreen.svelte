<script lang="ts">
    import { Device } from "@capacitor/device";
    import { Mail } from "@lucide/svelte";
    import { m } from "#paraglide/messages.js";
    import { Overlay } from "#shared/capabilities/overlay/mod.svelte.ts";
    import { VirtualKeyboard } from "#shared/capabilities/virtual-keyboard/mod.ts";
    import { PLATFORM } from "#shared/cfg/capacitor.ts";
    import { Timer } from "#shared/lib/svelte/timer.svelte.ts";
    import ApiForm from "#shared/ui/ApiForm.svelte";
    import Button from "#shared/ui/Button.svelte";
    import { ScreenView } from "#shared/ui/composites/screen-view/mod.ts";
    import TextField from "#shared/ui/TextField.svelte";
    import CreateLoginChallenge from "#wayfinder/actions/App/Domain/Identity/Actions/CreateLoginChallenge.ts";
    import z from "zod";

    const throttle_timer = new Timer();

    async function get_device_name() {
        const name = await Device.getInfo().then((info) => info.name ?? info.model);
        return name.trim().slice(0, 255);
    }
</script>

<ScreenView.Overlay
    bind:open={() => Overlay.is_open("login/email"), () => Overlay.close()}
    {@attach VirtualKeyboard.retain_focus()}
>
    <ScreenView.Header shape="flat" />
    <ScreenView.Content class="mt-2">
        <h1 class="text-4xl font-bold">{m["login/email.title"]()}</h1>

        <ApiForm
            action={CreateLoginChallenge()}
            class="mt-7 flex grow flex-col"
            input_schema={z.object({
                email: z.email({ error: m["common.errors.email"]() })
            })}
            data_schema={z.object({ id: z.string(), email: z.string() })}
            transform={async (input) => ({
                email: input.email,
                credential: PLATFORM === "web" ? "cookie" : "token",
                ...(PLATFORM !== "web" && { deviceName: await get_device_name() })
            })}
            on_rate_limit={(retry_after) => {
                throttle_timer.start(retry_after);
            }}
            on_success={({ id, email }) =>
                Overlay.open("login/verify", { loginchallenge: { id, email } })}
        >
            {#snippet children({ errors, submitting, is_dirty })}
                <TextField
                    type="email"
                    name="email"
                    placeholder={m["login/email.placeholder"]()}
                    error={errors.email?.[0]}
                    maxlength={254}
                    required
                >
                    {#snippet indicator()}<Mail />{/snippet}
                </TextField>

                <Button
                    type="submit"
                    class="mt-auto shrink-0"
                    disabled={throttle_timer.is_running || !is_dirty}
                    loading={submitting}
                >
                    {#if throttle_timer.is_running}
                        {m["login/email.continue-in"]({
                            seconds: throttle_timer.seconds_left
                        })}
                    {:else}
                        {m["login/email.continue"]()}
                    {/if}
                </Button>
            {/snippet}
        </ApiForm>
    </ScreenView.Content>
</ScreenView.Overlay>
