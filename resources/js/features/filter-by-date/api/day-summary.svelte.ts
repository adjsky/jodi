import { daySummary } from "$/generated/routes";
import { useDebounce } from "runed";
import { SvelteMap } from "svelte/reactivity";

import type { CalendarDate } from "@internationalized/date";

export const summaryCache = new SvelteMap<
    number,
    SvelteMap<string, App.Data.DaySummaryDto>
>();

let pendingRequests = $state<{ year: number; months: Set<number> } | null>(
    null
);

const _requestSummary = useDebounce(async () => {
    if (!pendingRequests) {
        return;
    }

    const { url, method } = daySummary(
        { year: pendingRequests.year },
        { query: { m: [...pendingRequests.months].join(",") } }
    );

    const response = await fetch(url, { method });
    const json = (await response.json()) as Record<
        string,
        App.Data.DaySummaryDto
    >;

    const monthsSummaryCache = summaryCache.get(pendingRequests.year);

    if (monthsSummaryCache) {
        for (const [day, s] of Object.entries(json)) {
            monthsSummaryCache.set(day, s);
        }
    } else {
        summaryCache.set(
            pendingRequests.year,
            new SvelteMap(Object.entries(json))
        );
    }

    pendingRequests = null;
}, 50);

export async function requestSummary(date: CalendarDate) {
    if (pendingRequests && pendingRequests.year == date.year) {
        pendingRequests.months.add(date.month);
    } else {
        pendingRequests = {
            year: date.year,
            months: new Set([date.month])
        };
    }
    void _requestSummary();
}
