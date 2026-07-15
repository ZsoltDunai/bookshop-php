import { test } from "@helpers/fixtures";

test.describe("Checkout UI", () => {
  test("completes purchase and shows order", async ({ cartWithItem, cartPage, ordersPage }) => {
    await cartPage.goto();
    await cartPage.checkout();
    await ordersPage.expectCheckoutSuccess();
  });
});
