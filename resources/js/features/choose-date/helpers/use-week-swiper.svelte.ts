import { getLocale } from "$/paraglide/runtime";
import { Week } from "$/shared/lib/date/week.svelte";
import { rem } from "$/shared/lib/dom/rem";
import { useSlidingWindowSwiper } from "$/shared/lib/swiper/use-sliding-window-swiper.svelte";
import { extract } from "runed";

import type { CalendarDate } from "@internationalized/date";
import type { WeekStart } from "$/shared/lib/types";
import type { Getter, MaybeGetter } from "runed";
import type { SwiperContainer } from "swiper/element";

type Options = {
    cursor: CalendarDate;
    weekStart: WeekStart;
    onCursorChange: (date: CalendarDate) => void;
};

export function useWeekSwiper(
    container: Getter<SwiperContainer | null>,
    options: MaybeGetter<Options>
) {
    const week = new Week(
        () => extract(options).cursor,
        () => ({
            weekStart: extract(options).weekStart,
            locale: getLocale()
        })
    );

    const swiper = useSlidingWindowSwiper(container, {
        slidesOffsetBefore: rem(0.5),
        slidesOffsetAfter: rem(0.5),
        spaceBetween: rem(0.5),
        edgeBuffer: 2,
        onSlideChange(offset) {
            const { cursor, onCursorChange } = extract(options);
            onCursorChange(cursor.add({ weeks: offset }));
        },
        getDataAt(offset) {
            return week.at(offset);
        }
    });

    return {
        get weeks() {
            return swiper.data;
        }
    };
}
