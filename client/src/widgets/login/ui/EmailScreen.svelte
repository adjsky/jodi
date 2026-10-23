<script lang="ts">
    import { Device } from "@capacitor/device";
    import { Mail } from "@lucide/svelte";
    import { m } from "#paraglide/messages.js";
    import { Overlay } from "#shared/capabilities/overlay/mod.svelte.ts";
    import { VirtualKeyboard } from "#shared/capabilities/virtual-keyboard/mod.ts";
    import { PLATFORM } from "#shared/cfg/constants.ts";
    import { Timer } from "#shared/lib/svelte/timer.svelte.ts";
    import ApiForm from "#shared/ui/ApiForm.svelte";
    import Button from "#shared/ui/Button.svelte";
    import { ScreenView } from "#shared/ui/composites/screen-view/mod.ts";
    import TextField from "#shared/ui/TextField.svelte";
    import CreateLoginChallenge from "#wayfinder/actions/App/Domain/Identity/Actions/CreateLoginChallenge.ts";
    import * as z from "zod";

    const throttleTimer = new Timer();

    async function getDeviceName() {
        const name = await Device.getInfo().then(
            (info) => info.name ?? info.model
        );
        return name.trim().slice(0, 255);
    }
</script>

<ScreenView.Overlay
    bind:open={() => Overlay.isOpen("login/email"), () => Overlay.close()}
    {@attach VirtualKeyboard.retainFocus()}
>
    <ScreenView.Header shape="flat" />
    <ScreenView.Content class="mt-2">
        <h1 class="text-4xl font-bold">{m["login/email.title"]()}</h1>

        <ApiForm
            action={CreateLoginChallenge()}
            class="mt-7 flex grow flex-col"
            inputSchema={z.object({
                email: z.email({
                    error: (issue) =>
                        issue.input === ""
                            ? m["login/email.errors.email.required"]()
                            : m["login/email.errors.email.invalid"]()
                })
            })}
            dataSchema={z.object({ id: z.string(), email: z.string() })}
            transform={async (input) => ({
                email: input.email,
                credential: PLATFORM === "web" ? "cookie" : "token",
                ...(PLATFORM !== "web" && { deviceName: await getDeviceName() })
            })}
            onRateLimit={(retryAfter) => {
                throttleTimer.start(retryAfter);
            }}
            onSuccess={({ id, email }) =>
                Overlay.open("login/verify", { loginchallenge: { id, email } })}
        >
            {#snippet children({ errors, submitting })}
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
                    disabled={throttleTimer.isRunning}
                    loading={submitting}
                >
                    {#if throttleTimer.isRunning}
                        {m["login/email.continue-in"]({
                            seconds: throttleTimer.secondsLeft
                        })}
                    {:else}
                        {m["login/email.continue"]()}
                    {/if}
                </Button>
            {/snippet}
        </ApiForm>
    </ScreenView.Content>
</ScreenView.Overlay>
