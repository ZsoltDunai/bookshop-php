import { test, expect } from "@helpers/fixtures";

test.describe("Register UI", () => {
  test("creates a new account", async ({ page, registerPage }) => {
    const email = `user-${Date.now()}@bookshop.io`;

    await registerPage.register(email);
    await expect(page).toHaveURL("/");
    await expect(registerPage.nav.userLabel).toContainText(email);
  });

  test("shows error for duplicate email", async ({ page, registerPage }) => {
    const email = `dup-${Date.now()}@bookshop.io`;

    await registerPage.register(email);
    await expect(page).toHaveURL("/");
    await expect(registerPage.nav.userLabel).toContainText(email);

    await registerPage.logout();
    await registerPage.register(email);
    await expect(registerPage.alert).toBeVisible();
    await expect(page).toHaveURL(/\/register/);
  });
});
