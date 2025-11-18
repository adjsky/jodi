<script lang="ts">
    import AppLayout from "$/app/ui/layouts/AppLayout.svelte";
    import { ProfileCalendar } from "$/widgets/profile-calendar";
    import { TodoList } from "$/widgets/todo-list";

    type Props = {
        todos: App.Data.TodoDto[];
    };

    const { todos }: Props = $props();
</script>

<AppLayout>
    <ProfileCalendar />
    <TodoList {todos} class="mt-3" />
</AppLayout>
