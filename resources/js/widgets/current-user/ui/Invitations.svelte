<script lang="ts">
    import { Form, progress } from "@inertiajs/svelte";
    import { ChevronRight, Mail } from "@lucide/svelte";
    import { invite } from "$/generated/actions/App/Http/Controllers/RegistrationInvitationController";
    import { m } from "$/paraglide/messages";
    import Dino from "$/shared/assets/dino.svg";
    import { toaster } from "$/shared/lib/toaster";
    import Button from "$/shared/ui/Button.svelte";
    import FloatingView from "$/shared/ui/FloatingView.svelte";
    import TextField from "$/shared/ui/TextField.svelte";
    import { resource } from "runed";

    import { fetchInvitations } from "../api/invitations";
    import { buildViewName, view } from "../model/view";
    import { back } from "./Back.svelte";
    import Invitation from "./Invitation.svelte";

    let inviteInput = $state<HTMLInputElement | null>(null);

    const invitations = resource(() => [], fetchInvitations);

    $effect(() => {
        if (invitations.error) {
            toaster.error(m["common.unexpected-error"]());
        }
    });
</script>

<FloatingView {back} title={m["current-user.account.invitations"]()}>
    <div
        class="flex grow flex-col justify-between gap-5 overflow-y-scroll py-5"
    >
        <div
            class={[
                "flex grow flex-col overflow-y-scroll pb-5",
                invitations.current?.length == 0 ? "justify-center" : "gap-2"
            ]}
        >
            {#if invitations.current?.length === 0}
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
                {#each invitations.current as invitation (invitation.email)}
                    <button
                        onclick={() => {
                            void view.push(
                                buildViewName("invitations", invitation.id)
                            );
                        }}
                        class="border-gray-950 flex items-center justify-between rounded-xl border bg-white px-4 py-3"
                    >
                        <span class="flex flex-col items-start">
                            <p class="font-semibold">
                                {m["current-user.invitations.waiting"]()}
                            </p>
                            <p class="text-sm text-cream-400">
                                {invitation.email}
                            </p>
                        </span>
                        <ChevronRight class="text-xl" />
                    </button>
                {/each}
            {/if}
        </div>
        <Button
            class="shrink-0"
            onclick={async () => {
                await view.push(buildViewName("invitations", "add"));
                inviteInput?.focus();
            }}
            disabled={!invitations.current}
        >
            {m["current-user.invitations.add"]()}
        </Button>
    </div>
</FloatingView>

{#if view.isOpen(buildViewName("invitations", "add"))}
    <FloatingView {back} title={m["current-user.invitations.invite"]()}>
        <Form
            action={invite()}
            class="flex grow flex-col justify-between py-5"
            options={{
                replace: true,
                preserveUrl: true,
                only: ["flash", "me"]
            }}
            onSuccess={async () => {
                try {
                    progress.reveal(true);
                    progress.start();
                    await invitations.refetch();
                    progress.finish();
                    view.back();
                } catch (e) {
                    console.error(e);
                    progress.remove();
                    toaster.error(m["common.unexpected-error"]());
                }
            }}
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
                {m["current-user.invitations.invite"]()}
            </Button>
        </Form>
    </FloatingView>
{:else if /invitations\/.+$/.test(view.name)}
    <Invitation
        onDelete={(id) => {
            if (!invitations.current) {
                return;
            }

            invitations.mutate(invitations.current.filter((i) => i.id != id));
        }}
    />
{/if}
