import js from "@eslint/js";
import globals from "globals";
import pluginVue from "eslint-plugin-vue";
import { defineConfig } from "eslint/config";

export default defineConfig({
  files: ["**/*.{js,mjs,cjs,vue}"],
  ignores: ["node_modules/**", "dist/**"],
  languageOptions: {
    ecmaVersion: 2024,
    sourceType: "module",
    globals: {
      ...globals.browser,
      defineProps: "readonly",
      defineEmits: "readonly",
      defineExpose: "readonly",
      withDefaults: "readonly",
    },
  },
  plugins: { vue: pluginVue },
  extends: [js.configs.recommended, pluginVue.configs["recommended"]],
  rules: {
    "no-console": process.env.NODE_ENV === "production" ? "warn" : "off",
    "no-debugger": process.env.NODE_ENV === "production" ? "warn" : "off",

    "vue/script-setup-uses-vars": "error",
    "vue/no-deprecated-slot-attribute": "off",

    "no-unused-vars": ["warn", { argsIgnorePattern: "^_" }],
  },
});
