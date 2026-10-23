<script>
    import { api } from "#shared/api/client.ts";
    import { LocalSession } from "#shared/capabilities/local-session/mod.ts";
    import LogoutUser from "#wayfinder/actions/App/Domain/Identity/Actions/LogoutUser.ts";
</script>

HI!

<button
    onclick={async () => {
        const { url, method } = LogoutUser();

        await api(url, { method });
        await LocalSession.end();
    }}
>
    log out
</button>
