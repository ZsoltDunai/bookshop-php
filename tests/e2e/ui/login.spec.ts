import { test, expect } from "@helpers/fixtures";
import { DEMO_USER } from "@helpers/constants";

test.describe("Login UI", () => {
  test("home page links to login", async ({ page, loginPage }) => {
    await loginPage.openFromHome();
    await expect(page).toHaveURL(/\/login/);
    await expect(loginPage.heading).toBeVisible();
  });

  test("successful login redirects home", async ({ page, loginPage, shopPage }) => {
    await loginPage.login();
    await expect(page).toHaveURL("/");
    await expect(shopPage.heading).toBeVisible();
    await expect(shopPage.bookGrid).toBeVisible();
    await expect(loginPage.nav.userLabel).toContainText(DEMO_USER.email);
  });

  test("invalid login shows error", async ({ page, loginPage }) => {
    await loginPage.login(DEMO_USER.email, "wrong-password");
    await expect(loginPage.alert).toBeVisible();
    await expect(page).toHaveURL(/\/login/);
  });
});
