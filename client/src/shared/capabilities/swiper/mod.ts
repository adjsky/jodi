import { register } from "swiper/element";

export const Swiper = {
    init(): void {
        register();
    }
};

export * from "./sliding-window-swiper.svelte";
export * from "./use-swiper.svelte";
