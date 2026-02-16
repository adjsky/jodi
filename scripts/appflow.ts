#!/usr/bin/env node
import { generateGoogleServices } from "../resources/vite/google-services/plugin.ts";

await generateGoogleServices({ env: process.env });
