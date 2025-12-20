<script lang="ts">
    import { page } from "@inertiajs/svelte";
    import { AddTodoOrEvent } from "$/features/add-todo-or-event";
    import { m } from "$/paraglide/messages";
    import CalendarCat from "$/shared/assets/calendar-cat.svg";
    import { CalendarPreview } from "$/widgets/calendar";
    import EventList from "$/widgets/event-list/ui/EventList.svelte";
    import { TodoList } from "$/widgets/todo-list";

    type Props = {
        todos: App.Data.TodoDto[];
        events: App.Data.EventDto[];
    };

    const { todos, events }: Props = $props();

    const isLoadingEvents = $derived(events === undefined);
    const isLoadingTodos = $derived(todos === undefined);
</script>

<main class="pb-20">
    <CalendarPreview user={$page.props.auth.user} />

    {#if todos?.length === 0 && events?.length === 0}
        <img
            src={CalendarCat}
            width={300}
            height={231}
            alt=""
            loading="lazy"
            decoding="async"
            class="mx-auto mt-30 max-w-48"
        />
        <p class="mx-auto mt-8 max-w-3/4 text-center text-lg font-medium">
            {m["home.empty-day"]()}
        </p>
    {:else}
        <EventList {events} loading={isLoadingEvents} class="mt-4" />
        <TodoList {todos} loading={isLoadingTodos} class="mt-4" />
    {/if}

    <AddTodoOrEvent loading={isLoadingEvents || isLoadingTodos} />
</main>
