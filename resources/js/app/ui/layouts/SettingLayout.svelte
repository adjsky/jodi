<script lang="ts">
    import { Link } from "@inertiajs/svelte";
    import { ChevronLeft } from "@lucide/svelte";
    import { index } from "$/generated/actions/App/Http/Controllers/CurrentUserController";
    import { useHandleRouterException } from "$/shared/inertia/use-handle-router-exception.svelte";
    import { toaster } from "$/shared/lib/toast";
    import Toaster from "$/shared/ui/Toaster.svelte";

    import type { Snippet } from "svelte";

    type Props = {
        title: string;
        children: Snippet;
    };

    const { title, children }: Props = $props();

    useHandleRouterException();
</script>

<Toaster {toaster} />

<main class="flex min-h-svh flex-col bg-cream-50 px-4 py-3">
    <header class="relative flex items-center">
        <Link href={index()} class="p-2">
            <ChevronLeft class="text-4xl" />
        </Link>
        <span
            class="absolute top-1/2 left-1/2 -translate-1/2 text-xl font-bold"
        >
            {title}
        </span>
    </header>
    {@render children()}
</main>
