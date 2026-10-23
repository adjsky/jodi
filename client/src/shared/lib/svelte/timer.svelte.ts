import { onDestroy } from "svelte";

export class Timer {
    #secondsLeft = $state(0);
    #interval: NodeJS.Timeout | undefined;

    constructor() {
        onDestroy(() => {
            clearInterval(this.#interval);
        });
    }

    get secondsLeft(): number {
        return this.#secondsLeft;
    }

    get isRunning(): boolean {
        return this.#secondsLeft > 0;
    }

    start(seconds: number): void {
        clearInterval(this.#interval);

        const end = performance.now() + seconds * 1000;
        this.#secondsLeft = Math.ceil(seconds);

        this.#interval = setInterval(() => {
            this.#secondsLeft = Math.ceil((end - performance.now()) / 1000);

            if (this.#secondsLeft <= 0) {
                clearInterval(this.#interval);
            }
        }, 1000);
    }
}
