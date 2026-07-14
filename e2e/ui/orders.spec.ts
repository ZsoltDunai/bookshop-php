import { test, expect } from "@helpers/fixtures";

test.describe("Orders UI", () => {
  test("shows empty state for new user", async ({ loggedInPage, ordersPage, page }) => {
    await ordersPage.goto();
    await expect(page.getByTestId("orders-empty")).toBeVisible();
  });

  test("shows order after checkout", async ({ cartWithItem, cartPage, ordersPage }) => {
    await cartPage.goto();
    await cartPage.checkout();
    await ordersPage.expectCheckoutSuccess();
  });
});
