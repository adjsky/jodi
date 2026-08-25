<script lang="ts">
    import { Form } from "@inertiajs/svelte";
    import { ChevronRight, Mail } from "@lucide/svelte";
    import CreateRegistrationInvitation from "$/generated/actions/App/Domain/Identity/Actions/CreateRegistrationInvitation";
    import { m } from "$/paraglide/messages";
    import Dino from "$/shared/assets/dino.svg";
    import SadCat from "$/shared/assets/sad-cat.svg";
    import { ScreenView } from "$/shared/composites/screen-view";
    import { VirtualKeyboard } from "$/shared/services/virtual-keyboard";
    import Button from "$/shared/ui/Button.svelte";
    import Skeleton from "$/shared/ui/Skeleton.svelte";
    import TextField from "$/shared/ui/TextField.svelte";
    import { onMount } from "svelte";

    import { context } from "../model/context";
    import { buildViewName, view } from "../model/view";
    import { back } from "./Back.svelte";
    import Invitation from "./Invitation.svelte";

    import type { RegistrationInvitationData } from "$/entities/user";

    const { invitations } = context.get();

    onMount(() => {
        if (invitations.current && !invitations.loading && !invitations.error) {
            void invitations.refetch();
        }
    });

    let inviteInput = $state<HTMLInputElement | null>(null);

    let isError = $derived(!invitations.current && invitations.error);
    let isLoading = $derived(!invitations.current && invitations.loading);
</script>

<ScreenView.Overlay>
    <ScreenView.Header {back} title={m["current-user.account.invitations"]()} />
    <ScreenView.Content
        class={[
            "overflow-y-auto overscroll-contain pt-5",
            invitations.current?.length === 0 || isError
                ? "justify-center"
                : "gap-2"
        ]}
        aria-live="polite"
        aria-busy={invitations.loading}
    >
        {#if isError}
            <img
                src={SadCat}
                width={82}
                height={85}
                alt=""
                decoding="async"
                class="mx-auto w-full max-w-28"
            />
            <p class="mx-auto mt-4 max-w-3/4 text-center text-lg font-medium">
                {m["current-user.invitations.error"]()}
            </p>
        {:else if isLoading}
            {#each Array.from({ length: 5 }) as _, idx (idx)}
                {@render row()}
            {/each}
        {:else}
            {#each invitations.current as invitation (invitation.id)}
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
                await view.push(buildViewName("invitations", "add"));
                inviteInput?.focus();
            }}
            disabled={!invitations.current}
        >
            {m["current-user.invitations.add"]()}
        </Button>
    </ScreenView.Footer>
</ScreenView.Overlay>

{#if view.isOpen(buildViewName("invitations", "add"))}
    <ScreenView.Overlay {@attach VirtualKeyboard.retainFocus()}>
        <ScreenView.Header
            {back}
            title={m["current-user.invitations.invite"]()}
        />
        <ScreenView.Content class="mt-5">
            <Form
                action={CreateRegistrationInvitation()}
                class="flex grow flex-col"
                options={{
                    replace: true,
                    preserveUrl: true,
                    only: ["flash", "me"]
                }}
                onSuccess={(page) => {
                    invitations.mutate([
                        ...(invitations.current ?? []),
                        page.props.flash.invitation
                    ]);

                    void view.back();
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
                    maxlength={254}
                    required
                >
                    {#snippet indicator()}<Mail />{/snippet}
                </TextField>

                <Button
                    type="submit"
                    class="mt-auto shrink-0"
                    disabled={processing}
                >
                    {m["current-user.invitations.invite"]()}
                </Button>
            </Form>
        </ScreenView.Content>
    </ScreenView.Overlay>
{:else if /invitations\/.+$/.test(view.name)}
    <Invitation
        resource={invitations}
        onDelete={(id) => {
            if (!invitations.current) {
                return;
            }

            invitations.mutate(invitations.current.filter((i) => i.id != id));
        }}
    />
{/if}

{#snippet row(invitation?: RegistrationInvitationData)}
    <button
        type="button"
        onclick={() => {
            if (!invitation) {
                return;
            }
            void view.push(buildViewName("invitations", invitation.id));
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
