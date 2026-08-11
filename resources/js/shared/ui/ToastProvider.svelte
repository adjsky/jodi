<script lang="ts">
    import { Toaster } from "svelte-sonner";

    import { useFlashToaster } from "../integrations/inertia";

    useFlashToaster();
</script>

<Toaster />
