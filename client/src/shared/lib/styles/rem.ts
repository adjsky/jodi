export function rem(value: number): number {
    const font_size = Number.parseFloat(getComputedStyle(document.documentElement).fontSize);

    return font_size * value;
}
