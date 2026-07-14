import { test as base, expect } from "@playwright/test";
import { LoginPage } from "./pages/login.page";
import { ShopPage } from "./pages/shop.page";
import { CartPage } from "./pages/cart.page";
import { OrdersPage } from "./pages/orders.page";
import { DEMO_USER } from "./constants";

type Fixtures = {
  loginPage: LoginPage;
  shopPage: ShopPage;
  cartPage: CartPage;
  ordersPage: OrdersPage;
  loggedInPage: void;
  cartWithItem: void;
};

export const test = base.extend<Fixtures>({
  loginPage: async ({ page }, use) => {
    await use(new LoginPage(page));
  },
  shopPage: async ({ page }, use) => {
    await use(new ShopPage(page));
  },
  cartPage: async ({ page }, use) => {
    await use(new CartPage(page));
  },
  ordersPage: async ({ page }, use) => {
    await use(new OrdersPage(page));
  },
  loggedInPage: async ({ page, loginPage }, use) => {
    await loginPage.login();
    await use();
  },
  cartWithItem: async ({ page, loginPage, shopPage }, use) => {
    await loginPage.login();
    await shopPage.goto();
    await shopPage.addFirstBookToCart();
    await use();
  },
});

export { expect, DEMO_USER };
