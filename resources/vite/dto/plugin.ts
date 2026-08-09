import { exec } from "node:child_process";
import { join } from "node:path";
import { promisify } from "node:util";

import pc from "picocolors";

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
                if (file.startsWith(join(server.config.root, "app/Domain"))) {
                    await generate.call(this);
                }
            }
        }
    ];
}

async function generate(this: MinimalPluginContextWithoutEnvironment) {
    try {
        const { stdout, stderr } = await execa(
            "php artisan typescript:transform",
            {
                timeout: 5_000
            }
        );

        const output = [pc.cyan(stdout.trim()), pc.red(stderr.trim())];

        this.info(
            ["Types generated for DTOs", ...output.filter(Boolean)].join("\n")
        );
    } catch (e) {
        this.warn(`Failed to generate DTO types: ${e}`);
    }
}
