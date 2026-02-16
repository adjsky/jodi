import { exec } from "node:child_process";
import { join } from "node:path";
import { promisify } from "node:util";

import type { MinimalPluginContextWithoutEnvironment, Plugin } from "vite";

const execa = promisify(exec);

export function dto(): Plugin[] {
    return [
        {
            name: "vite-plugin-dto:generate",
            enforce: "pre",
            buildStart() {
                return generate.call(this);
            },
            async handleHotUpdate({ file, server }) {
                if (file.startsWith(join(server.config.root, "app/Data"))) {
                    await generate.call(this);
                }
            }
        }
    ];
}

async function generate(this: MinimalPluginContextWithoutEnvironment) {
    try {
        await execa("php artisan typescript:transform", {
            timeout: 5_000
        });
        this.info("Types generated for DTOs");
    } catch (e) {
        this.warn(`Failed to generate DTO types: ${e}`);
    }
}
