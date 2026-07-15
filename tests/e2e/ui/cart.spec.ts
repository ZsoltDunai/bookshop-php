import { test, expect } from "@helpers/fixtures";

test.describe("Cart UI", () => {
  test("redirects guests to login", async ({ page, cartPage }) => {
    await cartPage.goto();
    await expect(page).toHaveURL(/\/login/);
  });

  test("add to cart increases badge", async ({ page, registerPage, shopPage }) => {
    const email = `cart-badge-${Date.now()}@bookshop.io`;

    await registerPage.register(email);
    await expect(page).toHaveURL("/");
    await expect(registerPage.nav.userLabel).toContainText(email);

    await shopPage.goto();
    await shopPage.addFirstBookToCart();
    await expect(shopPage.nav.cartCountBadge).toHaveText("1");
  });

  test("cart shows added item", async ({ cartWithItem, cartPage }) => {
    await cartPage.goto();
    await expect(cartPage.layout).toBeVisible();
    await expect(cartPage.items.first()).toBeVisible();
    await expect(cartPage.checkoutButton).toBeVisible();
  });
});
