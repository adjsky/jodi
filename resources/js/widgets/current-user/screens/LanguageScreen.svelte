<script lang="ts">
    import { setLocale } from "$/paraglide/runtime";
    import { LANGUAGES } from "$/shared/lib/language";

    import Layout from "./Layout.svelte";

    import type { LayoutProps } from "../model/types";
    import type { Locale } from "$/paraglide/runtime";

    const props: LayoutProps = $props();
</script>

<Layout {...props}>
    {#each Object.entries(LANGUAGES) as [locale, language] (locale)}
        <button onclick={() => setLocale(locale as Locale)}>{language}</button>
    {/each}
</Layout>
