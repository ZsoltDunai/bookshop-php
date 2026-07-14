import { test, expect } from "@helpers/fixtures";

test.describe("Login UI", () => {
  test("home page links to login", async ({ page }) => {
    await page.goto("/");
    await page.getByTestId("nav-login").click();
    await expect(page).toHaveURL(/\/login/);
    await expect(page.getByTestId("login-heading")).toBeVisible();
  });

  test("successful login redirects home", async ({ page, loginPage, shopPage }) => {
    await loginPage.login();
    await expect(page).toHaveURL("/");
    await shopPage.expectLoaded();
    await expect(page.getByTestId("nav-user")).toContainText("demo@bookshop.io");
  });

  test("invalid login shows error", async ({ loginPage }) => {
    await loginPage.login("demo@bookshop.io", "wrong-password");
    await loginPage.expectLoginError();
  });
});
