<script lang="ts">
    type Props = {
        name: string;
    };

    const { name }: Props = $props();
</script>

<button class="size-8 rounded-full bg-[#C2F4E9] outline outline-cream-950">
    {name[0].toLocaleUpperCase()}
</button>
