import { test, expect } from "@helpers/fixtures";

test.describe("Orders UI", () => {
  test("shows empty state for new user", async ({ page }) => {
    const email = `orders-empty-${Date.now()}@bookshop.io`;

    await page.goto("/register");
    await page.getByTestId("register-email").fill(email);
    await page.getByTestId("register-password").fill("password123");
    await page.getByTestId("register-submit").click();
    await expect(page).toHaveURL("/");

    await page.goto("/orders");
    await expect(page.getByTestId("orders-empty")).toBeVisible();
  });

  test("shows order after checkout", async ({ cartWithItem, cartPage, ordersPage }) => {
    await cartPage.goto();
    await cartPage.checkout();
    await ordersPage.expectCheckoutSuccess();
  });
});
