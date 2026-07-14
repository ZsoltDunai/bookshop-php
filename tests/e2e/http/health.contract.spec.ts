import { test, expect } from "@playwright/test";
import { z } from "zod";
import { TIMEOUTS } from "@helpers/constants";

const healthSchema = z.object({
  status: z.literal("ok"),
  app: z.literal("bookshop-php"),
});

test.describe("HTTP contract", () => {
  test("health endpoint matches schema", async ({ request }) => {
    const response = await request.get("/health");
    expect(response.status()).toBe(200);
    expect(response.headers()["content-type"]).toContain("application/json");

    const body = healthSchema.parse(await response.json());
    expect(body.status).toBe("ok");
  });

  test("health endpoint is fast", async ({ request }) => {
    const start = Date.now();
    const response = await request.get("/health");
    const elapsed = Date.now() - start;

    expect(response.ok()).toBeTruthy();
    expect(elapsed).toBeLessThan(TIMEOUTS.health);
  });
});
