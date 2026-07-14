import { Page, expect } from "@playwright/test";

export class CartPage {
  constructor(private readonly page: Page) {}

  async goto() {
    await this.page.goto("/cart");
  }

  async expectLoaded() {
    await expect(this.page.getByTestId("cart-heading")).toBeVisible();
  }

  async expectHasItems() {
    await expect(this.page.getByTestId("cart-layout")).toBeVisible();
    await expect(this.page.getByTestId("cart-item").first()).toBeVisible();
  }

  async checkout() {
    await this.page.getByTestId("checkout-btn").click();
  }
}
