import { getLocale } from "$/paraglide/runtime";
import { SlidingWindowSwiper } from "$/shared/integrations/swiper";
import { rem } from "$/shared/lib/styles/rem";
import { extract } from "runed";

import { Week } from "./week.svelte";

import type { CalendarDate } from "@internationalized/date";
import type { WeekStart } from "$/shared/lib/date/types";
import type { Getter, MaybeGetter } from "runed";
import type { SwiperContainer } from "swiper/element";

type Options = {
    cursor: CalendarDate;
    weekStart: WeekStart;
    onCursorChange: (date: CalendarDate) => void;
};

export class WeekSwiper {
    #swiper: SlidingWindowSwiper<CalendarDate[]>;

    constructor(
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

        this.#swiper = new SlidingWindowSwiper(container, {
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
    }

    get weeks(): CalendarDate[][] {
        return this.#swiper.data;
    }
}
