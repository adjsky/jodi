<script lang="ts">
    import { Link } from "@inertiajs/svelte";
    import { ChevronLeft } from "@lucide/svelte";
    import { index } from "$/generated/actions/App/Http/Controllers/CurrentUserController";

    import type { UrlMethodPair } from "@inertiajs/core";
    import type { Snippet } from "svelte";

    type Props = {
        title: string;
        back?: UrlMethodPair;
        viewTransition?: boolean;
        children: Snippet;
    };

    const { title, back, viewTransition = true, children }: Props = $props();
</script>

<main class="flex min-h-svh flex-col bg-cream-50 px-4 py-3">
    <div class="relative flex items-center">
        <Link href={back ?? index()} {viewTransition} class="p-2">
            <ChevronLeft class="text-4xl" />
        </Link>
        <span
            class="absolute top-1/2 left-1/2 -translate-1/2 text-xl font-bold"
        >
            {title}
        </span>
    </div>

    {@render children()}
</main>
