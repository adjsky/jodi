<script lang="ts">
    import { User } from "$/entities/user";
    import { m } from "$/paraglide/messages";
    import { getLocale } from "$/paraglide/runtime";
    import { HistoryView } from "$/shared/inertia/history-view.svelte";
    import { LANGUAGES } from "$/shared/lib/language";

    import { makeScreenSnippet } from "../helpers/make-screen-snippet.svelte";
    import EmailScreen from "../screens/EmailScreen.svelte";
    import LanguageScreen from "../screens/LanguageScreen.svelte";
    import NameScreen from "../screens/NameScreen.svelte";

    import type { AppPageProps } from "$/globals";

    type Props = { user: AppPageProps["auth"]["user"] };

    const { user }: Props = $props();

    const blocks = $derived({
        [m["current-user.account.title"]()]: [
            {
                id: "name",
                title: m["current-user.account.name"](),
                value: user.name,
                screen: makeScreenSnippet(NameScreen, { name: user.name })
            },
            {
                id: "email",
                title: m["current-user.account.email"](),
                value: user.email,
                screen: makeScreenSnippet(EmailScreen, { email: user.email })
            },
            {
                id: "friends",
                title: m["current-user.account.friends"](),
                value: 0,
                screen: makeScreenSnippet(EmailScreen, { email: user.email })
            },
            {
                id: "invitations",
                title: m["current-user.account.invitations"](),
                value: 0,
                screen: makeScreenSnippet(EmailScreen, { email: user.email })
            }
        ],
        [m["current-user.app-settings.title"]()]: [
            {
                id: "language",
                title: m["current-user.app-settings.language"](),
                value: LANGUAGES[getLocale()],
                screen: makeScreenSnippet(LanguageScreen, {})
            },
            {
                id: "week-start",
                title: m["current-user.app-settings.week-start-on"](),
                value: "Monday",
                screen: makeScreenSnippet(EmailScreen, { email: user.email })
            },
            {
                id: "notifications",
                title: m["current-user.app-settings.notifications"](),
                value: "All",
                screen: makeScreenSnippet(EmailScreen, { email: user.email })
            }
        ]
    });

    const view = new HistoryView();
</script>

{#each Object.entries(blocks) as [title, rows], idx (idx)}
    <User.Info.Block {title} class={[idx != 0 && "mt-10"]}>
        {#each rows as { id, title, value, screen } (id)}
            <User.Info.ModalRow {title} onclick={() => view.open(id)}>
                {value}
            </User.Info.ModalRow>
            {#if view.isOpen(id)}
                {@render screen?.({ title, onclose: () => view.close() })}
            {/if}
        {/each}
    </User.Info.Block>
{/each}
