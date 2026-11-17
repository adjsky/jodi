import {
    APP_DEBUG,
    APP_ENV,
    APP_URL,
    FIREBASE_VAPID_KEY,
    FIREBASE_WEB_CONFIG
} from "$app/env/public";

export const APP_CONFIG = {
    url: APP_URL,
    env: APP_ENV,
    debug: APP_DEBUG,
    firebase: {
        config: FIREBASE_WEB_CONFIG,
        vapidKey: FIREBASE_VAPID_KEY
    }
};
