<script lang="ts">
    import BackButton from "$/shared/ui/BackButton.svelte";

    import type { Snippet } from "svelte";

    type Props = {
        title: string;
        onclose: VoidFunction;
        children: Snippet;
    };

    const { title, children, onclose }: Props = $props();
</script>

<div class="fixed inset-0 z-10 flex flex-col bg-cream-50 px-4 py-3">
    <div class="relative">
        <BackButton onclick={onclose} />
        <span
            class="absolute top-1/2 left-1/2 -translate-1/2 text-xl font-bold"
        >
            {title}
        </span>
    </div>
    <div class="flex-grow py-5">{@render children()}</div>
</div>
