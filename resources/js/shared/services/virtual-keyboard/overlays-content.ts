import { PLATFORM } from "$/shared/cfg/constants";

export function overlaysContent(): boolean {
    return PLATFORM != "web";
}
