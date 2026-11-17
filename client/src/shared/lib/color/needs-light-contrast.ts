export function needs_light_contrast(hex: string, threshold = 0.65): boolean {
    const clean_hex = hex.startsWith("#") ? hex.slice(1) : hex;

    const r = parseInt(clean_hex.substring(0, 2), 16);
    const g = parseInt(clean_hex.substring(2, 4), 16);
    const b = parseInt(clean_hex.substring(4, 6), 16);

    const r_norm = r / 255;
    const g_norm = g / 255;
    const b_norm = b / 255;

    const luminance = 0.2126 * r_norm + 0.7152 * g_norm + 0.0722 * b_norm;

    return luminance < threshold;
}
