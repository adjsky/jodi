<script lang="ts">
    import { raise } from "#shared/lib/exception/raise.ts";

    import type { Snippet } from "svelte";

    type Props = {
        disabled?: boolean;
        children?: Snippet;
    };

    let { disabled = false, children }: Props = $props();

    function portal(element: HTMLElement) {
        const target = document.querySelector("#portal-root");

        if (!target) {
            raise("Failed to locate portal root.");
        }

        const placeholder = document.createComment("portal");

        element.before(placeholder);
        target.append(element);

        return () => {
            if (placeholder.isConnected) {
                placeholder.replaceWith(element);
            } else {
                element.remove();
            }
        };
    }
</script>

{#if disabled}
    {@render children?.()}
{:else}
    <div class="contents" {@attach portal}>
        {@render children?.()}
    </div>
{/if}
