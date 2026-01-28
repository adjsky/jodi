import { get } from "$/generated/actions/App/Http/Controllers/DaySummaryController";
import { useDebounce } from "runed";
import { SvelteMap } from "svelte/reactivity";

import type { DateValue } from "@internationalized/date";

type Options = {
    onError?: VoidFunction;
    onSuccess?: VoidFunction;
};

type PendingRequests = {
    year: number;
    months: Set<number>;
};

export function useDaySummary(options?: Options) {
    const { onError, onSuccess } = options ?? {};

    let pendingRequests: PendingRequests | null = null;
    let abortController: AbortController | null = null;

    const cache = new SvelteMap<
        number,
        SvelteMap<string, App.Data.DaySummaryDto>
    >();

    function request(date: DateValue) {
        if (pendingRequests && pendingRequests.year == date.year) {
            pendingRequests.months.add(date.month);
        } else {
            pendingRequests = {
                year: date.year,
                months: new Set([date.month])
            };
        }
        void _request(structuredClone(pendingRequests));
    }

    const _request = useDebounce(async (requests: PendingRequests) => {
        abortController?.abort();
        abortController = new AbortController();

        try {
            const { url, method } = get(
                { year: requests.year },
                { query: { m: [...requests.months].join(",") } }
            );

            const response = await fetch(url, {
                method,
                signal: abortController.signal
            });
            const json = (await response.json()) as Record<
                string,
                App.Data.DaySummaryDto
            >;

            const yearCache = cache.get(requests.year);

            if (yearCache) {
                for (const [day, s] of Object.entries(json)) {
                    yearCache.set(day, s);
                }
            } else {
                cache.set(requests.year, new SvelteMap(Object.entries(json)));
            }

            pendingRequests = null;
            onSuccess?.();
        } catch (e) {
            if (e instanceof DOMException && e.name === "AbortError") return;
            console.error(e);
            onError?.();
        }
    }, 50);

    return { cache, request };
}
