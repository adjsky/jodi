#!/usr/bin/env node
import { generateGoogleServices } from "../resources/vite/firebase-messaging/plugin.ts";

await generateGoogleServices.call(console as never, { env: process.env });
