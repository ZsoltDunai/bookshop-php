import { defineConfig, devices } from "@playwright/test";
import path from "path";

const BASE_URL = process.env.BASE_URL || "http://127.0.0.1:8080";
const E2E_DB = path.join(__dirname, "../data/bookshop-e2e.sqlite");
const startCommand =
  process.platform === "win32"
    ? `if exist "${E2E_DB}" del /f /q "${E2E_DB}" & php -S 127.0.0.1:8080 -t ../public ../router.php`
    : `rm -f "${E2E_DB}" && php -S 127.0.0.1:8080 -t ../public ../router.php`;

export default defineConfig({
  testDir: ".",
  fullyParallel: true,
  forbidOnly: !!process.env.CI,
  retries: process.env.CI ? 2 : 0,
  workers: process.env.CI ? 1 : undefined,
  reporter: [["list"], ["html", { open: "never" }]],
  use: {
    baseURL: BASE_URL,
    trace: "on-first-retry",
  },
  projects: [
    {
      name: "http",
      testMatch: /http\/.*\.spec\.ts/,
    },
    {
      name: "ui",
      testMatch: /ui\/.*\.spec\.ts/,
      use: { ...devices["Desktop Chrome"] },
    },
  ],
  webServer: {
    command: startCommand,
    url: `${BASE_URL}/health`,
    cwd: __dirname,
    reuseExistingServer: !process.env.CI,
    timeout: 120_000,
    env: {
      BOOKSHOP_DB: `sqlite:${E2E_DB}`,
    },
  },
});
