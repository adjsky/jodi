<script lang="ts">
    import { router } from "@inertiajs/svelte";
    import { Check } from "@lucide/svelte";
    import { tw } from "$/shared/lib/styles/tw";
    import { Haptics } from "$/shared/services/haptics";
    import { boolAttr } from "runed";

    import type { UrlMethodPair, VisitOptions } from "@inertiajs/core";
    import type { ClassName } from "$/shared/lib/styles/tw";

    type Props = VisitOptions & {
        href: string | UrlMethodPair;
        class?: ClassName;
        completedAt: string | null;
        occursAt: string | null;
    };

    const {
        class: classname,
        completedAt,
        occursAt,
        href,
        ...options
    }: Props = $props();

    function onClick() {
        void Haptics.impact("medium");

        router.visit(href, {
            ...options,
            only: ["todos"],
            data: { occursAt },
            showProgress: false
        });
    }
</script>

<button
    type="button"
    class={tw(
        "group flex size-5.5 shrink-0 items-center justify-center rounded-full border border-cream-950 text-ms data-completed:bg-cream-950 data-completed:text-cream-50",
        classname
    )}
    data-completed={boolAttr(completedAt)}
    onclick={onClick}
>
    <Check class="group-not-data-completed:hidden" />
</button>
