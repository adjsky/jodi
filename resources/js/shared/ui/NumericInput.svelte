<script lang="ts">
    import { tw } from "../lib/styles";

    type Props = {
        value: string;
        min?: number;
        class?: string;
    };

    let { value = $bindable(), min, ...props }: Props = $props();

    function setter(v: string) {
        const tv = v.trim();

        if (tv == "") {
            value = tv;
            return;
        }

        const ntv = Number(tv);

        if (isNaN(ntv) || tv.length > 3 || tv.includes(".")) {
            return;
        }

        if (min !== undefined && ntv < min) {
            value = min.toString();
        } else {
            value = tv;
        }
    }
</script>

<input
    bind:value={() => value, setter}
    inputMode="numeric"
    class={tw(
        "form-input h-13.75 w-full rounded-xl border-none bg-cream-500/10 px-4 text-lg font-medium outline-none placeholder:text-cream-600 focus:ring-0",
        props.class
    )}
    autocomplete="off"
/>
