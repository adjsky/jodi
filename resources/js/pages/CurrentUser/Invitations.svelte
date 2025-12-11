<script lang="ts">
    import { Form, Link } from "@inertiajs/svelte";
    import { ChevronRight, Mail } from "@lucide/svelte";
    import SettingsLayout, {
        header
    } from "$/app/ui/layouts/SettingLayout.svelte";
    import {
        index,
        invite,
        show
    } from "$/generated/actions/App/Http/Controllers/RegistrationInvitationController";
    import { m } from "$/paraglide/messages";
    import Dino from "$/shared/assets/dino.svg";
    import { HistoryView } from "$/shared/inertia/history-view.svelte";
    import Button from "$/shared/ui/Button.svelte";
    import TextField from "$/shared/ui/TextField.svelte";

    type Props = {
        invitations: App.Data.RegistrationInvitationDto[];
    };

    const { invitations }: Props = $props();

    let inviteInput = $state<HTMLInputElement | null>(null);

    const view = new HistoryView("invite", { viewTransition: true });
</script>

<SettingsLayout title={m["current-user.account.invitations"]()}>
    <div class="flex flex-grow flex-col justify-between py-5">
        <div
            class={[
                "flex flex-grow flex-col pb-5",
                invitations.length == 0 && "justify-center",
                invitations.length > 0 && "gap-2"
            ]}
        >
            {#if invitations.length == 0}
                <img
                    src={Dino}
                    width={187}
                    height={141}
                    alt=""
                    loading="lazy"
                    decoding="async"
                    class="mx-auto max-w-32"
                />
                <p
                    class="mx-auto mt-4 max-w-3/4 text-center text-lg font-medium"
                >
                    {m["current-user.invitations.no-invitations"]()}
                </p>
            {:else}
                {#each invitations as invitation (invitation.email)}
                    <Link
                        href={show(invitation.id)}
                        viewTransition
                        class="border-gray-950 flex items-center justify-between rounded-xl border bg-white px-4 py-3"
                    >
                        <div>
                            <p class="font-semibold">
                                {m["current-user.invitations.waiting"]()}
                            </p>
                            <p class="text-sm text-cream-400">
                                {invitation.email}
                            </p>
                        </div>
                        <ChevronRight class="text-xl" />
                    </Link>
                {/each}
            {/if}
        </div>
        <Button
            onclick={async () => {
                await view.open();
                inviteInput?.focus();
            }}
        >
            {m["current-user.add"]()}
        </Button>
    </div>

    {#if view.isOpen()}
        <div class="fixed inset-0 z-10 flex flex-col bg-cream-50 px-4 py-3">
            {@render header(m["current-user.invite"](), index())}
            <Form
                action={invite()}
                class="flex flex-grow flex-col justify-between py-5"
                let:processing
                let:errors
            >
                <TextField
                    bind:input={inviteInput}
                    type="email"
                    name="email"
                    placeholder={m["current-user.account.email"]()}
                    error={errors.email}
                    required
                >
                    {#snippet indicator()}<Mail />{/snippet}
                </TextField>
                <Button disabled={processing}>
                    {m["current-user.invite"]()}
                </Button>
            </Form>
        </div>
    {/if}
</SettingsLayout>
