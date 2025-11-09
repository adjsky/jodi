<script lang="ts">
    import { Calendar } from "../helpers/calendar.svelte";
    import Carousel from "./Carousel.svelte";
    import Header from "./Header.svelte";

    const calendar = new Calendar();
</script>

<Header {calendar} />
<Carousel {calendar} />
