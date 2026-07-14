import { test, expect } from "@playwright/test";

test.describe("Register UI", () => {
  test("creates a new account", async ({ page }) => {
    const email = `user-${Date.now()}@bookshop.io`;

    await page.goto("/register");
    await page.getByTestId("register-email").fill(email);
    await page.getByTestId("register-password").fill("password123");
    await page.getByTestId("register-submit").click();

    await expect(page).toHaveURL("/");
    await expect(page.getByTestId("nav-user")).toContainText(email);
  });

  test("shows error for duplicate email", async ({ page }) => {
    const email = `dup-${Date.now()}@bookshop.io`;

    await page.goto("/register");
    await page.getByTestId("register-email").fill(email);
    await page.getByTestId("register-password").fill("password123");
    await page.getByTestId("register-submit").click();
    await expect(page).toHaveURL("/");

    await page.getByTestId("nav-logout").click();
    await page.goto("/register");
    await page.getByTestId("register-email").fill(email);
    await page.getByTestId("register-password").fill("password123");
    await page.getByTestId("register-submit").click();

    await expect(page.getByTestId("register-alert")).toBeVisible();
    await expect(page).toHaveURL(/\/register/);
  });
});
