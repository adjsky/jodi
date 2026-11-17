import { onDestroy } from "svelte";

export class Timer {
    #seconds_left = $state(0);
    #interval: NodeJS.Timeout | undefined;

    constructor() {
        onDestroy(() => {
            clearInterval(this.#interval);
        });
    }

    get seconds_left(): number {
        return this.#seconds_left;
    }

    get is_running(): boolean {
        return this.#seconds_left > 0;
    }

    start(seconds: number): void {
        clearInterval(this.#interval);

        const end = performance.now() + seconds * 1000;
        this.#seconds_left = Math.ceil(seconds);

        this.#interval = setInterval(() => {
            this.#seconds_left = Math.ceil((end - performance.now()) / 1000);

            if (this.#seconds_left <= 0) {
                clearInterval(this.#interval);
            }
        }, 1000);
    }
}
