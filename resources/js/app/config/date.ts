import { TIMEZONE, TIMEZONE_COOKIE } from "$/shared/cfg/constants";
import * as Cookie from "$/shared/lib/cookie";
import dayjs from "dayjs";
import utc from "dayjs/plugin/utc";

dayjs.extend(utc);

if (Cookie.get(TIMEZONE_COOKIE) != TIMEZONE) {
    Cookie.set(TIMEZONE_COOKIE, TIMEZONE, {
        maxAge: 34560000,
        sameSite: "lax"
    });
}
