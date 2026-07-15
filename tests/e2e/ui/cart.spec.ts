import { test } from "@helpers/fixtures";

test.describe("Cart UI", () => {
  test("redirects guests to login", async ({ cartPage }) => {
    await cartPage.goto();
    await cartPage.expectRedirectedToLogin();
  });

  test("add to cart increases badge", async ({ registerPage, shopPage }) => {
    const email = `cart-badge-${Date.now()}@bookshop.io`;

    await registerPage.register(email);
    await registerPage.expectRegisteredAs(email);

    await shopPage.goto();
    await shopPage.addFirstBookToCart();
    await shopPage.expectCartBadge(1);
  });

  test("cart shows added item", async ({ cartWithItem, cartPage }) => {
    await cartPage.goto();
    await cartPage.expectHasItems();
    await cartPage.expectCheckoutVisible();
  });
});
