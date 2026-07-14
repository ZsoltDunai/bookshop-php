import { test, expect } from "@helpers/fixtures";

test.describe("Cart UI", () => {
  test("redirects guests to login", async ({ page }) => {
    await page.goto("/cart");
    await expect(page).toHaveURL(/\/login/);
  });

  test("add to cart increases badge", async ({ page, shopPage }) => {
    const email = `cart-badge-${Date.now()}@bookshop.io`;

    await page.goto("/register");
    await page.getByTestId("register-email").fill(email);
    await page.getByTestId("register-password").fill("password123");
    await page.getByTestId("register-submit").click();
    await expect(page).toHaveURL("/");

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
