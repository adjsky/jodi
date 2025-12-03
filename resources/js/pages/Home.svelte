<script lang="ts">
    import { page } from "@inertiajs/svelte";
    import AppLayout from "$/app/ui/layouts/AppLayout.svelte";
    import { TodoList } from "$/widgets/todo-list";
    import { UserCalendarHeader } from "$/widgets/user-calendar";

    type Props = {
        todos: App.Data.TodoDto[];
    };

    const { todos }: Props = $props();
</script>

<AppLayout class="pt-2 pb-8">
    <UserCalendarHeader user={$page.props.auth.user} />
    <TodoList {todos} loading={todos == undefined} class="mt-3" />
</AppLayout>
