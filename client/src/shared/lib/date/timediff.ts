import type { TimeDuration } from "@internationalized/date";

type TimeLike = {
    hour: number;
    minute: number;
    second: number;
    millisecond: number;
};

export function timediff(a: TimeLike, b: TimeLike): TimeDuration {
    return {
        hours: b.hour - a.hour,
        minutes: b.minute - a.minute,
        seconds: b.second - a.second,
        milliseconds: b.millisecond - a.millisecond
    };
}
