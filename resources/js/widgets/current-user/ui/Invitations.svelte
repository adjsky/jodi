<script lang="ts">
    import { Form, progress } from "@inertiajs/svelte";
    import { ChevronRight, Mail } from "@lucide/svelte";
    import { invite } from "$/generated/actions/App/Http/Controllers/RegistrationInvitationController";
    import { m } from "$/paraglide/messages";
    import Dino from "$/shared/assets/dino.svg";
    import SadCat from "$/shared/assets/sad-cat.svg";
    import { toaster } from "$/shared/lib/toaster";
    import Button from "$/shared/ui/Button.svelte";
    import FloatingView from "$/shared/ui/FloatingView.svelte";
    import Skeleton from "$/shared/ui/Skeleton.svelte";
    import TextField from "$/shared/ui/TextField.svelte";
    import { resource } from "runed";

    import { fetchInvitations } from "../api/invitations";
    import { buildViewName, view } from "../model/view";
    import { back } from "./Back.svelte";
    import Invitation from "./Invitation.svelte";

    let inviteInput = $state<HTMLInputElement | null>(null);

    const invitationsResource = resource(() => [], fetchInvitations);
</script>

<FloatingView {back} title={m["current-user.account.invitations"]()}>
    <div
        class="flex grow flex-col justify-between gap-5 overflow-y-scroll py-5"
    >
        <div
            class={[
                "flex grow flex-col overflow-y-scroll pb-5",
                invitationsResource.current?.length === 0 ||
                invitationsResource.error
                    ? "justify-center"
                    : "gap-2"
            ]}
        >
            {#if invitationsResource.error}
                <img
                    src={SadCat}
                    width={82}
                    height={85}
                    alt=""
                    loading="lazy"
                    decoding="async"
                    class="mx-auto w-full max-w-28"
                />
                <p
                    class="mx-auto mt-4 max-w-3/4 text-center text-lg font-medium"
                >
                    {m["current-user.invitations.error"]()}
                </p>
            {:else if invitationsResource.loading}
                {#each Array.from({ length: 5 }) as _, idx (idx)}
                    {@render row()}
                {/each}
            {:else}
                {#each invitationsResource.current as friend (friend.id)}
                    {@render row(friend)}
                {:else}
                    <img
                        src={Dino}
                        width={187}
                        height={141}
                        alt=""
                        loading="lazy"
                        decoding="async"
                        class="mx-auto w-full max-w-28"
                    />
                    <p
                        class="mx-auto mt-4 max-w-3/4 text-center text-lg font-medium"
                    >
                        {m["current-user.invitations.no-invitations"]()}
                    </p>
                {/each}
            {/if}
        </div>
        <Button
            class="shrink-0"
            onclick={async () => {
                await view.push(buildViewName("invitations", "add"));
                inviteInput?.focus();
            }}
            disabled={!invitationsResource.current}
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
                    await invitationsResource.refetch();
                    progress.finish();
                    void view.back();
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
            if (!invitationsResource.current) {
                return;
            }

            invitationsResource.mutate(
                invitationsResource.current.filter((i) => i.id != id)
            );
        }}
    />
{/if}

{#snippet row(invitation?: App.Data.RegistrationInvitationDto)}
    <button
        onclick={() => {
            if (!invitation) {
                return;
            }
            void view.push(buildViewName("invitations", invitation.id));
        }}
        disabled={!invitation}
        class="border-gray-950 flex items-center justify-between rounded-xl border bg-white px-4 py-3"
    >
        <span class="flex flex-col items-start">
            <p class="font-semibold">
                {#if !invitation}
                    <Skeleton class="w-25" />
                {:else}
                    {m["current-user.invitations.waiting"]()}
                {/if}
            </p>
            <p class="text-sm text-cream-400">
                {#if !invitation}
                    <Skeleton class="w-40" />
                {:else}
                    {invitation.email}
                {/if}
            </p>
        </span>
        <ChevronRight class="text-xl" />
    </button>
{/snippet}
