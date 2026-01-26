<script lang="ts">
    import { Check } from "@lucide/svelte";
    import { link } from "$/shared/inertia/link";
    import { tw } from "$/shared/lib/styles";
    import { boolAttr } from "runed";

    import type { LinkParameters } from "$/shared/inertia/link";
    import type { ClassName } from "$/shared/lib/styles";

    type Props = LinkParameters & {
        class?: ClassName;
        completedAt: string | null;
    };

    const { class: classname, completedAt, ...options }: Props = $props();
</script>

<button
    {@attach link(() => ({
        ...options,
        only: ["todos"],
        showProgress: false
    }))}
    type="button"
    class={tw(
        "group flex size-5.5 shrink-0 items-center justify-center rounded-full border border-cream-950 text-ms data-completed:bg-cream-950 data-completed:text-cream-50",
        classname
    )}
    data-completed={boolAttr(completedAt)}
    onclick={() => navigator.vibrate?.(100)}
>
    <Check class="group-not-data-completed:hidden" />
</button>
