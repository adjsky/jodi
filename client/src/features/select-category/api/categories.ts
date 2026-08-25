import { queryOptions } from "@tanstack/svelte-query";
import ListCategories from "$/generated/actions/App/Domain/Todo/Actions/ListCategories";
import ky from "ky";

import type { CategoryData } from "$/entities/todo";

export const categoriesQueryOptions = queryOptions<CategoryData[]>({
    queryKey: ["categories"],
    queryFn({ signal }) {
        const { url, method } = ListCategories();

        return ky(url, { method, signal }).json();
    }
});
