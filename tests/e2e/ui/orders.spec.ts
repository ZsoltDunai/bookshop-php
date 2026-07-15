import { test, expect } from "@helpers/fixtures";

test.describe("Orders UI", () => {
  test("shows empty state for new user", async ({ page, registerPage, ordersPage }) => {
    const email = `orders-empty-${Date.now()}@bookshop.io`;

    await registerPage.register(email);
    await expect(page).toHaveURL("/");
    await expect(registerPage.nav.userLabel).toContainText(email);

    await ordersPage.goto();
    await expect(ordersPage.emptyState).toBeVisible();
  });

  test("shows order after checkout", async ({ page, cartWithItem, cartPage, ordersPage }) => {
    await cartPage.goto();
    await cartPage.checkout();
    await expect(page).toHaveURL(/\/orders/);
    await expect(ordersPage.list).toBeVisible();
    await expect(ordersPage.orderCards.first()).toBeVisible();
  });
});
