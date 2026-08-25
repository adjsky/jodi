<script lang="ts">
    import { User } from "$/entities/user";
    import LogoutUser from "$/generated/actions/App/Domain/Identity/Actions/LogoutUser";
    import { m } from "$/paraglide/messages";
</script>

<User.Info.ActionRow
    class="justify-center text-red"
    href={LogoutUser()}
    loadingIndicator
    viewTransition
>
    {m["current-user.log-out"]()}
</User.Info.ActionRow>
