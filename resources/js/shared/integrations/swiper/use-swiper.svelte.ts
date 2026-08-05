import { extract } from "runed";
import a11yCssUrl from "swiper/css/a11y?url";
import navigationCssUrl from "swiper/css/navigation?url";
import { A11y, Navigation } from "swiper/modules";

import type { MaybeGetter } from "runed";
import type { SwiperContainer } from "swiper/element";
import type { SwiperParams } from "swiper/types";

export function useSwiper(
    container: MaybeGetter<SwiperContainer | null>,
    options?: SwiperParams
): void {
    $effect(() => {
        const c = extract(container);

        if (c == null || c.swiper) {
            return;
        }

        Object.assign(c, {
            ...options,
            modules: options?.modules ?? [Navigation, A11y],
            injectStylesUrls: options?.injectStylesUrls ?? [
                navigationCssUrl,
                a11yCssUrl
            ]
        });

        c.initialize();

        return () => {
            c.swiper?.destroy();
        };
    });
}
