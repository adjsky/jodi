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

export function useSlidingWindowSwiper<T>(
    container: Getter<SwiperContainer | null>,
    { edgeBuffer, onSlideChange, getDataAt, ...swiperParams }: Options<T>
) {
    const windowSize = edgeBuffer * 2 + 1;
    const centerIndex = edgeBuffer;

    const data = $derived(
        Array.from({ length: windowSize }, (_, index) =>
            getDataAt(index - centerIndex)
        )
    );

    useSwiper(container, {
        ...swiperParams,
        slidesPerView: 1,
        initialSlide: centerIndex,
        touchStartForcePreventDefault: true,
        on: {
            slideChangeTransitionEnd(swiper) {
                const offset = swiper.activeIndex - centerIndex;
                if (offset === 0) return;

                onSlideChange(offset);

                flushSync();

                swiper.update();
                swiper.slideTo(centerIndex, 0, false);
            }
        }
    });

    return {
        get data() {
            return data;
        }
    };
}
