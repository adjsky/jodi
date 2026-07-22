import { TIMEZONE, TIMEZONE_COOKIE } from "$/shared/cfg/constants";
import Cookies from "js-cookie";

if (Cookies.get(TIMEZONE_COOKIE) != TIMEZONE) {
    Cookies.set(TIMEZONE_COOKIE, TIMEZONE, {
        sameSite: "lax",
        expires: 365
    });
}
