export function rem(value: number): number {
    const fontSize = Number.parseFloat(
        getComputedStyle(document.documentElement).fontSize
    );

    return fontSize * value;
}
