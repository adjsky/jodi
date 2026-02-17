<script lang="ts">
    import { usePoll } from "@inertiajs/svelte";
    import { AddTodoOrEvent } from "$/features/add-todo-or-event";
    import { m } from "$/paraglide/messages";
    import CalendarCat from "$/shared/assets/calendar-cat.svg";
    import ActionBanner from "$/shared/ui/ActionBanner.svelte";
    import { CalendarPreview } from "$/widgets/calendar";
    import { CurrentUser } from "$/widgets/current-user";
    import { EventList } from "$/widgets/event-list/";
    import { TodoList } from "$/widgets/todo-list";

    type Props = {
        todos: App.Data.TodoDto[];
        events: App.Data.EventDto[];
    };

    const { todos, events }: Props = $props();

    const hasNoEventsAndTodos = $derived(
        todos?.length === 0 && events?.length === 0
    );

    usePoll(30_000, {}, { keepAlive: false });
</script>

<ActionBanner />

<main class="pb-20">
    <CalendarPreview>
        <CurrentUser />
    </CalendarPreview>

    {#if hasNoEventsAndTodos}
        <img
            src={CalendarCat}
            width={300}
            height={231}
            alt=""
            loading="lazy"
            decoding="async"
            class="mx-auto mt-[15vh] max-w-48"
        />
        <p class="mx-auto mt-8 max-w-74 text-center text-lg font-medium">
            {m["home.empty-day"]()}
        </p>
    {/if}

    <EventList {events} class="mt-4" hidden={hasNoEventsAndTodos} />
    <TodoList {todos} class="mt-4" hidden={hasNoEventsAndTodos} />

    <AddTodoOrEvent />
</main>
