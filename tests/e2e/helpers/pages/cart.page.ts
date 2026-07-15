import { Page, expect } from "@playwright/test";
import { NavBar } from "./nav.page";

export class CartPage {
  readonly nav: NavBar;

  constructor(private readonly page: Page) {
    this.nav = new NavBar(page);
  }

  async goto() {
    await this.page.goto("/cart");
  }

  async expectLoaded() {
    await expect(this.page.getByTestId("cart-heading")).toBeVisible();
  }

  async expectRedirectedToLogin() {
    await expect(this.page).toHaveURL(/\/login/);
  }

  async expectHasItems() {
    await expect(this.page.getByTestId("cart-layout")).toBeVisible();
    await expect(this.page.getByTestId("cart-item").first()).toBeVisible();
  }

  async expectCheckoutVisible() {
    await expect(this.page.getByTestId("checkout-btn")).toBeVisible();
  }

  async expectEmpty() {
    await expect(this.page.getByTestId("cart-empty")).toBeVisible();
  }

  async checkout() {
    await this.page.getByTestId("checkout-btn").click();
  }
}
