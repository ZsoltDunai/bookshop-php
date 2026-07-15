import { Page, expect } from "@playwright/test";
import { NavBar } from "./nav.page";

export class OrdersPage {
  readonly nav: NavBar;

  constructor(private readonly page: Page) {
    this.nav = new NavBar(page);
  }

  async goto() {
    await this.page.goto("/orders");
  }

  async expectLoaded() {
    await expect(this.page.getByTestId("orders-heading")).toBeVisible();
  }

  async expectEmpty() {
    await expect(this.page.getByTestId("orders-empty")).toBeVisible();
  }

  async expectCheckoutSuccess() {
    await expect(this.page).toHaveURL(/\/orders/);
    await expect(this.page.getByTestId("orders-list")).toBeVisible();
    await expect(this.page.getByTestId("order-card").first()).toBeVisible();
  }
}
