#!/usr/bin/env node
import fs from "node:fs";

import { generateGoogleServices } from "../resources/vite/google-services/plugin.ts";

const json = await generateGoogleServices({ env: process.env });

console.info(json);

console.info(fs.readFileSync("android/app/google-services.json"));
