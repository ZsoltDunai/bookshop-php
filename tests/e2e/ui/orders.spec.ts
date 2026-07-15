import { test } from "@helpers/fixtures";

test.describe("Orders UI", () => {
  test("shows empty state for new user", async ({ registerPage, ordersPage }) => {
    const email = `orders-empty-${Date.now()}@bookshop.io`;

    await registerPage.register(email);
    await registerPage.expectRegisteredAs(email);

    await ordersPage.goto();
    await ordersPage.expectEmpty();
  });

  test("shows order after checkout", async ({ cartWithItem, cartPage, ordersPage }) => {
    await cartPage.goto();
    await cartPage.checkout();
    await ordersPage.expectCheckoutSuccess();
  });
});
