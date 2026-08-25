<script lang="ts">
    import { Form, page } from "@inertiajs/svelte";
    import { AtSign } from "@lucide/svelte";
    import UpdateUser from "$/generated/actions/App/Domain/Identity/Actions/UpdateUser";
    import { m } from "$/paraglide/messages";
    import { ScreenView } from "$/shared/composites/screen-view";
    import { VirtualKeyboard } from "$/shared/services/virtual-keyboard";
    import Button from "$/shared/ui/Button.svelte";
    import TextField from "$/shared/ui/TextField.svelte";

    import { view } from "../model/view";

    import type { ViewProps } from "../model/view";

    let { open = $bindable() }: ViewProps = $props();

    const user = $derived(page.props.auth.user);
</script>

<ScreenView.Overlay bind:open {@attach VirtualKeyboard.retainFocus()}>
    <ScreenView.Header title={m["current-user.account.name"]()} />
    <ScreenView.Content class="mt-5">
        <Form
            action={UpdateUser()}
            class="flex grow flex-col"
            options={{
                only: ["auth"],
                replace: true,
                preserveUrl: true
            }}
            showProgress={false}
            onSuccess={() => view.back()}
        >
            {#snippet children({ processing, errors, isDirty })}
                <TextField
                    type="text"
                    name="name"
                    placeholder={m["current-user.account.name"]()}
                    error={errors.name}
                    defaultValue={user.name}
                    maxlength={36}
                    required
                >
                    {#snippet indicator()}<AtSign />{/snippet}
                </TextField>

                <Button
                    type="submit"
                    class="mt-auto shrink-0"
                    disabled={!isDirty}
                    loading={processing}
                >
                    {m["current-user.name.save"]()}
                </Button>
            {/snippet}
        </Form>
    </ScreenView.Content>
</ScreenView.Overlay>
