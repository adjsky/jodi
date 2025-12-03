<script lang="ts">
    import { User } from "$/entities/user";
    import { m } from "$/paraglide/messages";

    import type { AppPageProps } from "$/globals";

    type Props = { user: AppPageProps["auth"]["user"] };

    const { user }: Props = $props();
</script>

<User.Info.Block title={m["current-user.account.title"]()}>
    <User.Info.ModalRow title={m["current-user.account.name"]()}>
        {user.name}
    </User.Info.ModalRow>
    <User.Info.ModalRow title={m["current-user.account.email"]()}>
        {user.email}
    </User.Info.ModalRow>
    <User.Info.ModalRow title={m["current-user.account.friends"]()}>
        0
    </User.Info.ModalRow>
    <User.Info.ModalRow title={m["current-user.account.invitations"]()}>
        0
    </User.Info.ModalRow>
</User.Info.Block>

<User.Info.Block title={m["current-user.app-settings.title"]()} class="mt-10">
    <User.Info.ModalRow title={m["current-user.app-settings.language"]()}>
        English
    </User.Info.ModalRow>
    <User.Info.ModalRow title={m["current-user.app-settings.week-start-on"]()}>
        Monday
    </User.Info.ModalRow>
    <User.Info.ModalRow title={m["current-user.app-settings.notifications"]()}>
        All
    </User.Info.ModalRow>
</User.Info.Block>
