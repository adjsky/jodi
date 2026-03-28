<script lang="ts">
    import { watch } from "runed";

    import { tw } from "../lib/styles";

    type Props = {
        value: string;
        min?: number;
        max?: number;
        class?: string;
    };

    let {
        value = $bindable(),
        min = Number.MIN_SAFE_INTEGER,
        max = Number.MAX_SAFE_INTEGER,
        ...props
    }: Props = $props();

    function setter(v: string) {
        const tv = v.trim();

        if (tv == "") {
            value = tv;
            return;
        }

        const ntv = Number(tv);

        if (isNaN(ntv) || tv.includes(".")) {
            return;
        }

        if (ntv < min) {
            value = min.toString();
        } else if (ntv > max) {
            value = max.toString();
        } else {
            value = tv;
        }
    }

    watch(
        () => [max, min],
        () => {
            setter(value);
        }
    );
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
