export function prefersLightText(hex: string, threshold = 0.5) {
    const cleanHex = hex.startsWith("#") ? hex.slice(1) : hex;

    const r = parseInt(cleanHex.substring(0, 2), 16);
    const g = parseInt(cleanHex.substring(2, 4), 16);
    const b = parseInt(cleanHex.substring(4, 6), 16);

    const rNorm = r / 255;
    const gNorm = g / 255;
    const bNorm = b / 255;

    const luminance = 0.2126 * rNorm + 0.7152 * gNorm + 0.0722 * bNorm;

    return luminance < threshold;
}
