export function normalizeIsoString(iso8601: string): string {
    const [date, tzTime] = iso8601.split("T");
    const [time, _] = tzTime.split(".");

    return `${date}T${time}+00:00`;
}
