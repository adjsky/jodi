import { flushSync } from "svelte";

import { useSwiper } from "./use-swiper.svelte";

import type { Getter } from "runed";
import type { SwiperContainer } from "swiper/element";
import type { SwiperParams } from "swiper/types";

type Options<T> = SwiperParams & {
    edgeBuffer: number;
    onSlideChange: (offset: number) => void;
    getDataAt: (offset: number) => T;
};

export class SlidingWindowSwiper<T> {
    #container: Getter<SwiperContainer | null>;
    #options: Options<T>;

    constructor(
        container: Getter<SwiperContainer | null>,
        options: Options<T>
    ) {
        this.#container = container;
        this.#options = options;

        this.#init();
    }

    get data(): T[] {
        const windowSize = this.#options.edgeBuffer * 2 + 1;

        return Array.from({ length: windowSize }, (_, index) =>
            this.#options.getDataAt(index - this.#options.edgeBuffer)
        );
    }

    #init() {
        useSwiper(this.#container, {
            ...this.#options,
            slidesPerView: 1,
            initialSlide: this.#options.edgeBuffer,
            touchStartForcePreventDefault: true,
            on: {
                slideChangeTransitionEnd: (swiper) => {
                    const offset =
                        swiper.activeIndex - this.#options.edgeBuffer;
                    if (offset === 0) return;

                    this.#options.onSlideChange(offset);

                    flushSync();

                    swiper.update();
                    swiper.slideTo(this.#options.edgeBuffer, 0, false);
                }
            }
        });
    }
}
