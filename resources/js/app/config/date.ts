import { TIMEZONE, TIMEZONE_COOKIE } from "$/shared/cfg/constants";
import * as Cookie from "$/shared/lib/cookie";

if (Cookie.get(TIMEZONE_COOKIE) != TIMEZONE) {
    Cookie.set(TIMEZONE_COOKIE, TIMEZONE, {
        maxAge: 34560000,
        sameSite: "lax"
    });
}
