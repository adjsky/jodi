<script lang="ts">
    type Props = {
        status: 404 | 500 | 503;
    };

    const { status }: Props = $props();
</script>

{status}
