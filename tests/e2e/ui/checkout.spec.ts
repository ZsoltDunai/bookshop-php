import { test, expect } from "@helpers/fixtures";

test.describe("Checkout UI", () => {
  test("completes purchase and shows order", async ({ page, cartWithItem, cartPage, ordersPage }) => {
    await cartPage.goto();
    await cartPage.checkout();
    await expect(page).toHaveURL(/\/orders/);
    await expect(ordersPage.list).toBeVisible();
    await expect(ordersPage.orderCards.first()).toBeVisible();
  });
});
