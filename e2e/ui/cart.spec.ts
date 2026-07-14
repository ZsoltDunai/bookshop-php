import { test, expect } from "@helpers/fixtures";

test.describe("Cart UI", () => {
  test("redirects guests to login", async ({ page }) => {
    await page.goto("/cart");
    await expect(page).toHaveURL(/\/login/);
  });

  test("add to cart increases badge", async ({ loggedInPage, shopPage, page }) => {
    await shopPage.goto();
    await shopPage.addFirstBookToCart();
    await expect(page.getByTestId("nav-cart-count")).toHaveText("1");
  });

  test("cart shows added item", async ({ cartWithItem, cartPage, page }) => {
    await cartPage.goto();
    await cartPage.expectHasItems();
    await expect(page.getByTestId("checkout-btn")).toBeVisible();
  });
});
