import { test as base, expect } from "@playwright/test";
import {
  LoginPage,
  RegisterPage,
  ShopPage,
  CartPage,
  OrdersPage,
} from "./pages";
import { DEMO_USER } from "./constants";

type Fixtures = {
  loginPage: LoginPage;
  registerPage: RegisterPage;
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
  registerPage: async ({ page }, use) => {
    await use(new RegisterPage(page));
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
  loggedInPage: async ({ loginPage }, use) => {
    await loginPage.login();
    await use();
  },
  cartWithItem: async ({ loginPage, shopPage }, use) => {
    await loginPage.login();
    await shopPage.goto();
    await shopPage.bookGrid.waitFor({ state: "visible" });
    await shopPage.addToCartButtons.first().waitFor({ state: "visible" });
    await shopPage.addFirstBookToCart();
    await use();
  },
});

export { expect, DEMO_USER };
