/// <reference lib="webworker" />

import { getMessaging } from "firebase/messaging/sw";
import { cleanupOutdatedCaches, precacheAndRoute } from "workbox-precaching";

import * as Firebase from "../shared/lib/firebase";

declare let self: ServiceWorkerGlobalScope;

precacheAndRoute(self.__WB_MANIFEST);
cleanupOutdatedCaches();

self.addEventListener("message", (e) => {
    if (e.data?.type == "SKIP_WAITING") {
        void self.skipWaiting();
    }
});

const firebase = Firebase.create();
getMessaging(firebase);
