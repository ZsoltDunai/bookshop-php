import { defineConfig, devices } from "@playwright/test";

const BASE_URL = process.env.BASE_URL || "http://127.0.0.1:8080";

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
    command:
      process.platform === "win32"
        ? "php -S 127.0.0.1:8080 -t ../public ../router.php"
        : "php -S 127.0.0.1:8080 -t ../public ../router.php",
    url: `${BASE_URL}/health`,
    cwd: __dirname,
    reuseExistingServer: !process.env.CI,
    timeout: 120_000,
  },
});
