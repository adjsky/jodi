import { PLATFORM } from "#shared/cfg/constants.ts";

export function overlaysContent(): boolean {
    return PLATFORM != "web";
}
