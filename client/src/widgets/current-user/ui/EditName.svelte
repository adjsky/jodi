<script lang="ts">
    import { AtSign } from "@lucide/svelte";
    import { SessionCache } from "#entities/user/mod.ts";
    import { m } from "#paraglide/messages.js";
    import { Overlay } from "#shared/capabilities/overlay/mod.svelte.ts";
    import { VirtualKeyboard } from "#shared/capabilities/virtual-keyboard/mod.ts";
    import ApiForm from "#shared/ui/ApiForm.svelte";
    import Button from "#shared/ui/Button.svelte";
    import { ScreenView } from "#shared/ui/composites/screen-view/mod.ts";
    import TextField from "#shared/ui/TextField.svelte";
    import UpdateUserName from "#wayfinder/actions/App/Domain/Identity/Actions/UpdateUserName.ts";
    import z from "zod";

    import type { ViewProps } from "../model/types.ts";

    let { open = $bindable(), user }: ViewProps = $props();
</script>

<ScreenView.Overlay bind:open {@attach VirtualKeyboard.retainFocus()}>
    <ScreenView.Header title={m["current-user.account.name"]()} />
    <ScreenView.Content class="mt-5">
        <ApiForm
            action={UpdateUserName()}
            class="flex grow flex-col"
            inputSchema={z.object({
                name: z
                    .string()
                    .min(1, { error: m["current-user.name.errors.name"]() })
            })}
            onSuccess={async (_, input) => {
                await SessionCache.update({ "user.name": input.name });
                Overlay.close();
            }}
        >
            {#snippet children({ errors, submitting, isDirty })}
                <TextField
                    type="text"
                    name="name"
                    placeholder={m["current-user.account.name"]()}
                    value={user.name}
                    error={errors.name?.[0]}
                    maxlength={36}
                    required
                >
                    {#snippet indicator()}<AtSign />{/snippet}
                </TextField>

                <Button
                    type="submit"
                    class="mt-auto shrink-0"
                    disabled={!isDirty}
                    loading={submitting}
                >
                    {m["current-user.name.save"]()}
                </Button>
            {/snippet}
        </ApiForm>
    </ScreenView.Content>
</ScreenView.Overlay>
