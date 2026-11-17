import { use_swiper } from "./use-swiper.svelte";

import type { Getter } from "runed";
import type { Swiper } from "swiper";
import type { SwiperContainer } from "swiper/element";
import type { SwiperParams } from "swiper/types";

export type SlidingWindowSwiperOptions<T> = SwiperParams & {
    edge_buffer: number;
    on_slide_change: (offset: number) => void;
    get_data_at: (offset: number) => T;
};

export class SlidingWindowSwiper<T> {
    #container: Getter<SwiperContainer | null>;
    #options: SlidingWindowSwiperOptions<T>;

    constructor(container: Getter<SwiperContainer | null>, options: SlidingWindowSwiperOptions<T>) {
        this.#container = container;
        this.#options = options;

        this.#init();
    }

    get data(): T[] {
        const window_size = this.#options.edge_buffer * 2 + 1;

        return Array.from({ length: window_size }, (_, index) =>
            this.#options.get_data_at(index - this.#options.edge_buffer)
        );
    }

    #init() {
        use_swiper(this.#container, {
            ...this.#options,
            slidesPerView: 1,
            initialSlide: this.#options.edge_buffer,
            touchStartForcePreventDefault: true,
            on: {
                transitionEnd: (swiper) => this.#recenter(swiper)
            }
        });
    }

    #recenter(swiper: Swiper): void {
        const offset = swiper.activeIndex - this.#options.edge_buffer;
        if (offset === 0) return;

        const translate = swiper.getTranslate();
        const translate_offset =
            swiper.slidesGrid[swiper.activeIndex] - swiper.slidesGrid[this.#options.edge_buffer];

        this.#options.on_slide_change(offset);

        swiper.setTranslate(translate + translate_offset);
        swiper.updateActiveIndex(this.#options.edge_buffer);
        swiper.updateSlidesClasses();

        if (swiper.touchEventsData.isTouched) {
            swiper.touchEventsData.startTranslate = translate + translate_offset;
        }
    }
}
