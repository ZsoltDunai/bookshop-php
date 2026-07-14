import { Page, expect } from "@playwright/test";

export class OrdersPage {
  constructor(private readonly page: Page) {}

  async goto() {
    await this.page.goto("/orders");
  }

  async expectLoaded() {
    await expect(this.page.getByTestId("orders-heading")).toBeVisible();
  }

  async expectCheckoutSuccess() {
    await expect(this.page).toHaveURL(/\/orders/);
    await expect(this.page.getByTestId("orders-list")).toBeVisible();
    await expect(this.page.getByTestId("order-card").first()).toBeVisible();
  }
}
