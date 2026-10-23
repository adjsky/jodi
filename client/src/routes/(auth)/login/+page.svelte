<script lang="ts">
    import { m } from "#paraglide/messages.js";
    import Cat from "#shared/assets/cat.svg";
    import Logo from "#shared/assets/logo.svg";
    import { Overlay } from "#shared/capabilities/overlay/mod.svelte.ts";
    import { appConfig } from "#shared/cfg/app.ts";
    import Button from "#shared/ui/Button.svelte";
    import { VerifyScreen } from "#widgets/login/mod.ts";
    import EmailScreen from "#widgets/login/ui/EmailScreen.svelte";
</script>

<main class="bottom-circle flex h-full flex-col px-4 pb-6">
    <div class="flex h-full flex-col items-center justify-center gap-2.5">
        <div class="flex gap-3">
            <img
                src={Logo}
                width={120}
                height={55}
                alt=""
                decoding="async"
                fetchpriority="high"
            />
            <img
                src={Cat}
                width={94}
                height={85}
                alt=""
                decoding="async"
                fetchpriority="high"
            />
        </div>
        <h1 class="text-center text-3xl font-bold">
            {m["login.title"]()}
        </h1>
    </div>
    <div class="mt-auto space-y-2.5">
        <Button type="button" onclick={() => Overlay.open("login/get-started")}>
            {m["login.get-started"]()}
        </Button>
        <Button
            type="button"
            variant="secondary"
            onclick={() => Overlay.open("login/email")}
        >
            {m["login.log-in"]()}
        </Button>
    </div>
    <div class="mt-6 text-center text-sm text-cream-700">
        <a href="{appConfig.url}/terms">{m["login.terms"]()}</a>
        •
        <a href="{appConfig.url}/privacy">{m["login.privacy-policy"]()}</a>
    </div>
</main>

<EmailScreen />
<VerifyScreen />
