import { fileURLToPath } from "node:url";

import { includeIgnoreFile } from "@eslint/compat";
import js from "@eslint/js";
import svelte from "eslint-plugin-svelte";
import { defineConfig } from "eslint/config";
import globals from "globals";
import ts from "typescript-eslint";

const gitignorePath = fileURLToPath(new URL("./.gitignore", import.meta.url));

export default defineConfig(
    includeIgnoreFile(gitignorePath),
    js.configs.recommended,
    ...ts.configs.recommended,
    ...svelte.configs.recommended,
    {
        languageOptions: {
            globals: {
                ...globals.browser
            },

            ecmaVersion: 2022,
            sourceType: "module",

            parserOptions: {
                projectService: true,
                tsconfigRootDir: import.meta.dirname
            }
        },

        rules: {
            "@typescript-eslint/no-import-type-side-effects": "error",
            "@typescript-eslint/no-floating-promises": "error",
            "@typescript-eslint/no-misused-promises": [
                "error",
                { checksVoidReturn: false }
            ],
            "@typescript-eslint/consistent-type-imports": [
                "error",
                { disallowTypeAnnotations: false }
            ],
            "@typescript-eslint/no-unused-vars": [
                "warn",
                {
                    varsIgnorePattern: "^_",
                    argsIgnorePattern: "^_",
                    caughtErrorsIgnorePattern: "^_"
                }
            ],

            "no-console": "error",
            "no-constant-condition": ["error", { checkLoops: false }],
            "no-undef": "off"
        }
    },
    {
        files: ["**/*.svelte", "**/*.svelte.ts", "**/*.svelte.js"],

        languageOptions: {
            parserOptions: {
                projectService: true,
                extraFileExtensions: [".svelte"],
                parser: ts.parser
            }
        },

        rules: {
            "svelte/no-target-blank": "error",
            "svelte/prefer-svelte-reactivity": "off"
        }
    },
    {
        files: ["**/*.js"],
        extends: [ts.configs.disableTypeChecked]
    }
);
