import { m } from "#paraglide/messages.js";
import { getLocale } from "#paraglide/runtime.js";
import { AuthTokenStorage } from "#shared/capabilities/auth-token-storage/mod.ts";
import { LocalSession } from "#shared/capabilities/local-session/mod.ts";
import { DEVICE_ID, PLATFORM, TIMEZONE } from "#shared/cfg/constants.ts";
import Cookies from "js-cookie";
import ky, { isHTTPError, isNetworkError } from "ky";

import { toaster } from "../ui/toaster.ts";

let csrfRefresh: Promise<unknown> | null = null;

export const api = ky.create({
    baseUrl: "/api/",
    hooks: {
        beforeRequest: [
            ({ request }) => {
                request.headers.set("Accept", "application/json");
                request.headers.set("Device-Id", DEVICE_ID);
                request.headers.set("Time-Zone", TIMEZONE);
                request.headers.set("Accept-Language", getLocale());
            },
            ({ request }) => {
                if (PLATFORM !== "web") {
                    return;
                }

                const xsrfToken = Cookies.get("XSRF-TOKEN");

                if (xsrfToken) {
                    request.headers.set("X-XSRF-TOKEN", xsrfToken);
                }
            },
            async ({ request }) => {
                if (PLATFORM === "web") {
                    return;
                }

                const authToken = await AuthTokenStorage.get();
                if (!authToken) return;

                request.headers.set("Authorization", `Bearer ${authToken}`);
            }
        ],
        afterResponse: [
            async ({ request, response, retryCount }) => {
                if (PLATFORM !== "web") {
                    return;
                }

                if (response.status !== 419 || retryCount > 0) {
                    return;
                }

                csrfRefresh ??= ky
                    .get("/api/sanctum/csrf-cookie")
                    .finally(() => {
                        csrfRefresh = null;
                    });

                await csrfRefresh;

                const xsrfToken = Cookies.get("XSRF-TOKEN");

                if (!xsrfToken) {
                    console.warn(
                        "No XSRF-TOKEN cookie found after calling `sanctum/csrf-cookie` endpoint."
                    );

                    return;
                }

                request.headers.set("X-XSRF-TOKEN", xsrfToken);

                return ky.retry({ request, delay: 0 });
            }
        ],
        beforeError: [
            async ({ error }) => {
                if (isHTTPError(error)) {
                    const { response } = error;

                    switch (response.status) {
                        case 401: {
                            const success = await LocalSession.end();

                            if (success) {
                                toaster.info(m["common.errors.unauthorized"]());
                            }

                            break;
                        }

                        case 403: {
                            toaster.error(m["common.errors.permission"]());
                            break;
                        }

                        case 429: {
                            toaster.error(
                                m["common.errors.too-many-requests"]()
                            );
                            break;
                        }
                    }
                } else if (isNetworkError(error)) {
                    toaster.error(m["common.errors.internet"]());
                }

                return error;
            }
        ]
    }
});
