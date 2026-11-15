import { TodoRepository } from "./todo-repository.ts";

import type { TodoDraft } from "./types.ts";

async function create(draft: TodoDraft): Promise<string> {
    const id = await TodoRepository.create(draft);

    return id;
}

export const TodoService = {
    create
};
