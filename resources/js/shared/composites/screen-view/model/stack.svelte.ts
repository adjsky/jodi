import { onMount } from "svelte";

class Stack {
    #layers: string[] = $state.raw([]);

    register(id: string): void {
        onMount(() => {
            this.#layers = [...this.#layers, id];

            return () => {
                this.#layers = this.#layers.filter((layerId) => layerId != id);
            };
        });
    }

    indexOf(id: string): number {
        return this.#layers.indexOf(id);
    }

    isTop(id: string): boolean {
        const layerIndex = this.indexOf(id);

        return layerIndex >= 0 && layerIndex == this.#layers.length - 1;
    }
}

export const stack = new Stack();
