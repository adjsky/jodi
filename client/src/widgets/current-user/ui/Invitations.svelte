<script lang="ts">
    import { ChevronRight, Mail } from "@lucide/svelte";
    import { createQuery, useQueryClient } from "@tanstack/svelte-query";
    import { m } from "#paraglide/messages.js";
    import Dino from "#shared/assets/dino.svg";
    import { Overlay } from "#shared/capabilities/overlay/mod.svelte.ts";
    import { VirtualKeyboard } from "#shared/capabilities/virtual-keyboard/mod.ts";
    import ApiForm from "#shared/ui/ApiForm.svelte";
    import Button from "#shared/ui/Button.svelte";
    import { ScreenView } from "#shared/ui/composites/screen-view/mod.ts";
    import ResourceError from "#shared/ui/ResourceError.svelte";
    import Skeleton from "#shared/ui/Skeleton.svelte";
    import TextField from "#shared/ui/TextField.svelte";
    import CreateRegistrationInvitation from "#wayfinder/actions/App/Domain/Identity/Actions/CreateRegistrationInvitation.ts";
    import z from "zod";

    import { invitationsQueryOptions } from "../api/invitations";
    import Invitation from "./Invitation.svelte";

    import type { ViewProps } from "../model/types.ts";
    import type { RegistrationInvitationData } from "#entities/user/mod.ts";

    let { open = $bindable() }: ViewProps = $props();

    const queryClient = useQueryClient();
    const invitations = createQuery(() => ({
        ...invitationsQueryOptions,
        enabled: open
    }));

    let inviteInput = $state<HTMLInputElement | null>(null);

    const isError = $derived(!invitations.data && invitations.error);
    const isLoading = $derived(invitations.isLoading);
</script>

<ScreenView.Overlay bind:open>
    <ScreenView.Header title={m["current-user.account.invitations"]()} />
    <ScreenView.Content
        class={[
            "overflow-y-auto overscroll-contain",
            !isError && "pt-5",
            invitations.data?.length === 0 || isError
                ? "justify-center"
                : "gap-2"
        ]}
        aria-live="polite"
        aria-busy={invitations.isFetching}
    >
        {#if isError}
            <ResourceError
                message={m["current-user.invitations.errors.list"]()}
                onRetry={() => invitations.refetch()}
            />
        {:else if isLoading}
            {#each Array.from({ length: 5 }) as _, idx (idx)}
                {@render row()}
            {/each}
        {:else}
            {#each invitations.data as invitation (invitation.id)}
                {@render row(invitation)}
            {:else}
                <img
                    src={Dino}
                    width={187}
                    height={141}
                    alt=""
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
    </ScreenView.Content>
    <ScreenView.Footer class="mt-5">
        <Button
            type="button"
            onclick={async () => {
                await Overlay.open("me/invitations/add");
                inviteInput?.focus();
            }}
            disabled={!invitations.data}
        >
            {m["current-user.invitations.add"]()}
        </Button>
    </ScreenView.Footer>
</ScreenView.Overlay>

<ScreenView.Overlay
    bind:open={
        () => Overlay.isOpen("me/invitations/add"), () => Overlay.close()
    }
    {@attach VirtualKeyboard.retainFocus()}
>
    <ScreenView.Header title={m["current-user.invitations.invite"]()} />
    <ScreenView.Content class="mt-5">
        <ApiForm
            action={CreateRegistrationInvitation()}
            class="flex grow flex-col"
            inputSchema={z.object({
                email: z.email({ error: m["common.errors.email"]() })
            })}
            dataSchema={z.object({
                id: z.string(),
                email: z.string(),
                expiresAt: z.iso.datetime({ offset: true }),
                shareUrl: z.string()
            })}
            onSuccess={(invitation) => {
                queryClient.setQueryData(
                    invitationsQueryOptions.queryKey,
                    (invitations) => [...(invitations ?? []), invitation]
                );
                void Overlay.close();
            }}
        >
            {#snippet children({ submitting, errors, isDirty })}
                <TextField
                    bind:input={inviteInput}
                    type="email"
                    name="email"
                    placeholder={m["current-user.account.email"]()}
                    error={errors.email?.[0]}
                    maxlength={254}
                    required
                >
                    {#snippet indicator()}<Mail />{/snippet}
                </TextField>

                <Button
                    type="submit"
                    class="mt-auto shrink-0"
                    loading={submitting}
                    disabled={!isDirty}
                >
                    {m["current-user.invitations.invite"]()}
                </Button>
            {/snippet}
        </ApiForm>
    </ScreenView.Content>
</ScreenView.Overlay>

<Invitation />

{#snippet row(invitation?: RegistrationInvitationData)}
    <button
        type="button"
        onclick={() => {
            if (!invitation) return;
            void Overlay.open("me/invitations", {
                invitation: { id: invitation.id }
            });
        }}
        disabled={!invitation}
        class="border-gray-950 flex w-full min-w-0 items-center gap-3 rounded-xl border bg-white px-4 py-3"
    >
        <span class="flex min-w-0 grow flex-col text-left">
            <span class="font-semibold">
                {#if !invitation}
                    <Skeleton class="w-25" />
                {:else}
                    {m["current-user.invitations.waiting"]()}
                {/if}
            </span>
            <span class="truncate text-sm text-cream-400">
                {#if !invitation}
                    <Skeleton class="w-40" />
                {:else}
                    {invitation.email}
                {/if}
            </span>
        </span>
        <ChevronRight class="shrink-0 text-xl" />
    </button>
{/snippet}
