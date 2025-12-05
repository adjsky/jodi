<script lang="ts">
    import Layout from "./Layout.svelte";

    import type { LayoutProps } from "../model/types";

    type Props = LayoutProps & {
        email: string;
    };

    const { email, ...props }: Props = $props();
</script>

<Layout {...props}>{email}</Layout>
